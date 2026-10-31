<?php

namespace Database\Factories;

use App\Enums\RentalAgreementStatus;
use App\Models\Event;
use App\Models\Item;
use App\Models\RentalAgreementItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalAgreement>
 */
class RentalAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'event_id' => Event::factory(),
            'created_by' => User::factory(),
            'supplier_agreement_number' => fake()->unique()->bothify('RA-######'),
            // The goods are rented for the whole event.
            'start_date' => fn (array $attributes) => Event::find($attributes['event_id'])->start_date,
            'end_date' => fn (array $attributes) => Event::find($attributes['event_id'])->end_date,
            'shipping_cost' => fake()->randomFloat(2, 0, 100),
            'status' => RentalAgreementStatus::PENDING,
        ];
    }

    /**
     * Add an item to the agreement: it is rented at the supplier list rates.
     */
    public function withItem(Item $item, int $qta): static
    {
        return $this->has(RentalAgreementItem::factory()->for($item)->state(['qta' => $qta]), 'items');
    }
}