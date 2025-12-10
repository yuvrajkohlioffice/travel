<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $rows = [];

        for ($i = 1; $i <= 50; $i++) {
            $rows[] = [
                'role_id'                  => fake()->numberBetween(1, 5),
                'company_id'               => null,
                'whatsapp_api_key'         => fake()->uuid(),
                'status'                   => fake()->randomElement(['1', '0']),
                'name'                     => fake()->name(),
                'email'                    => fake()->unique()->safeEmail(),
                'email_verified_at'        => now(),
                'password'                 => Hash::make('password'),
                'two_factor_secret'        => null,
                'two_factor_recovery_codes'=> null,
                'two_factor_confirmed_at'  => null,
                'remember_token'           => fake()->sha1(),
                'current_team_id'          => null,
                'profile_photo_path'       => null,
                'created_at'               => now(),
                'updated_at'               => now(),
            ];
        }

        DB::table('users')->insert($rows);
    }
}
