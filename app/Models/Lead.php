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
        'website','package_id','inquiry_text'
    ];
    /**
     * Relationship with Package (optional)
     */
    protected $casts = [
    'created_at' => 'datetime',
];

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
    public function assignedUsers()
    {
        return $this->hasMany(LeadUser::class);
    }
}
