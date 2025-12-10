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
    public function run()
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
        CompanySeeder::class,
        CarSeeder::class,
        DifficultyTypesSeeder::class,
        PackageTypeSeeder::class,
        PackageCategorySeeder::class,
        PackageSeeder::class,
        PackageItemSeeder::class,
        LeadSeeder::class,
        InvoiceSeeder::class,
    ]);
}

}
