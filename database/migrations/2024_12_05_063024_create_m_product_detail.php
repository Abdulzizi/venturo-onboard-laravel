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
        Schema::create('m_product_detail', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('m_product_id')
                ->comment('Fill with id from table m_product');
            $table->enum('type', ['Level', 'Toping'])
                ->comment('Fill with type of detail product');
            $table->string('description', 255)
                ->comment('Fill with description of detail product, ex : Topping Telur');
            $table->double('price')->default(0)
                ->comment('Fill price of product details');

            $table->timestamps();
            $table->softDeletes();

            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);

            $table->index('m_product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_product_detail');
    }
};
