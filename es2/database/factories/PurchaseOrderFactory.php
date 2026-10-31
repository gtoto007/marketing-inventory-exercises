<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use App\Models\Item;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
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
            'created_by' => User::factory(),
            'supplier_order_number' => fake()->unique()->bothify('PO-######'),
            'shipping_cost' => fake()->randomFloat(2, 0, 100),
            'status' => PurchaseOrderStatus::PENDING,
        ];
    }

    /**
     * Add an item to the order: it is bought at the supplier list price.
     */
    public function withItem(Item $item, int $qta): static
    {
        return $this->has(PurchaseOrderItem::factory()->for($item)->state(['qta' => $qta]), 'items');
    }
}