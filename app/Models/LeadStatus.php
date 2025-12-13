<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    protected $table = 'lead_statuses';

    protected $fillable = [
        'company_id',
        'name',
        'color',
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    /**
     * Scope to fetch statuses that are global or belong to a specific company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('is_global', true)
              ->orWhere('company_id', $companyId);
        });
    }
}
