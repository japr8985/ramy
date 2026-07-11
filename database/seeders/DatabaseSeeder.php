<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        // Category::factory(3)->create();
        // Customer::factory(10)->create();
        // Unit::factory()->create([
        //     'name' => 'Kilos',
        //     'symbol' => 'Kg.'
        // ]);
        // Unit::factory()->create([
        //     'name' => 'Litros',
        //     'symbol' => 'L.'
        // ]);
        // Unit::factory()->create([
        //     'name' => 'Unidad',
        //     'symbol' => 'U.'
        // ]);
        // Setting::factory()->create([
        //     'key' => 'bcv_rate',
        //     'value' => 709,6935
        // ]);
        Product::factory(100)->create();
    }
}
