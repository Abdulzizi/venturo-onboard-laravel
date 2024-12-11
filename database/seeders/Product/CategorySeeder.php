<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (['Beverages', 'Snacks', 'Desserts'] as $name) {
            if (!DB::table('m_product_categories')->where('name', $name)->exists()) {
                DB::table('m_product_categories')->insert([
                    'id' => Str::uuid(),
                    'name' => $name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
