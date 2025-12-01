<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackageItem extends Model
{
    use HasFactory;

    protected $table = 'package_items';

    protected $fillable = [
        'package_id',
        'car_id',
        'hotel_id',
        'custom_price',
        'already_price',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
