<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupReason extends Model
{
    use HasFactory;

    /* ================= FILLABLE ================= */

    protected $fillable = ['company_id', 'name', 'remark', 'date', 'time', 'email_template', 'whatsapp_template', 'is_active', 'lead_status_id', 'is_global'];

    /* ================= CASTS ================= */

    protected $casts = [
        'remark' => 'boolean',
        'date' => 'boolean',
        'time' => 'boolean',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    /* ================= RELATION ================= */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /* ================= SCOPES ================= */

    /**
     * Only active reasons
     */
    public function leadStatus()
    {
        return $this->belongsTo(LeadStatus::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Company reasons with global fallback
     * - If company has reasons → only company
     * - Else → global
     */
    public function scopeCompanyOrGlobal($query, $companyId)
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('company_id', $companyId)->orWhere('is_global', true);
        });
    }

    /**
     * Only global reasons
     */
    public function scopeGlobal($query)
    {
        return $query->where('is_global', true);
    }

    /* ================= MUTATORS ================= */

    /**
     * If is_global = true → company_id must be NULL
     */
    public function setIsGlobalAttribute($value)
    {
        $this->attributes['is_global'] = (bool) $value;

        if ($value) {
            $this->attributes['company_id'] = null;
        }
    }

    /**
     * If company_id is set → is_global must be false
     */
    public function setCompanyIdAttribute($value)
    {
        $this->attributes['company_id'] = $value;

        if (!is_null($value)) {
            $this->attributes['is_global'] = false;
        }
    }
}
