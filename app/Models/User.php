<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasProfilePhoto, Notifiable, TwoFactorAuthenticatable;

    protected $fillable = [
        'name', 'email', 'password', 'role_id', 'company_id', 
        'whatsapp_api_key', 'status',
        'smtp_host', 'smtp_port', 'smtp_encryption', 
        'smtp_username', 'smtp_password', 'smtp_from_email', 'smtp_from_name'
    ];

    protected $hidden = [
        'password', 'remember_token', 'two_factor_recovery_codes', 
        'two_factor_secret', 'smtp_password'
    ];

    protected $appends = ['profile_photo_url'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'password' => 'hashed',
        'smtp_port' => 'integer',
    ];

    /* -----------------------------------------------------------------
     |  🚨 REMOVED GLOBAL SCOPE TO PREVENT RECURSION
     |  ✅ ADDED LOCAL SCOPE BELOW
     | ----------------------------------------------------------------- */

    /**
     * Scope the query to only include users accessible by the current user.
     * Usage: User::accessible()->get();
     */
    public function scopeAccessible($query)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // If NOT Admin (Role 1), filter by Company
            if ($user->role_id !== 1 && $user->company_id) {
                return $query->where('company_id', $user->company_id);
            }
        }
        return $query;
    }

    /* -----------------------------------------------------------------
     |  RELATIONSHIPS
     | ----------------------------------------------------------------- */
    public function company() { return $this->belongsTo(Company::class); }
    public function role() { return $this->belongsTo(Role::class); }
    public function leads() { return $this->hasMany(Lead::class, 'user_id'); }

    /* -----------------------------------------------------------------
     |  ACCESSORS
     | ----------------------------------------------------------------- */
    public function setSmtpPasswordAttribute($value) { $this->attributes['smtp_password'] = $value ? encrypt($value) : null; }
    
    public function getSmtpPasswordAttribute($value) {
        if (!$value) return null;
        try { return decrypt($value); } catch (\Exception $e) { return $value; }
    }

    public function routeNotificationForWhatsapp() { return $this->phone_number; }
}