<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Carrier extends Model
{
    /** @use HasFactory<\Database\Factories\CarrierFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function shipments(): HasMany
    {
        return $this->hasMany(EventShipment::class);
    }
}