<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = 'cars';

    protected $fillable = [
        'name',
        'type',
        'capacity',
        'price',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'price' => 'array',
    ];

    /**
     * Many-to-Many relationship with Package through package_items
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_items', 'car_id', 'package_id')
                    ->withPivot('custom_price', 'already_price')
                    ->withTimestamps();
    }

    public function getPricePerKmAttribute()
    {
        return $this->price['per_km'] ?? 0;
    }

    public function getPricePerDayAttribute()
    {
        return $this->price['per_day'] ?? 0;
    }

    public function isAvailable()
    {
        return true; // placeholder
    }
}
