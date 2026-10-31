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
        $states = array_column(StockState::cases(), 'value');

        Schema::create('stock_movements', function (Blueprint $table) use ($states) {
            $table->id();
            $table->unsignedInteger('qta');
            // NULL only when goods are received from a supplier.
            $table->enum('from_state', $states)->nullable();
            $table->enum('to_state', $states);
            $table->string('reason')->nullable();
            $table->foreignId('stock_lot_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->nullable()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('performed_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index('stock_lot_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
