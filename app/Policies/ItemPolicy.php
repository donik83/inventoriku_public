<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;
//use Illuminate\Auth\Access\Response;
//use Illuminate\Auth\Access\HandlesAuthorization; // Atau Response
use Illuminate\Support\Facades\Log; // <-- TAMBAH use statement ini

class ItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Andaikan semua pengguna log masuk boleh lihat senarai item
        return true;
        // Atau guna permission: return $user->can('view items');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Item $item): bool
    {
        //Log::info("ItemPolicy@view dipanggil: User ID = {$user->id}, Item ID = {$item->id}");
        // Andaikan semua pengguna log masuk boleh lihat butiran semua item buat masa ini
        // Kita akan tambah logik visibility keluarga di sini nanti jika perlu
            // Benarkan jika item TIDAK private (umum)
        if (!$item->is_private) {
            return true;
        }

        // Jika item private, hanya benarkan Pemilik atau Admin
        return $user->id === $item->owner_user_id || $user->hasRole('Admin');
        // Atau guna permission: return $user->can('view items');
        // Atau mungkin: return $user->id === $item->owner_user_id || $user->hasRole('Admin'); jika butiran terhad?
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Andaikan semua pengguna log masuk boleh cipta item baru
        return true;
        // Atau guna permission: return $user->can('create items');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Item $item): bool
    {
        // Benarkan jika pengguna adalah pemilik item ATAU pengguna ialah Admin
        return $user->id === $item->owner_user_id || $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Item $item): bool
    {
        // Guna logik yang sama: hanya pemilik atau Admin boleh padam
        return $user->id === $item->owner_user_id || $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can restore the model.
     * (Hanya relevan jika Tuan guna Soft Deletes)
     */
    public function restore(User $user, Item $item): bool
    {
        // Biasanya hanya Admin
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     * (Hanya relevan jika Tuan guna Soft Deletes)
     */
    public function forceDelete(User $user, Item $item): bool
    {
        // Biasanya hanya Admin
        return $user->hasRole('Admin');
    }
}
