<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'type', 'is_active', 'is_tax_applicable', 'tax_percentage', 'tax_name', 'tax_number', 'bank_name', 'account_name', 'account_number', 'ifsc_code', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_tax_applicable' => 'boolean',
        'tax_percentage' => 'decimal:2',
    ];

    /* =============================
       SCOPES
    ============================== */

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
