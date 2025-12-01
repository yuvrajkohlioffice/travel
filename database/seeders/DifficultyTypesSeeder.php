<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DifficultyTypesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('difficulty_types')->insert([
            ['name' => 'Easy', 'level' => 1],
            ['name' => 'Medium', 'level' => 2],
            ['name' => 'Hard', 'level' => 3],
        ]);
    }
}
