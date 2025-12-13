<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowupReason extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'remark',
        'date',
        'time',
        'email_template',
        'whatsapp_template',
        'is_active',
        'is_global',
    ];

    // Relationship: FollowupReason belongs to Company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Mutator for is_global
     * If a reason is global, company_id is set to null
     */
    public function setIsGlobalAttribute($value)
    {
        $this->attributes['is_global'] = $value ? 1 : 0;

        if ($value) {
            $this->attributes['company_id'] = null;
        }
    }

    /**
     * Mutator for company_id
     * If company_id is set, is_global is automatically false
     */
    public function setCompanyIdAttribute($value)
    {
        $this->attributes['company_id'] = $value;
        if ($value) {
            $this->attributes['is_global'] = 0;
        }
    }
}
