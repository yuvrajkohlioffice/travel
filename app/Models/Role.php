<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /* -----------------------------------------------------------------
     |  SCOPES
     | ----------------------------------------------------------------- */

    /**
     * Scope to filter roles visible to the current user.
     * * Usage: Role::accessible()->get();
     * * Logic:
     * - If User is Admin (ID 1): Show All Roles.
     * - If User is NOT Admin: Hide Role ID 1 (Admin Role).
     */
    public function scopeAccessible($query)
    {
        // Ensure user is logged in
        if (Auth::check()) {
            $currentUser = Auth::user();

            // If the current user is NOT an Admin (ID 1)
            // exclude the Admin role (ID 1) from the results.
            if ($currentUser->role_id !== 1) {
                return $query->where('id', '!=', 1);
            }
        }

        return $query;
    }

    /* -----------------------------------------------------------------
     |  RELATIONSHIPS
     | ----------------------------------------------------------------- */

    /**
     * Relationship: A Role has many Users
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relationship: A Role has many Routes (Permissions)
     */
    public function routes()
    {
        return $this->hasMany(RoleRoute::class, 'role_id');
    }
}