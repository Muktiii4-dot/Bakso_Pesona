<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderRating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderRating>
 */
class OrderRatingFactory extends Factory
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
            'customer_user_id' => fn (array $attributes): int => Order::query()
                ->findOrFail($attributes['order_id'])
                ->user_id,
            'rating' => fake()->numberBetween(1, 5),
            'original_comment' => fake()->optional()->sentence(),
            'edited_comment' => null,
            'moderation_status' => 'pending',
            'moderated_by_user_id' => null,
            'moderation_note' => null,
            'moderated_at' => null,
        ];
    }
}
