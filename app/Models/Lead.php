<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes; // ✅ Import SoftDeletes

class Lead extends Model
{
    use HasFactory, SoftDeletes;

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
        'child_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // ================= Relationships =================

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }

    public function followups()
    {
        return $this->hasMany(Followup::class);
    }

    public function lastFollowup()
    {
        return $this->hasOne(Followup::class)->latestOfMany();
    }

    public function assignedUsers()
    {
        return $this->hasMany(LeadUser::class);
    }

    public function latestAssignedUser()
    {
        return $this->hasOne(LeadUser::class)->latestOfMany();
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function views()
    {
        return $this->hasMany(LeadView::class);
    }

    // ================= Optional Accessors =================
    // You can get counts directly without loading relations separately

    public function getFollowupsCountAttribute()
    {
        return $this->followups()->count();
    }

    public function getAssignedUsersCountAttribute()
    {
        return $this->assignedUsers()->count();
    }

    public function getViewsCountAttribute()
    {
        return $this->views()->count();
    }


public function invoice()
{
    return $this->hasOne(Invoice::class);
}

    // Example for last followup date
    public function getLastFollowupDateAttribute()
    {
        return optional($this->lastFollowup)->created_at;
    }
}
