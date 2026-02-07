<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Added
use Illuminate\Support\Facades\Auth;    // Added
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'user_id', 'lead_id', 'package_id', 'package_items_id', 
        'issued_date', 'travel_start_date', 'primary_full_name', 
        'primary_email', 'primary_phone', 'primary_address', 
        'additional_travelers', 'total_travelers', 'adult_count', 
        'child_count', 'package_name', 'package_type', 'price_per_person', 
        'subtotal_price', 'discount_amount', 'tax_amount', 'final_price', 
        'additional_details'
    ];

    protected $casts = [
        'additional_travelers' => 'array',
        'issued_date' => 'date',
        'travel_start_date' => 'date',
        'subtotal_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'final_price' => 'decimal:2',
    ];

    /**
     * The "booted" method of the model.
     * Applies Global Scope for Company Isolation.
     */
    protected static function booted()
    {
        static::addGlobalScope('company_isolation', function (Builder $builder) {
            if (Auth::check()) {
                $user = Auth::user();

                // 1. If Role is Admin (1), allow access to ALL invoices
                if ($user->role_id === 1) {
                    return;
                }

                // 2. Otherwise, filter invoices where the CREATOR (User) is in the same company
                $builder->whereHas('user', function ($query) use ($user) {
                    $query->where('company_id', $user->company_id);
                });
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    public function packageItem()
    {
        return $this->belongsTo(PackageItem::class, 'package_items_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors & Helpers
    |--------------------------------------------------------------------------
    */

    public function getFormattedIssueDateAttribute()
    {
        return $this->issued_date ? Carbon::parse($this->issued_date)->format('M d, Y') : null;
    }

    public function getFormattedTravelStartDateAttribute()
    {
        return $this->travel_start_date ? Carbon::parse($this->travel_start_date)->format('M d, Y') : null;
    }

    public function routeNotificationForWhatsapp($notification)
    {
        // 1. Clean the phone number (remove spaces, dashes)
        // assuming you store phone_number or primary_phone
        // Note: Your fillable has 'primary_phone' but here you used 'this->phone_number'.
        // Ensure this matches your database column.
        
        $phoneField = $this->primary_phone ?? $this->phone_number; 
        
        $number = preg_replace('/[^0-9]/', '', $phoneField);
        
        // If you have a separate code field, clean it too. Otherwise assume it's included.
        $code = isset($this->phone_code) ? preg_replace('/[^0-9]/', '', $this->phone_code) : '';

        return $code . $number;
    }
}