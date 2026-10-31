<?php

use App\Enums\StockState;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_lot_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('state', array_column(StockState::cases(), 'value'));
            $table->foreignId('event_id')->nullable()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('qta');
            $table->timestamps();

            $table->unique(['stock_lot_id', 'state', 'event_id']);
            $table->index('event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_balances');
    }
};
