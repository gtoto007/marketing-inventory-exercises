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
        Schema::create('event_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->index()->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->string('tracking_number')->nullable();
            $table->foreignId('carrier_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->decimal('shipping_cost', 10, 2);

            $table->unique(['tracking_number', 'carrier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_shipments');
    }
};