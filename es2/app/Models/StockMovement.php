<?php

namespace App\Models;

use App\Enums\StockState;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    protected $fillable = [
        'qta',
        'from_state',
        'to_state',
        'reason',
        'stock_lot_id',
        'event_id',
        'performed_by',
    ];

    public function stockLot(): BelongsTo
    {
        return $this->belongsTo(StockLot::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    protected function casts(): array
    {
        return [
            'qta' => 'integer',
            'from_state' => StockState::class,
            'to_state' => StockState::class,
        ];
    }
}