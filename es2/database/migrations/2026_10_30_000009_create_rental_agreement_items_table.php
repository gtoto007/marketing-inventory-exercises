<?php

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
        Schema::create('rental_agreement_items', function (Blueprint $table) {
            $table->id();
            $table->decimal('daily_rate_per_unit', 10, 2);
            $table->decimal('daily_late_fee_per_unit', 10, 2)->default(0);
            $table->decimal('non_return_fee', 10, 2)->default(0);
            $table->unsignedInteger('qta');
            $table->foreignId('item_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('rental_agreement_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->unique(['rental_agreement_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_agreement_items');
    }
};
