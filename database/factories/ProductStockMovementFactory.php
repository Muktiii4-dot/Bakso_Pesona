<?php

namespace Database\Factories;

use App\Models\ProductStockMovement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductStockMovement>
 */
class ProductStockMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => null,
            'order_id' => null,
            'order_item_id' => null,
            'actor_user_id' => null,
            'movement_type' => 'manual_adjustment',
            'direction' => 0,
            'quantity' => 1,
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}
