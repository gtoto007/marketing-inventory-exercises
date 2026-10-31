<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    /** @use HasFactory<\Database\Factories\SupplierFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'vat_number',
        'address_line',
        'city',
        'postal_code',
        'country',
        'website'
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot('sku', 'type', 'purchase_price', 'rental_daily_rate', 'daily_late_fee_per_unit', 'non_return_fee');
    }

}
