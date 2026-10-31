<?php

namespace App\Models;

use App\Enums\StockState;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * A single item of a rental agreement: stock lots of RENTED goods point here to know their rates and fees.
 */
class RentalAgreementItem extends Model
{
    /** @use HasFactory<\Database\Factories\RentalAgreementItemFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'rental_agreement_id',
        'item_id',
        'daily_rate_per_unit',
        'daily_late_fee_per_unit',
        'non_return_fee',
        'qta',
    ];

    public function rentalAgreement(): BelongsTo
    {
        return $this->belongsTo(RentalAgreement::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }


    public function stockLots(): HasMany
    {
        return $this->hasMany(StockLot::class);
    }

    public function stockMovements(): HasManyThrough
    {
        return $this->hasManyThrough(StockMovement::class, StockLot::class);
    }

    public function stockBalances(): HasManyThrough
    {
        return $this->hasManyThrough(StockBalance::class, StockLot::class);
    }

    /**
     * The penalties of the item: the fee for the goods never given back and the fee for the late days.
     */
    protected function totalPenalty(): Attribute
    {
        return Attribute::get(fn () => $this->penalty_for_lost + $this->penalty_for_delay);
    }

    /**
     * Goods lost (or consumed) will never be given back: non_return_fee for each unit.
     */
    protected function penaltyForLost(): Attribute
    {
        return Attribute::get(fn () => $this->lostQta() * $this->non_return_fee);
    }

    /**
     * Goods given back late: daily_late_fee_per_unit for each day after the end of the agreement.
     * Goods lost (or consumed) are never given back, so they only pay the penalty for lost.
     */
    protected function penaltyForDelay(): Attribute
    {
        return Attribute::get(function () {
            $penalty = 0;

            // Units given back: late until the day of their return.
            $returns = $this->stockMovements()
                ->where('to_state', StockState::RETURNED_TO_SUPPLIER)
                ->get();

            foreach ($returns as $return) {
                $penalty += $return->qta * $this->lateDaysUntil($return->created_at) * $this->daily_late_fee_per_unit;
            }

            // Units neither given back nor lost: late until today.
            $notGivenBackQta = $this->qta - $returns->sum('qta') - $this->lostQta();

            $penalty += $notGivenBackQta * $this->lateDaysUntil(now()) * $this->daily_late_fee_per_unit;

            return $penalty;
        });
    }

    /**
     * Units lost or consumed: they will never be given back to the supplier.
     */
    private function lostQta(): int
    {
        return (int) $this->stockBalances()
            ->whereIn('state', [StockState::CONSUMED, StockState::LOST])
            ->sum('stock_balances.qta');
    }

    /**
     * Days after the end of the agreement until the given date: 0 when the date is not after the end.
     */
    private function lateDaysUntil(Carbon $date): int
    {
        return max(0, (int) $this->rentalAgreement->end_date->diffInDays($date->copy()->startOfDay(), false));
    }

    protected function casts(): array
    {
        return [
            'daily_rate_per_unit' => 'decimal:2',
            'daily_late_fee_per_unit' => 'decimal:2',
            'non_return_fee' => 'decimal:2',
            'qta' => 'integer',
        ];
    }
}
