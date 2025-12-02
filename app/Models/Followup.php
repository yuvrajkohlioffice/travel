<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'reason',
        'remark',
        'next_followup_date',
        'next_followup_time',
        'last_followup_date',
    ];
    protected $dates = ['next_followup_date', 'created_at', 'updated_at'];


    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
