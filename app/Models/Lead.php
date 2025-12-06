<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'district',
        'country',
        'phone_code',
        'phone_number',
        'city',
        'client_category',
        'lead_status',
        'lead_source',
        'website',
        'package_id',
        'inquiry_text',
        'status',
        'user_id',
        'people_count',
        'child_count', // Add this
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Relationship with Package (optional)
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function followups()
    {
        return $this->hasMany(Followup::class);
    }

    /**
     * Relationship with LeadUser (multiple assigned users)
     */
    public function assignedUsers()
    {
        return $this->hasMany(LeadUser::class);
    }
public function latestAssignedUser()
{
    return $this->hasOne(LeadUser::class)->latestOfMany();
}

    /**
     * User who created the lead
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function views()
    {
        return $this->hasMany(LeadView::class);
    }
    public function lastFollowup()
    {
        return $this->hasOne(Followup::class)->latestOfMany();
    }
}
