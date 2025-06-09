<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\Auth; // Untuk dapatkan ID Admin jika perlu

class ItemTransferController extends Controller
{
    /**
     * Paparkan borang untuk pindah pemilik item.
     */
    public function showForm(): View
    {
        // Dapatkan semua item (mungkin dengan paginasi jika terlalu banyak)
        // dan semua pengguna (untuk pilihan pemilik baru)
        // Admin boleh lihat semua item
        $items = Item::orderBy('name')->get(['id', 'name', 'owner_user_id']); // Ambil owner_user_id untuk rujukan
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.items.transfer-ownership-form', compact('items', 'users'));
    }

    /**
     * Proses pemindahan pemilik item.
     */
    public function transfer(Request $request): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'item_id' => 'required|integer|exists:items,id',
            'new_owner_user_id' => 'required|integer|exists:users,id',
        ]);

        // Dapatkan item
        $item = Item::find($validated['item_id']);
        $newOwner = User::find($validated['new_owner_user_id']);

        if (!$item || !$newOwner) {
            return back()->with('error', 'Item atau Pemilik Baru tidak ditemui.');
        }

        // (Pilihan) Log pergerakan/perubahan jika perlu
        // Contoh: Rekod pergerakan 'TUKAR PEMILIK'
        // $item->itemMovements()->create([
        //     'user_id' => Auth::id(), // Admin yang melakukan
        //     'movement_type' => 'TUKAR PEMILIK',
        //     'quantity_involved' => $item->quantity, // Atau 0 jika tidak relevan
        //     'notes' => 'Pemilik ditukar dari (ID: ' . $item->owner_user_id . ') kepada ' . $newOwner->name . ' (ID: ' . $newOwner->id . ') oleh Admin.',
        //     'movement_date' => now(),
        //     // 'to_location_id' tidak relevan di sini
        // ]);

        // Tukar pemilik
        $item->owner_user_id = $validated['new_owner_user_id'];
        // Jika Tuan mahu item private pemilik lama jadi public selepas tukar (atau sebaliknya)
        // $item->is_private = true; // Contoh: jadikan public selepas tukar
        $item->save();

        return redirect()->route('admin.items.transfer.form') // Kembali ke borang transfer atau senarai item
                         ->with('success', 'Pemilik untuk item "' . $item->name . '" telah berjaya ditukar kepada "' . $newOwner->name . '".');
    }
}