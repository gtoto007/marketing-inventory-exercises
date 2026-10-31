<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventShipment extends Model
{
    /** @use HasFactory<\Database\Factories\EventShipmentFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'tracking_number',
        'carrier_id',
        'shipping_cost',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    protected function casts(): array
    {
        return [
            'shipping_cost' => 'decimal:2',
        ];
    }
}