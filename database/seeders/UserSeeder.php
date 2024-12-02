<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Input data default untuk tabel m_user
        DB::table('m_user')->insert([
            'id' => "	",
            'name' => 'Admin',
            'email' => 'admin@landa.co.id',
            'password' => Hash::make('devGanteng'),
            'updated_security' => date('Y-m-d H:i:s')
        ]);
    }
}
