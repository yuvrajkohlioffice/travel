<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'user_id',
        'lead_id',
        'package_id',
        'package_items_id',
        'issued_date',
        'travel_start_date',
        'primary_full_name',
        'primary_email',
        'primary_phone',
        'primary_address',
        'additional_travelers',
        'total_travelers',
        'adult_count',
        'child_count',
        'package_name',
        'package_type',
        'price_per_person',
        'subtotal_price',
        'discount_amount',
        'tax_amount',
        'final_price',
        'additional_details',
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

    public function packageItem()
    {
        return $this->belongsTo(PackageItem::class, 'package_items_id');
    }
}
