<?php

namespace Database\Factories;

use App\Models\Carrier;
use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventShipment>
 */
class EventShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'carrier_id' => Carrier::factory(),
            'tracking_number' => fake()->unique()->bothify('TRK-########'),
            'shipping_cost' => fake()->randomFloat(2, 0, 100),
        ];
    }
}