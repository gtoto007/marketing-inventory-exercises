<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('-2 months', '+2 months')->format('Y-m-d'),
            // Computed from start_date, so it stays valid when start_date is overridden.
            'end_date' => fn (array $attributes) => Carbon::parse($attributes['start_date'])
                ->addDays(fake()->numberBetween(0, 4))
                ->format('Y-m-d'),
            'venue_name' => fake()->company(),
            'address_line' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(),
        ];
    }
}