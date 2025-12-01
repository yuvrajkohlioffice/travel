<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('package_category')->insert([
            ['name' => 'Adventure'],
            ['name' => 'Family'],
            ['name' => 'Corporate'],
        ]);
    }
}
