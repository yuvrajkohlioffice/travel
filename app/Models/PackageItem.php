<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $table = 'package_items';

    protected $fillable = [
        'package_id',
        'car_id',
        'person_count',
        'vehicle_name',
        'room_count',
        'standard_price',
        'deluxe_price',
        'luxury_price',
        'premium_price',
    ];

    /**
     * Relationship: PackageItem belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Relationship: PackageItem belongs to a Car
     */
    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}
