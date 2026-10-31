<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\RentalAgreement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RentalAgreementItem>
 */
class RentalAgreementItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rental_agreement_id' => RentalAgreement::factory(),
            // An item the supplier of the agreement rents.
            'item_id' => fn (array $attributes) => Item::factory()
                ->rentable(RentalAgreement::find($attributes['rental_agreement_id'])->supplier),
            // Rates and fees are copied from the supplier price list.
            'daily_rate_per_unit' => fn (array $attributes) => $this->offer($attributes)->rental_daily_rate,
            'daily_late_fee_per_unit' => fn (array $attributes) => $this->offer($attributes)->daily_late_fee_per_unit ?? 0,
            'non_return_fee' => fn (array $attributes) => $this->offer($attributes)->non_return_fee ?? 0,
            'qta' => fake()->numberBetween(1, 100),
        ];
    }

    /**
     * The price list of the item at the supplier of the agreement.
     */
    private function offer(array $attributes): object
    {
        return Item::find($attributes['item_id'])
            ->suppliers()
            ->whereKey(RentalAgreement::find($attributes['rental_agreement_id'])->supplier_id)
            ->first()
            ->pivot;
    }
}