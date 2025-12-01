<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('package_types')->insert([
            ['name' => 'Basic Package'],
            ['name' => 'Standard Package'],
            ['name' => 'Premium Package'],
        ]);
    }
}
