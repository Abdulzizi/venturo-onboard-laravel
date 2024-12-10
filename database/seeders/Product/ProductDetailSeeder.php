<?php

namespace Database\Seeders\Product;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

use DB;

class ProductDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('m_product_detail')->insert([
            [
                'id' => Str::uuid()->toString(),
                'm_product_id' => DB::table('m_product')->where('name', 'Iced Coffee')->first()->id,
                'type' => 'Level',
                'description' => 'Extra Ice',
                'price' => 2000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid()->toString(),
                'm_product_id' => DB::table('m_product')->where('name', 'Iced Coffee')->first()->id,
                'type' => 'Toping',
                'description' => 'Whipped Cream',
                'price' => 5000,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
