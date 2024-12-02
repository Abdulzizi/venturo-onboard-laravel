<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('m_user_roles')->insert([
            [
                'id' => Str::uuid(),
                'name' => 'Admin',
                'access' => json_encode(['create', 'read', 'update', 'delete']),
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'User',
                'access' => json_encode(['read']),
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Manager',
                'access' => json_encode(['read', 'update']),
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
