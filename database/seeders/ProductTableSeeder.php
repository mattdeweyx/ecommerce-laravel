<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Database\Factories\ProductFactory;

class ProductTableSeeder extends Seeder
{
    public function run()
    {
        Product::factory()->count(50)->create();
    }
}