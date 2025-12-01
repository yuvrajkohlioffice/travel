<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $table = 'packages';

    // Mass assignable fields
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

    // Cast JSON field
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
}
