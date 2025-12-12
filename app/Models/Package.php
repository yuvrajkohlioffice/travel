<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'packages';

    protected $fillable = ['package_type_id', 'package_category_id', 'difficulty_type_id', 'pickup_points', 'package_name', 'package_docs', 'package_banner', 'other_images', 'package_days', 'package_nights', 'package_price', 'altitude', 'min_age', 'max_age', 'best_time_to_visit', 'content', 'company_id'];

    protected $casts = [
        'other_images' => 'array',
        'package_docs' => 'array',
    ];

    /* --------------------------
       RELATIONSHIPS
    --------------------------- */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

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
        return $this->belongsToMany(Car::class, 'package_items', 'package_id', 'car_id')->withPivot('custom_price', 'already_price')->withTimestamps();
    }

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class, 'package_items', 'package_id', 'hotel_id')->withPivot('custom_price', 'already_price')->withTimestamps();
    }

    public function pickupPoints()
    {
        return $this->hasMany(PickupPoint::class);
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class);
    }

    /* --------------------------
       ACCESSORS / ATTRIBUTES
    --------------------------- */

    public function getPackageBannerUrlAttribute(): ?string
    {
        return $this->package_banner ? asset('storage/' . $this->package_banner) : null;
    }

    public function getPackageDocsUrlAttribute(): array|string|null
    {
        if (!$this->package_docs) {
            return [];
        }

        return is_array($this->package_docs) ? array_map(fn($doc) => asset('storage/' . $doc), $this->package_docs) : asset('storage/' . $this->package_docs);
    }
    public function messageTemplate()
    {
        return $this->hasOne(MessageTemplate::class);
    }
    public function getOtherImagesUrlAttribute(): array
    {
        return collect($this->other_images ?? [])
            ->map(fn($img) => asset('storage/' . $img))
            ->toArray();
    }

    /* --------------------------
       SCOPES / PERFORMANCE
    --------------------------- */

    // Eager load relations to reduce queries
    public function scopeWithRelations($query)
    {
        return $query->with(['company:id,name', 'packageType:id,name', 'packageCategory:id,name', 'difficultyType:id,name', 'cars:id,name', 'hotels:id,name', 'pickupPoints:id,package_id,name']);
    }

    // Optional caching for heavy queries
    public static function cached($id)
    {
        return Cache::remember("package_{$id}", 60, fn() => self::withRelations()->find($id));
    }
}
