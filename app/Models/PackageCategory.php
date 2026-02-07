<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageCategory extends Model
{
    use HasFactory;

    // Explicitly define the table name since it's singular (package_category)
    protected $table = 'package_category';

    /**
     * The attributes that are mass assignable.
     * We add 'company_id' and 'is_global' here.
     */
    protected $fillable = [
        'company_id',
        'name',
        'is_global',
    ];

    /**
     * The attributes that should be cast.
     * This ensures $category->is_global returns true/false instead of 1/0.
     */
    protected $casts = [
        'is_global' => 'boolean',
    ];

    /**
     * Get the company that owns the package category.
     * This links package_category.company_id to companies.id
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}