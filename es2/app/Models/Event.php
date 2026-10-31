<?php

namespace App\Models;

use App\Enums\OwnershipType;
use App\Enums\StockState;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'venue_name',
        'address_line',
        'city',
        'postal_code',
        'country_code'
    ];

    public function rentalAgreements(): HasMany
    {
        return $this->hasMany(RentalAgreement::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(EventShipment::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Owned goods consumed or lost at the event cost their purchase price.
     * Rented goods are paid through their rental agreement.
     */
    protected function costOfOwnedStockLots(): Attribute
    {
        return Attribute::get(fn () => $this->stockBalances()
            ->whereIn('state', [StockState::CONSUMED, StockState::LOST])
            ->whereHas('stockLot', fn ($lot) => $lot->where('ownership_type', OwnershipType::OWNED))
            ->with('stockLot.purchaseOrderItem')
            ->get()
            ->sum(fn (StockBalance $balance) => $balance->qta * $balance->stockLot->purchaseOrderItem->price));
    }

    /**
     * Rent, penalties and shipping of the rental agreements of the event (a cancelled one costs 0).
     */
    protected function costOfRentalAgreements(): Attribute
    {
        return Attribute::get(fn () => $this->rentalAgreements->sum('total_cost'));
    }

    /**
     * Shipments to and from the event.
     */
    protected function costOfShipments(): Attribute
    {
        return Attribute::get(fn () => $this->shipments()->sum('shipping_cost'));
    }
}
