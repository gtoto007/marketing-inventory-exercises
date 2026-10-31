<?php

namespace Database\Factories;

use App\Enums\SupplyType;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->optional()->sentence(),
            'photo_url' => fake()->optional()->imageUrl(),
            'ean' => fake()->optional()->ean13(),
            'min_stock_quantity' => fake()->optional()->numberBetween(1, 50),
            'category_id' => Category::factory(),
        ];
    }

    /**
     * The item can be bought from the supplier (a new one if none is given): only the purchase price is set.
     */
    public function purchasable(?Supplier $supplier = null): static
    {
        return $this->withSupplier($supplier, fn () => [
            'type' => SupplyType::PURCHASABLE,
            ...$this->purchasePrices(),
        ]);
    }

    /**
     * The item can be rented from the supplier (a new one if none is given): only the rental rates and fees are set.
     */
    public function rentable(?Supplier $supplier = null): static
    {
        return $this->withSupplier($supplier, fn () => [
            'type' => SupplyType::RENTABLE,
            ...$this->rentalPrices(),
        ]);
    }

    /**
     * The same supplier (a new one if none is given) both sells and rents the item: all prices are set.
     */
    public function purchasableAndRentable(?Supplier $supplier = null): static
    {
        return $this->withSupplier($supplier, fn () => [
            'type' => SupplyType::BOTH,
            ...$this->purchasePrices(),
            ...$this->rentalPrices(),
        ]);
    }

    private function purchasePrices(): array
    {
        return [
            'purchase_price' => fake()->randomFloat(2, 1, 500),
        ];
    }

    private function rentalPrices(): array
    {
        return [
            'rental_daily_rate' => fake()->randomFloat(2, 1, 50),
            'daily_late_fee_per_unit' => fake()->randomFloat(2, 0, 20),
            'non_return_fee' => fake()->randomFloat(2, 0, 300),
        ];
    }

    /**
     * Attach the supplier on the item_supplier pivot with a random SKU plus the given prices.
     */
    private function withSupplier(?Supplier $supplier, callable $prices): static
    {
        return $this->hasAttached(
            $supplier ?? Supplier::factory(),
            fn () => [
                'sku' => fake()->bothify('SKU-####-??'),# = digit, ? = lowercase letter, * = letter or digit; anything else is kept as is.
                ...$prices(),
            ],
            'suppliers'
        );
    }
}
