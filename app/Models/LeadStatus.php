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
        'order_by', // ✅ add this
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_global' => 'boolean',
        'order_by' => 'integer',
    ];
    public function followupReasons()
    {
        return $this->hasMany(FollowupReason::class);
    }

    /**
     * Scope to fetch statuses that are global or belong to a specific company
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('is_global', true)->orWhere('company_id', $companyId);
        });
    }

    /**
     * Scope for default ordering
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_by')->orderBy('id');
    }
}
