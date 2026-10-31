<?php

namespace App\Models;

use App\Enums\OwnershipType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockLot extends Model
{
    protected $fillable = [
        'item_id',
        'ownership_type',
        'purchase_order_item_id',
        'rental_agreement_item_id',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // Set only for OWNED lots.
    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    // Set only for RENTED lots.
    public function rentalAgreementItem(): BelongsTo
    {
        return $this->belongsTo(RentalAgreementItem::class);
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
            'ownership_type' => OwnershipType::class,
        ];
    }
}