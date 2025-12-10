<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Package;
use Illuminate\Support\Facades\DB;

class PackageItemSeeder extends Seeder
{
    public function run()
    {
        $chunkSize = 500; // insert 500 rows per batch

        for ($i = 1; $i <= 1; $i += $chunkSize) {

            $batch = [];

            for ($j = 0; $j < $chunkSize; $j++) {

                $batch[] = [
                    'package_id' =>  Package::inRandomOrder()->first()->id,
                    'car_id'         => fake()->numberBetween(1, 10000),
                    'person_count'   => fake()->numberBetween(1, 10),
                    'vehicle_name'   => fake()->words(2, true),
                    'room_count'     => fake()->numberBetween(1, 5),
                    'standard_price' => fake()->numberBetween(1000, 5000),
                    'deluxe_price'   => fake()->numberBetween(3000, 8000),
                    'luxury_price'   => fake()->numberBetween(5000, 12000),
                    'premium_price'  => fake()->numberBetween(8000, 18000),
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

            }

            DB::table('package_items')->insert($batch);
        }
    }
}
