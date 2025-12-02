<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadUser extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'lead_user';

    // Mass assignable fields
    protected $fillable = [
        'lead_id',
        'user_id',
        'assigned_by',
    ];

    // Relationships
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
