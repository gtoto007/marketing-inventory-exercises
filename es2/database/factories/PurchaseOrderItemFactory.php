<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            // An item the supplier of the order sells.
            'item_id' => fn (array $attributes) => Item::factory()
                ->purchasable(PurchaseOrder::find($attributes['purchase_order_id'])->supplier),
            // The price is copied from the supplier price list.
            'price' => fn (array $attributes) => Item::find($attributes['item_id'])
                ->suppliers()
                ->whereKey(PurchaseOrder::find($attributes['purchase_order_id'])->supplier_id)
                ->first()
                ->pivot
                ->purchase_price,
            'qta' => fake()->numberBetween(1, 100),
        ];
    }
}