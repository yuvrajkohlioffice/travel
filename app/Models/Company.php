<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'owner_id',
        'team_name',
        'logo',
        'scanner_details',
        'bank_details',
        'whatsapp_api_key',
        'email',        // NEW
        'phone',        // NEW
    ];

    /**
     * Attribute Casting
     */
    protected $casts = [
        'scanner_details' => 'array',
        'bank_details'    => 'array',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Accessor for logo URL
     */
    public function getLogoAttribute($value)
    {
        if (!$value) return null;

        return filter_var($value, FILTER_VALIDATE_URL)
            ? $value
            : asset('storage/' . $value);
    }

    /**
     * Accessor for scanner_details (auto image URL conversion)
     */
    public function getScannerDetailsAttribute($value)
    {
        if (!$value) return [];

        $details = json_decode($value, true);

        if (!is_array($details)) return [];

        if (!empty($details['image'])) {

            // Convert storage path to full URL if needed
            if (!filter_var($details['image'], FILTER_VALIDATE_URL)) {
                $details['image'] = asset('storage/' . $details['image']);
            }
        }

        return $details;
    }
}
