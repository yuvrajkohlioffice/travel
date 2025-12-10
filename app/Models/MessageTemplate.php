<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        
        'whatsapp_text',
        'whatsapp_media',
        'email_subject',
        'email_body',
        'email_media',
    ];

    // Relation (if needed)
    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
