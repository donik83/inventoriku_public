<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    // Semua boleh lihat senarai
    public function viewAny(User $user): bool
    {
        return true;
    }

    // Semua boleh lihat butiran individu
    public function view(User $user, Category $category): bool
    {
        return true;
    }

    // Semua pengguna log masuk boleh cipta
    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Category $category): bool
    {
        // Hanya pemilik atau Admin boleh edit
        return $user->id === $category->owner_user_id || $user->hasRole('Admin');
    }

    public function delete(User $user, Category $category): bool
    {
        // Hanya pemilik atau Admin boleh padam DAN kategori itu tiada item
        if ($category->items()->count() > 0 && !$user->hasRole('Admin')) {
            // Jika ada item & bukan Admin, tidak boleh padam terus
            // Admin boleh override ini, tapi logic di controller akan sekat jika ada item
            return false;
        }
        return $user->id === $category->owner_user_id || $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}
