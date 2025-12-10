<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CarSeeder extends Seeder
{
    public function run()
    {
        $chunkSize = 500;

        for ($i = 1; $i <= 10000; $i += $chunkSize) {

            $batch = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $batch[] = [
                    'car_type'      => fake()->randomElement(['SUV', 'Sedan', 'Hatchback', 'Tempo Traveller', 'Mini Bus']),
                    'capacity'      => fake()->numberBetween(2, 20),
                    'price_per_km'  => fake()->numberBetween(5, 50),
                    'price_per_day' => fake()->numberBetween(500, 5000),
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }

            DB::table('cars')->insert($batch);
        }
    }
}
