<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'cars';

    // Fillable fields
    protected $fillable = [
        'car_type',
        'capacity',
        'price_per_km',
        'price_per_day',
    ];

    // Casts
    protected $casts = [
        'capacity' => 'integer',
        'price_per_km' => 'decimal:2',
        'price_per_day' => 'decimal:2',
    ];

    /**
     * Many-to-Many relationship with Package through package_items
     */
    public function packages()
    {
        return $this->belongsToMany(
            Package::class,       // Related model
            'package_items',      // Pivot table
            'car_id',             // Foreign key on pivot for this model
            'package_id'          // Foreign key on pivot for related model
        )
        ->withPivot('custom_price', 'already_price')
        ->withTimestamps();
    }

    /**
     * Get price per km
     */
    public function getPricePerKmAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * Get price per day
     */
    public function getPricePerDayAttribute($value)
    {
        return number_format($value, 2);
    }

    /**
     * Availability placeholder
     */
    public function isAvailable()
    {
        return true; // Placeholder, implement your logic here
    }
}
