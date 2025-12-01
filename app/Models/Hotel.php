<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'type',
        'meal_included',
        'meal_type',
        'price',
    ];

    protected $casts = [
        'meal_included' => 'boolean',
        'price' => 'decimal:2',
    ];

    /**
     * Many-to-Many relationship with Package through package_items
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class, 'package_items', 'hotel_id', 'package_id')
                    ->withPivot('custom_price', 'already_price')
                    ->withTimestamps();
    }
}
