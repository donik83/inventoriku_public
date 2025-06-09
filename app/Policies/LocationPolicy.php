<?php

namespace App\Policies;

use App\Models\Location;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocationPolicy
{
    public function viewAny(User $user): bool { return true; }
    public function view(User $user, Location $location): bool { return true; }
    public function create(User $user): bool { return true; }

    public function update(User $user, Location $location): bool
    {
        return $user->id === $location->owner_user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, Location $location): bool
    {
        if ($location->items()->count() > 0 && !$user->hasRole('Admin')) {
            return false;
        }
        return $user->id === $location->owner_user_id || $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Location $location): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Location $location): bool
    {
        return false;
    }
}
