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
        Schema::table('m_user', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->string('address', 255)
                ->after('phone_number')
                ->nullable()
                ->comment('Fill with user address');


            $table->string('city', 100)
                ->after('address')
                ->nullable()
                ->comment('Fill with user city');


            $table->string('country', 100)
                ->after('city')
                ->nullable()
                ->comment('Fill with user country');

            // Ubah kolom `phone_number` menjadi nullable
            $table->string('phone_number', 25)
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_user', function (Blueprint $table) {
            // Undo changes made in `up()` method
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('country');

            // Ubah kembali `phone_number` menjadi tidak nullable
            $table->string('phone_number', 25)
                ->nullable(false)
                ->change();
        });
    }
};
