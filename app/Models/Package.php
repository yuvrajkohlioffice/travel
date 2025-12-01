<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    protected $fillable = [
        'package_type_id',
        'package_category_id',
        'difficulty_type_id',
        'pickup_points',
        'package_name',
        'package_docs',
        'package_banner',
        'other_images',
        'package_days',
        'package_nights',
        'package_price',
        'altitude',
        'min_age',
        'max_age',
        'best_time_to_visit',
        'content',
    ];

    protected $casts = [
        'other_images' => 'array',
    ];

    // Relationships
    public function packageType()
    {
        return $this->belongsTo(PackageType::class);
    }

    public function packageCategory()
    {
        return $this->belongsTo(PackageCategory::class);
    }

    public function difficultyType()
    {
        return $this->belongsTo(DifficultyType::class);
    }

    public function cars()
    {
        return $this->belongsToMany(Car::class, 'package_items', 'package_id', 'car_id')
                    ->withPivot('custom_price', 'already_price')
                    ->withTimestamps();
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'package_items', 'package_id', 'hotel_id')
                    ->withPivot('custom_price', 'already_price')
                    ->withTimestamps();
    }

    public function pickupPoints()
    {
        return $this->hasMany(PickupPoint::class);
    }
    public function packageItems()
{
    return $this->hasMany(PackageItem::class);
}

}
