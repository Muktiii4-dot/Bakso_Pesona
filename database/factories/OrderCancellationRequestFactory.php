<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderCancellationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderCancellationRequest>
 */
class OrderCancellationRequestFactory extends Factory
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
            'requested_by_user_id' => User::factory()->state(['role' => 'customer']),
            'requested_by_role' => 'customer',
            'reason' => fake()->sentence(),
            'status' => 'pending',
            'decided_by_user_id' => null,
            'decision_note' => null,
            'decided_at' => null,
        ];
    }
}
