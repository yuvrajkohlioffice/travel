<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','company_name','email','district','country','phone_code',
        'phone_number','city','client_category','lead_status','lead_source',
        'website','package_id','inquiry_text','user_id'  // Added user_id
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

}
