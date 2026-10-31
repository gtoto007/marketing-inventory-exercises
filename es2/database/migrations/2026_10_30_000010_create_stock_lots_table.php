<?php

use App\Enums\OwnershipType;
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
        Schema::create('stock_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->enum('ownership_type', array_column(OwnershipType::cases(), 'value'));
            // OWNED lots point to a purchase line, RENTED lots to a rental line.
            $table->foreignId('purchase_order_item_id')->nullable()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('rental_agreement_item_id')->nullable()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index('item_id');
            $table->index('rental_agreement_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_lots');
    }
};
