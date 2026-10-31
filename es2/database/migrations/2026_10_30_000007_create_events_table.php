<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('venue_name');
            $table->string('address_line');
            $table->string('city', 100);
            $table->string('postal_code', 20);
            $table->char('country_code', 2);
            $table->timestamps();
        });

        // SQLite cannot add a constraint to an existing table: there the check is enforced by the application.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE events ADD CONSTRAINT events_dates_check CHECK (end_date IS NULL OR end_date >= start_date)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};