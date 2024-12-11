<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_sales_details', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('t_sales_id'); // Referencing to t_sales
            $table->string('m_product_id'); // Referencing to m_products
            $table->string('m_product_detail_id'); //Referencing to m_product_details

            $table->integer('total_item');
            $table->decimal('price', 15, 2);

            $table->timestamps();

            $table->index('id');
            $table->index('t_sales_id');
            $table->index('m_product_id');
            $table->index('m_product_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_sales_details');
    }
};
