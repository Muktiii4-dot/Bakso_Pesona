<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'BP-'.fake()->unique()->numerify('########'),
            'user_id' => User::factory(),
            'recipient_name' => fake()->name(),
            'phone' => '08123456789',
            'shipping_address' => fake()->address(),
            'subtotal' => 25000,
            'shipping_cost' => null,
            'total_amount' => null,
            'status' => Order::STATUS_PENDING_CONFIRMATION,
            'payment_method' => 'manual',
            'payment_status' => Order::PAYMENT_AWAITING_CONFIRMATION,
        ];
    }
}
