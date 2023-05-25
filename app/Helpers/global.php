<?php
/**
 * Some globally available constants and functions
 */

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

/**
 * Check if a user is a super admin
 *
 * @param User|null $user
 * @return bool
 */
function is_superadmin(?User $user = null): bool
{
    if (is_null($user)){
        return false;
    }
    return $user->id == config('app.admin_user_id');
}

/**
 * Get the logged in user or throw an exception
 *
 * @return User|Authenticatable
 */
function user(): User|Authenticatable
{
    return Auth::user() ?? throw new \Illuminate\Validation\UnauthorizedException("Not logged in");
}
