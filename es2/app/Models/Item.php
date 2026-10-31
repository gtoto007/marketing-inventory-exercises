<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'photo_url',
        'ean',
        'min_stock_quantity',
        'category_id',
    ];

    public function category() : BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class)->withPivot('sku', 'type', 'purchase_price', 'rental_daily_rate', 'daily_late_fee_per_unit', 'non_return_fee');
    }

    public function stockLots(): HasMany
    {
        return $this->hasMany(StockLot::class);
    }

    protected function casts(): array
    {
        return [
            'min_stock_quantity' => 'integer',
        ];
    }

}
