<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $supplier = Supplier::inRandomOrder(1)->get();
        $user = \App\Models\User::inRandomOrder(1)->get();

        return [
            'invoice_number' => fake()->numerify('#####-#'),
            'supplier_id' => $supplier->id,
            'purchase_date' => fake()->dateTimeBetween(now()->subDays(fake()->randomDigit())),
            'due_date' => fake()->dateTimeBetween(now()->subDays(fake()->randomDigit())),
            'total' => fake()->randomFloat(3, 1000, 10000),
            'status' => fake()->randomElement(['draft', 'pending', 'paid']),
            'notes' => fake()->paragraph(),
            'proof_image' => null,
            'created_by' => $user->id
        ];
    }
}
