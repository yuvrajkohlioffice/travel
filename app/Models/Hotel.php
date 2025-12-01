<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    // Table name (optional, follows Laravel convention here)
    protected $table = 'hotels';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'type',
        'meal_included',
        'meal_type',
        'price',
    ];

    // Casts
    protected $casts = [
        'meal_included' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Many-to-Many relationship with Package
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_hotel', 'hotel_id', 'package_id')
                    ->withTimestamps();
    }
}
