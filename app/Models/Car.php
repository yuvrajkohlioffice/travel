<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    // Only fillable fields that exist in the table
    protected $fillable = [
        'name',
        'type',
        'capacity',
        'price', // JSON column
    ];

    protected $casts = [
        'capacity' => 'integer',
        'price' => 'array', // automatically cast JSON to array
    ];

    /**
     * Many-to-Many relationship with Package
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_car', 'car_id', 'package_id')
                    ->withTimestamps();
    }

    /**
     * Helper method to get price per km
     */
    public function getPricePerKmAttribute()
    {
        return $this->price['per_km'] ?? 0;
    }

    // Accessor for price_per_day
    public function getPricePerDayAttribute()
    {
        return $this->price['per_day'] ?? 0;
    }

    /**
     * Example helper method
     */
    public function isAvailable()
    {
        return true; // placeholder
    }
}
