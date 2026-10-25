<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderRefund;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderRefund>
 */
class OrderRefundFactory extends Factory
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
            'amount' => 25000,
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'processed_by_user_id' => null,
            'transfer_reference' => null,
            'requested_at' => now(),
            'completed_at' => null,
        ];
    }
}
