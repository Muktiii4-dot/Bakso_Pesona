<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderEvent>
 */
class OrderEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'actor_user_id' => null,
            'actor_type' => 'system',
            'event_type' => 'test_event',
            'old_values' => null,
            'new_values' => null,
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}
