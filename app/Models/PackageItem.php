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
    public function scopeFilter($query, $request)
{
    // Search by vehicle name
    if ($request->filled('search')) {
        $query->where('vehicle_name', 'LIKE', '%' . $request->search . '%');
    }

    // Filter by package id
    if ($request->filled('package_id')) {
        $query->where('package_id', $request->package_id);
    }

    // Filter by car id
    if ($request->filled('car_id')) {
        $query->where('car_id', $request->car_id);
    }

    // Filter by person count
    if ($request->filled('person_count')) {
        $query->where('person_count', $request->person_count);
    }

    // Filter by room count
    if ($request->filled('room_count')) {
        $query->where('room_count', $request->room_count);
    }

    return $query;
}

}
