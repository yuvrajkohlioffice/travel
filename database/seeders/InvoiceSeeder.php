<?php

namespace Database\Seeders;

use App\Models\Lead;
use App\Models\Package;
use App\Models\PackageItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InvoiceSeeder extends Seeder
{
    public function run()
    {
        $rows = [];

        for ($i = 1; $i <= 1000; $i++) {
            $rows[] = [
                'invoice_no' => 'INV-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'user_id' => User::inRandomOrder()->first()->id,
                'lead_id' => Lead::inRandomOrder()->first()->id,
                'package_id' => Package::inRandomOrder()->first()->id,
                'package_items_id' => PackageItem::inRandomOrder()->first()->id,

                'issued_date' => fake()->date(),
                'travel_start_date' => fake()->date(),

                'primary_full_name' => fake()->name(),
                'primary_email' => fake()->email(),
                'primary_phone' => fake()->phoneNumber(),
                'primary_address' => fake()->address(),

                'additional_travelers' => json_encode([
                    [
                        'name' => fake()->name(),
                        'age' => fake()->numberBetween(5, 70),
                        'relation' => fake()->randomElement(['Friend', 'Family', 'Colleague']),
                    ],
                    [
                        'name' => fake()->name(),
                        'age' => fake()->numberBetween(5, 70),
                        'relation' => fake()->randomElement(['Friend', 'Family', 'Colleague']),
                    ],
                ]),

                'total_travelers' => fake()->numberBetween(1, 15),
                'adult_count' => fake()->numberBetween(1, 10),
                'child_count' => fake()->numberBetween(0, 5),

                'package_name' => fake()->words(3, true),
                'package_type' => fake()->randomElement(['Adventure', 'Holiday', 'Trekking', 'Luxury']),

                'price_per_person' => fake()->numberBetween(500, 5000),
                'subtotal_price' => fake()->numberBetween(5000, 50000),
                'discount_amount' => fake()->numberBetween(0, 5000),
                'tax_amount' => fake()->numberBetween(100, 2000),
                'final_price' => fake()->numberBetween(5000, 70000),

                'additional_details' => fake()->sentence(10),

                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks for performance
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table('invoices')->insert($chunk);
        }
    }
}
