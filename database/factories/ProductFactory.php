<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomFloat(4, 1, 700000);
        return [
            'category_id' => Category::inRandomOrder(1)->first()?->id ?? Category::factory(), 
            'unit_id' => Unit::inRandomOrder(1)->first()?->id ?? Unit::factory(), 
            'sku' => fake()->unique()->ean13(), 
            // Avoid 0 words which causes empty string names
            'name' => fake()->words(fake()->numberBetween(1, 4), true), 
            'purchase_price' => $price, 
            'selling_price' => round(($price * 1.3), 4), // Kept within scale limits
            'quantity' => fake()->randomNumber(2), 
            'min_stock' => fake()->randomNumber(2),
            'is_active' => true, 
            'description' => fake()->paragraph(), 
            'notes' => fake()->paragraph(2)
        ];
    }
}
