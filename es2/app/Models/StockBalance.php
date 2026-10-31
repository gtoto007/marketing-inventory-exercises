<?php

namespace App\Models;

use App\Enums\StockState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Net quantity of a stock lot in a given state (and event), kept in sync with the stock movements.
 */
class StockBalance extends Model
{
    protected $fillable = [
        'stock_lot_id',
        'state',
        'event_id',
        'qta',
    ];

    public function stockLot(): BelongsTo
    {
        return $this->belongsTo(StockLot::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    protected function casts(): array
    {
        return [
            'state' => StockState::class,
            'qta' => 'integer',
        ];
    }
}