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
     * Relationships
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'package_items_id');
    }

    /**
     * Scope for filtering
     */
    public function scopeFilter($query, $request)
    {
        if ($request->filled('search')) {
            $query->where('vehicle_name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->filled('package_id')) {
            $query->where('package_id', $request->package_id);
        }

        if ($request->filled('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        if ($request->filled('person_count')) {
            $query->where('person_count', $request->person_count);
        }

        if ($request->filled('room_count')) {
            $query->where('room_count', $request->room_count);
        }

        return $query;
    }
}
