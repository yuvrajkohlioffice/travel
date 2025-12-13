<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FollowupReason;

class FollowupReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name' => 'Call Back Later', 'date' => true, 'time' => true],
            ['name' => 'Call Me Tomorrow', 'date' => true, 'time' => true],
            ['name' => 'Payment Tomorrow', 'date' => true, 'time' => true],
            ['name' => 'Talk With My Partner', 'date' => true, 'time' => true],
            ['name' => 'Work with other company', 'date' => false, 'time' => false],
            ['name' => 'Not Interested', 'date' => false, 'time' => false],
            ['name' => 'Interested', 'date' => true, 'time' => true],
            ['name' => 'Wrong Information', 'date' => false, 'time' => false],
            ['name' => 'Not Pickup', 'date' => false, 'time' => false],
            ['name' => 'Other', 'date' => false, 'time' => false],
        ];

        foreach ($reasons as $reason) {
            FollowupReason::updateOrCreate(
                ['name' => $reason['name']],
                [
                    'company_id' => null, // Global reason
                    'remark' => null,
                    'date' => $reason['date'],
                    'time' => $reason['time'],
                    'email_template' => null,
                    'whatsapp_template' => null,
                    'is_active' => true,
                    'is_global' => true,
                ]
            );
        }
    }
}
