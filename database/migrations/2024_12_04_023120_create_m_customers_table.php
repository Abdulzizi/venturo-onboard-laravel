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
        Schema::create('m_customers', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('m_user_id');
            $table->string('name', 100);
            $table->text('address')->nullable();
            $table->text('photo')->nullable();
            $table->string('phone', 100)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
            $table->integer('deleted_by')->default(0);

            $table->index('name');
            $table->index('m_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_customers');
    }
};
