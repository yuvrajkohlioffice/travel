<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin'],
            ['name' => 'Manager'],
            ['name' => 'Staff'],
            ['name' => 'User'],
        ];

        $rows = [];

        foreach ($roles as $role) {
            $rows[] = [
                'name'       => $role['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('roles')->insert($rows);
    }
}
