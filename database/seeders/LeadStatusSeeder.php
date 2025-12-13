<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            'Pending' => 'bg-lime-500 text-white',
            'Approved' => 'bg-green-500 text-white',
            'Quotation Sent' => 'bg-indigo-500 text-white',
            'Follow-up Taken' => 'bg-purple-500 text-white',
            'Converted' => 'bg-teal-500 text-white',
            'Lost' => 'bg-gray-500 text-white',
            'On Hold' => 'bg-amber-500 text-white',
            'Rejected' => 'bg-red-500 text-white',
        ];

        foreach ($statuses as $name => $color) {
            DB::table('lead_statuses')->insert([
                'company_id' => null,
                'name' => $name,
                'color' => $color,
                'is_active' => true,
                'is_global' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
