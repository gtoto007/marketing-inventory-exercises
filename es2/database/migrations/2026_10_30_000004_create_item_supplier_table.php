<?php

use App\Enums\SupplyType;
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
        Schema::create('item_supplier', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            // Whether the supplier sells the item, rents it, or both.
            $table->enum('type', array_column(SupplyType::cases(), 'value'));
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->decimal('rental_daily_rate', 10, 2)->nullable();
            $table->decimal('daily_late_fee_per_unit', 10, 2)->nullable()->default(0);
            $table->decimal('non_return_fee', 10, 2)->nullable()->default(0);
            $table->foreignId('item_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['supplier_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_supplier');
    }
};
