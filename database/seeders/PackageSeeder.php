<?php

namespace Database\Seeders;

use App\Models\PackageCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run()
    {
        $chunkSize = 500; // insert in batches of 500

        for ($i = 1; $i <= 1; $i += $chunkSize) {

            $batch = [];

            for ($i = 1; $i <= 1; $i++) {

                $batch[] = [
                    'company_id'           => 1,
                    'package_type_id'      => 1,
                    'package_category_id'  => PackageCategory::inRandomOrder()->first()->id,
                    'difficulty_type_id'   => fake()->numberBetween(1, 5),
                    'pickup_points'        => fake()->address(),
                    'package_name'         => fake()->words(3, true),
                    'package_docs'         => 'doc_' . fake()->uuid() . '.pdf',
                    'package_banner'       => 'banner_' . fake()->uuid() . '.jpg',
                    'other_images'         => json_encode([
                        fake()->imageUrl(),
                        fake()->imageUrl()
                    ]),
                    'package_days'         => fake()->numberBetween(1, 15),
                    'package_nights'       => fake()->numberBetween(1, 14),
                    'package_price'        => fake()->numberBetween(2000, 50000),
                    'altitude'             => fake()->numberBetween(500, 6000),
                    'min_age'              => 10,
                    'max_age'              => 60,
                    'best_time_to_visit'   => fake()->monthName(),
                    'content'              => fake()->paragraph(5),
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ];

            }

            DB::table('packages')->insert($batch);
        }
    }
}
