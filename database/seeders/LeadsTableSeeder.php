<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LeadsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('leads')->delete();

        $faker = Faker::create();
        $total = 40000;
        $chunkSize = 1000;

        $leadStatuses = ['Hot', 'Warm', 'Cold', 'Interested'];
        $statuses = ['Pending', 'Approved', 'Quotation Sent', 'Follow-up Taken', 'Converted', 'Lost', 'On Hold', 'Rejected'];

        // Reference "today" as 8 Dec 2025
        $today = Carbon::create(2025, 12, 8);
        $todayStart = $today->startOfDay();
        $todayEnd = $today->endOfDay();

        $yesterdayStart = $today->copy()->subDay()->startOfDay();
        $yesterdayEnd = $today->copy()->subDay()->endOfDay();

        $thisMonthStart = $today->copy()->startOfMonth();
        $thisMonthEnd = $today->copy()->endOfMonth();

        $lastMonthStart = $today->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $today->copy()->subMonth()->endOfMonth();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $rangeChoice = $faker->randomElement(['today', 'yesterday', 'this_month', 'last_month']);

                switch ($rangeChoice) {
                    case 'today':
                        $createdAt = Carbon::instance($faker->dateTimeBetween($todayStart, $todayEnd));
                        break;
                    case 'yesterday':
                        $createdAt = Carbon::instance($faker->dateTimeBetween($yesterdayStart, $yesterdayEnd));
                        break;
                    case 'this_month':
                        $createdAt = Carbon::instance($faker->dateTimeBetween($thisMonthStart, $thisMonthEnd));
                        break;
                    case 'last_month':
                        $createdAt = Carbon::instance($faker->dateTimeBetween($lastMonthStart, $lastMonthEnd));
                        break;
                    default:
                        $createdAt = $todayStart;
                        break;
                }

                // updated_at >= created_at
                $updatedAtEnd = $todayEnd->greaterThan($createdAt) ? $todayEnd : $createdAt->copy()->addSecond();
                $updatedAt = Carbon::instance($faker->dateTimeBetween($createdAt, $updatedAtEnd));

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
                    'created_at'      => $createdAt,
                    'updated_at'      => $updatedAt,
                ];
            }

            DB::table('leads')->insert($data);
            $this->command->info("Inserted $chunkSize leads...");
            $faker->unique($reset = true);
        }

        $this->command->info("✅ All $total leads inserted successfully!");
    }
}
