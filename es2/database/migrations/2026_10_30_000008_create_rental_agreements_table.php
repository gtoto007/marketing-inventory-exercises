<?php

use App\Enums\RentalAgreementStatus;
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
        Schema::create('rental_agreements', function (Blueprint $table) {
            $table->id();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('supplier_agreement_number')->unique();
            $table->decimal('shipping_cost', 10, 2);
            $table->enum('status', array_column(RentalAgreementStatus::cases(), 'value'))->default(RentalAgreementStatus::PENDING->value);
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('event_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();

            $table->index('event_id');
        });

        // SQLite cannot add a constraint to an existing table: there the check is enforced by the application.
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE rental_agreements ADD CONSTRAINT rental_agreements_dates_check CHECK (end_date >= start_date)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_agreements');
    }
};
