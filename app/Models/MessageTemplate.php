<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Added
use Illuminate\Support\Facades\Auth;    // Added
use Illuminate\Support\Facades\Storage;

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

    /**
     * Booted method to apply Global Scope
     */
    protected static function booted()
    {
        static::addGlobalScope('company_isolation', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // If NOT Admin (Role 1), restrict by company through the Package
                if ($user->role_id !== 1) {
                    $builder->whereHas('package', function ($query) use ($user) {
                        $query->where('company_id', $user->company_id);
                    });
                }
            }
        });
    }

    /**
     * Relationship: Each MessageTemplate belongs to a Package
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /* -----------------------------------------------------------------
     |  ACCESSORS
     | ----------------------------------------------------------------- */

    public function getWhatsappMediaAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    public function getEmailMediaAttribute($value)
    {
        return $value ? Storage::url($value) : null;
    }

    /* -----------------------------------------------------------------
     |  HELPERS
     | ----------------------------------------------------------------- */

    public function getWhatsappMessage()
    {
        return [
            'text' => $this->whatsapp_text,
            'media' => $this->whatsapp_media,
        ];
    }

    public function getEmailMessage()
    {
        return [
            'subject' => $this->email_subject,
            'body' => $this->email_body,
            'media' => $this->email_media,
        ];
    }
}