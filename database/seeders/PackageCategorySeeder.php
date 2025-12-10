<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageCategorySeeder extends Seeder
{
    public function run()
    {
        $chunkSize = 500; // insert 500 rows at a time

        for ($i = 1; $i <= 1; $i += $chunkSize) {

            $batch = [];

            for ($j = 0; $j < $chunkSize; $j++) {
                $batch[] = [
                    'name'       => fake()->words(2, true),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            DB::table('package_category')->insert($batch);
        }
    }
}
