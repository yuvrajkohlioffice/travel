<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageTypeSeeder extends Seeder
{
    public function run()
    {
        $types = [
            ['name' => 'Adventure'],
            ['name' => 'Trekking'],
            ['name' => 'Camping'],
            ['name' => 'Sightseeing'],
            ['name' => 'Spiritual'],
        ];

        $rows = [];

        foreach ($types as $type) {
            $rows[] = [
                'name'       => $type['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('package_types')->insert($rows);
    }
}
