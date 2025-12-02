<?php

namespace App\Imports;

use App\Models\Lead;
use App\Models\Package;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LeadsImport implements ToCollection, WithHeadingRow
{
    protected $userId;

    // Accept user_id from controller
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $package = isset($row['package_name']) 
                ? Package::where('package_name', $row['package_name'])->first() 
                : null;

            Lead::create([
                'name'            => $row['name'] ?? null,
                'company_name'    => $row['company_name'] ?? null,
                'email'           => $row['email'] ?? null,
                'district'        => $row['district'] ?? null,
                'country'         => $row['country'] ?? null,
                'phone_code'      => $row['phone_code'] ?? null,
                'phone_number'    => $row['phone_number'] ?? null,
                'city'            => $row['city'] ?? null,
                'client_category' => $row['client_category'] ?? null,
                'lead_status'     => $row['lead_status'] ?? null,
                'lead_source'     => $row['lead_source'] ?? null,
                'website'         => $row['website'] ?? null,
                'package_id'      => $package ? $package->id : null,
                'inquiry_text'    => $package ? ($row['inquiry_text'] ?? null) : ($row['package_name'] ?? ''),
                'user_id'         => $this->userId,
            ]);
        }
    }
}
