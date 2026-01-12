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

    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Sanitization helper to remove icons, spaces, and non-numeric characters
     */
    private function sanitizeNumeric($value, $limit = null)
    {
        if (empty($value)) return null;
        
        // Remove everything except digits
        $clean = preg_replace('/[^0-9]/', '', $value);
        
        // Apply character limit if provided (e.g., 3 for phone_code)
        if ($limit) {
            $clean = substr($clean, 0, $limit);
        }
        
        return $clean;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Find the package by name
            $package = isset($row['package_name']) ? Package::where('package_name', $row['package_name'])->first() : null;

            // Clean Phone Code (Max 3 digits) and Phone Number
            $cleanPhoneCode = $this->sanitizeNumeric($row['phone_code'] ?? null);
            $cleanPhoneNumber = $this->sanitizeNumeric($row['phone_number'] ?? null);

            Lead::create([
                'name'             => $row['name'] ?? null,
                'company_name'     => $row['company_name'] ?? null,
                'email'            => $row['email'] ?? null,
                'district'         => $row['district'] ?? null,
                'country'          => $row['country'] ?? null,
                'phone_code'       => $cleanPhoneCode,
                'phone_number'     => $cleanPhoneNumber,
                'city'             => $row['city'] ?? null,
                'client_category'  => $row['client_category'] ?? null,
                'lead_status'      => $row['lead_status'] ?? null,
                'lead_source'      => $row['lead_source'] ?? null,
                'website'          => $row['website'] ?? null,
                'package_id'       => $package ? $package->id : null,
                'inquiry_text'     => $row['inquiry_text'] ?? ($package ? '' : ($row['package_name'] ?? '')),
                'user_id'          => $this->userId,
                'people_count'     => $row['people_count'] ?? 1,
                'child_count'      => $row['child_count'] ?? 0,
            ]);
        }
    }
}