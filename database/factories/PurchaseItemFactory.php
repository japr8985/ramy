<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseItem>
 */
class PurchaseItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::inRandomOrder(1)->get();

        return [
            'purchase_id' => Purchase::inRandomOrder(1)->get(),
            'product_id' =>  $product->id, 
            'quantity' => fake()->randomNumber(2), 
            'unit_price' => fake()->randomNumber(2), 
            'selling_price' => $product->selling_price, 
            'subtotal' => $product->selling_price
        ];
    }
}
