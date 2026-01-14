<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'company_id',
        'whatsapp_api_key',
        'status',

        // ✅ SMTP fields
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'smtp_from_email',
        'smtp_from_name',
    ];

    /**
     * The attributes that should be hidden for arrays.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',

        // 🔐 hide sensitive smtp password
        'smtp_password',
    ];

    /**
     * The accessors to append to the model's array form.
     */
    protected $appends = ['profile_photo_url'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',

        // ✅ SMTP casts
        'smtp_port' => 'integer',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    /**
     * User belongs to a Company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * User belongs to a Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /* -----------------------------------------------------------------
     | 🔐 SMTP Password Encryption / Decryption
     |------------------------------------------------------------------*/

    /**
     * Encrypt SMTP password before saving
     */
    public function setSmtpPasswordAttribute($value)
    {
        $this->attributes['smtp_password'] = $value ? encrypt($value) : null;
    }

    public function getSmtpPasswordAttribute($value)
    {
        if (!$value) {
            return null;
        }

        try {
            return decrypt($value);
        } catch (\Exception $e) {
            return $value; // backward compatibility
        }
    }
    public function routeNotificationForWhatsapp()
    {
        return $this->phone_number; // Replace with your actual database column name
    }
}
