<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MessageTemplate extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'package_id',
        'whatsapp_text',
        'whatsapp_media',
        'email_subject',
        'email_body',
        'email_media',
    ];

    /**
     * Relationship: Each MessageTemplate belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Accessor: Get full URL for WhatsApp media
     */
    public function getWhatsappMediaAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    /**
     * Accessor: Get full URL for Email media
     */
    public function getEmailMediaAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    /**
     * Helper function to get WhatsApp message
     */
    public function getWhatsappMessage()
    {
        return [
            'text' => $this->whatsapp_text,
            'media' => $this->whatsapp_media, // automatically full URL
        ];
    }

    /**
     * Helper function to get Email message
     */
    public function getEmailMessage()
    {
        return [
            'subject' => $this->email_subject,
            'body' => $this->email_body,
            'media' => $this->email_media, // automatically full URL
        ];
    }
}
