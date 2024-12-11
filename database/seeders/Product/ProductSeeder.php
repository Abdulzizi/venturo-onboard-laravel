<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_products')->insert([
            [
                'id' => Str::uuid(),
                'm_product_category_id' => DB::table('m_product_categories')->where('name', 'Beverages')->first()->id,
                'name' => 'Iced Coffee',
                'price' => 15000,
                'description' => 'Refreshing iced coffee',
                'photo' => '/images/iced_coffee.jpg',
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'm_product_category_id' => DB::table('m_product_categories')->where('name', 'Snacks')->first()->id,
                'name' => 'French Fries',
                'price' => 20000,
                'description' => 'Crispy golden fries',
                'photo' => '/images/french_fries.jpg',
                'is_available' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
