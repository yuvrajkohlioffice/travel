<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DifficultyType;
use App\Models\PackageType;
use App\Models\PackageCategory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call individual seeders
        $this->call([
            DifficultyTypesSeeder::class,
            PackageTypesSeeder::class,
            PackageCategorySeeder::class,
        ]);
    }
}
