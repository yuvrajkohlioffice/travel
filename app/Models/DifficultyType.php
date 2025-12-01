<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DifficultyType extends Model
{
    use HasFactory;

    // Table name (optional if plural of model name)
    protected $table = 'difficulty_types';

    // Mass assignable fields
    protected $fillable = [
        'name',
        'level',
    ];
}
