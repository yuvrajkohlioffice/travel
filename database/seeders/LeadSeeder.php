<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadSeeder extends Seeder
{
    public function run()
    {
        $rows = [];

        for ($i = 1; $i <= 10000; $i++) {

            $rows[] = [
                'user_id'        => User::inRandomOrder()->first()->id, // adjust based on your user count
                'name'           => fake()->name(),
                'company_name'   => fake()->company(),
                'email'          => fake()->unique()->safeEmail(),
                'country'        => fake()->country(),
                'phone_code'     => fake()->numberBetween(1, 999),
                'phone_number'   => fake()->phoneNumber(),
                'city'           => fake()->city(),
                'district'       => fake()->citySuffix(),
                'client_category'=> fake()->randomElement(['Hot', 'Warm', 'Cold']),
                'lead_status'    => fake()->randomElement(['Hot', 'Warm', 'Cold']),
                'status'         => fake()->randomElement(['Pending', 'Approved','Quotation Sent','Follow-up Taken','Converted','Lost','On Hold','Rejected']),
                'lead_source'    => fake()->randomElement(['Facebook', 'Website', 'Referral', 'Email Campaign']),
                'website'        => fake()->url(),
                'package_id'     => Package::inRandomOrder()->first()->id, // adjust based on your package count
                'people_count'   => rand(1, 10),
                'child_count'    => rand(0, 5),
                'inquiry_text'   => fake()->sentence(10),
                'created_at'     => now(),
                'updated_at'     => now(),
                'deleted_at'     => null,
            ];
        }

        // Insert in chunks for performance
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('leads')->insert($chunk);
        }
    }
}
