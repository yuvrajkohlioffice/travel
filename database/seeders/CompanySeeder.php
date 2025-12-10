<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $rows = [];

        for ($i = 1; $i <= 20; $i++) {
            $rows[] = [
                'company_name' => fake()->company(),
                'owner_id'     => 1, // super admin (or change)
                'team_name'    => fake()->word(),
                'created_at'   => now(),
                'updated_at'   => now(),
                'deleted_at'   => null,
            ];
        }

        DB::table('companies')->insert($rows);
    }
}
