<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_users', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('m_user_roles_id')->comment('Foreign key referencing the m_user_roles table');

            $table->string('name', 100)
                ->comment('Fill with name of user');
            $table->string('email', 50)
                ->comment('Fill with user email for login');
            $table->string('password', 255)
                ->comment('Fill with user password');
            $table->string('phone_number', 25)
                ->nullable()
                ->comment('Fill with phone number of user');
            $table->string('photo', 100)
                ->nullable()
                ->comment('Fill with user profile picture');
            $table->timestamp('updated_security')
                ->nullable()
                ->comment('Fill with timestamp when user updates password or email');

            $table->timestamps();
            $table->softDeletes();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->index('m_user_roles_id');
            $table->index('email');
            $table->index('name');
            $table->index('updated_security');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_users');
    }
};
