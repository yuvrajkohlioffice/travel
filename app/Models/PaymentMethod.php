<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    // Fillable columns
    protected $fillable = [
        'name',
        'type',
        'company_id',
        'is_active',
        'is_tax_applicable',
        'tax_percentage',
        'tax_name',
        'tax_number',
        'bank_name',
        'account_name',
        'account_number',
        'ifsc_code',
        'description',
        'image_proof_required'
    ];

    // Casts for proper data types
    protected $casts = [
        'is_active' => 'boolean',
        'is_tax_applicable' => 'boolean',
        'tax_percentage' => 'decimal:2',
        'image_proof_required' => 'boolean',
    ];

    // Soft delete timestamp
    protected $dates = ['deleted_at'];

    /* =============================
       SCOPES
    ============================== */

    // Only active methods
    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

    /* =============================
       RELATIONSHIPS
    ============================== */

    // Each method belongs to a company
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
