<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse; // <-- Tambah untuk JSON Response
use Illuminate\Support\Facades\Gate; // <-- Tambah use Gate

class ItemLookupController extends Controller
{
    /**
     * Cari item berdasarkan kod bar dan kembalikan hasil sebagai JSON.
     */
    public function lookupByBarcode(Request $request): JsonResponse
    {
        // Validasi input kod bar
        $validated = $request->validate([
            'barcode' => 'required|string|max:100' // Laraskan max jika perlu
        ]);
        $barcode = $validated['barcode'];

        // Cari item berdasarkan barcode_data
        $item = Item::where('barcode_data', $barcode)->first();

        // === SEMAK KEBENARAN VIEW SELEPAS ITEM DIJUMPAI ===
        if ($item && Gate::allows('view', $item)) {
            // Jika item jumpa DAN pengguna dibenarkan melihatnya
            return response()->json([
                'status' => 'found',
                'item_id' => $item->id,
                'show_url' => route('items.show', $item->id) // URL ke halaman butiran
            ]);
        } else {
            // Jika item TIDAK jumpa ATAU pengguna TIDAK DIBENARKAN melihatnya
            // Anggap ia sebagai 'not_found' untuk pengguna ini
            return response()->json([
                'status' => 'not_found',
                // URL ke halaman tambah baru, hantar kod bar sebagai parameter query
                'create_url' => route('items.create', ['barcode_data' => $barcode])
            ]);
        }
    }

    /**
     * Semak jika pengguna semasa boleh akses item dari QR & pulangkan URL pergerakan.
     */
    public function checkQrItemAccess(Item $item): JsonResponse
    {
        // Guna policy 'view' untuk semak kebenaran akses
        if (Gate::allows('view', $item)) {
            // Jika dibenarkan, pulangkan status 'allowed' dan URL ke borang pergerakan
            return response()->json([
                'status' => 'allowed',
                'move_url' => route('movements.create', $item->id)
            ]);
        } else {
            // Jika tidak dibenarkan, pulangkan status 'denied'
            return response()->json([
                'status' => 'denied',
                'message' => 'Anda tidak dibenarkan merekodkan pergerakan untuk item ini.'
            ], 403); // Kembalikan status 403 Forbidden juga
        }
    }
}
