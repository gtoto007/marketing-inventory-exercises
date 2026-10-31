<?php

namespace App\Models;

use App\Enums\RentalAgreementStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RentalAgreement extends Model
{
    /** @use HasFactory<\Database\Factories\RentalAgreementFactory> */
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'supplier_agreement_number',
        'shipping_cost',
        'status',
        'created_by',
        'event_id',
        'supplier_id'
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(RentalAgreementItem::class);
    }

    /**
     * What the agreement costs: the rent and the penalties of every item, plus the shipping.
     */
    protected function totalCost(): Attribute
    {
        return Attribute::get(function () {
            // A cancelled agreement costs nothing: the goods never arrived.
            if ($this->status === RentalAgreementStatus::CANCELLED) {
                return 0;
            }

            $days = $this->start_date->diffInDays($this->end_date) + 1;
            $cost = $this->shipping_cost;

            foreach ($this->items as $item) {
                $cost += $item->daily_rate_per_unit * $item->qta * $days;
                $cost += $item->total_penalty;
            }

            return $cost;
        });
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'shipping_cost' => 'decimal:2',
            'status' => RentalAgreementStatus::class,
        ];
    }
}
