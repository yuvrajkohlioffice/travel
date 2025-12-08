<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LeadsTableSeeder extends Seeder
{
    public function run()
    {
        // Clear table before seeding
        DB::table('leads')->delete();


        $faker = Faker::create();
        $total = 40000;       // Total leads
        $chunkSize = 1000;    // Insert in chunks for performance

        $leadStatuses = ['Hot', 'Warm', 'Cold', 'Interested'];
        $statuses = ['Pending', 'Approved', 'Quotation Sent', 'Follow-up Taken', 'Converted', 'Lost', 'On Hold', 'Rejected'];

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $data[] = [
                    'user_id'         => 1,
                    'name'            => $faker->name,
                    'company_name'    => $faker->company,
                    'email'           => $faker->unique()->safeEmail,
                    'country'         => $faker->country,
                    'phone_code'      => '+' . $faker->numberBetween(1, 99),
                    'phone_number'    => $faker->numberBetween(1000000000, 9999999999),
                    'city'            => $faker->city,
                    'district'        => $faker->state,
                    'client_category' => $faker->randomElement(['Retail', 'Corporate', 'Individual']),
                    'lead_status'     => $faker->randomElement($leadStatuses),
                    'status'          => $faker->randomElement($statuses),
                    'lead_source'     => $faker->randomElement(['Website','Referral','Email','Social Media']),
                    'website'         => $faker->optional()->url,
                    'package_id'      => 1,
                    'people_count'    => $faker->numberBetween(1,10),
                    'child_count'     => $faker->numberBetween(0,5),
                    'inquiry_text'    => $faker->optional()->sentence(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }

            DB::table('leads')->insert($data);
            $this->command->info("Inserted $chunkSize leads...");

            // Reset unique to prevent Faker overflow in large inserts
            $faker->unique($reset = true);
        }

        $this->command->info("✅ All $total leads inserted successfully!");
    }
}
