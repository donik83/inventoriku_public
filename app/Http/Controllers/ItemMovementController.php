<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Location; // <-- Tambah
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ItemMovement; // <-- Tambah
use Illuminate\Http\RedirectResponse; // <-- Tambah
use Illuminate\Support\Facades\Auth; // <-- Tambah untuk dapatkan ID pengguna log masuk
use Illuminate\Support\Facades\DB; // <-- Import DB untuk transaction (pilihan tapi baik)

class ItemMovementController extends Controller
{
    /**
     * Paparkan halaman untuk memilih item bagi merekod pergerakan.
     */
    public function selectItem(Request $request): View
    {
        $user = Auth::user(); // Dapatkan pengguna semasa
        $query = Item::query(); // Mulakan query

        // === TAMBAH BLOK SKOP PRIVASI INI ===
        // Aplikasikan skop visibility jika pengguna BUKAN Admin
        if (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('is_private', false) // Item adalah umum (public)
                ->orWhere('owner_user_id', $user->id); // ATAU item ini milik pengguna semasa
            });
        }

        // Dapatkan searchTerm dari request (akan jadi null jika tiada)
        $searchTerm = $request->input('search');

        // ====================================

        // Aplikasikan carian jika searchTerm wujud
        if ($searchTerm) { // Semak jika ada nilai
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('serial_number', 'like', "%{$searchTerm}%")
                  ->orWhere('barcode_data', 'like', "%{$searchTerm}%");
            });
        }

        // Ambil item, susun ikut nama, guna paginasi ringkas
        // Tuan boleh tambah with('category', 'location') jika mahu papar dalam jadual pilihan
        $items = $query->orderBy('name')->paginate(15)->withQueryString();

        // Pulangkan view baru (kita akan cipta view ini sebentar lagi)
        return view('movements.select-item', compact('items', 'searchTerm'));
    }

    /**
     * Paparkan borang untuk merekod pergerakan item baru.
     * Menerima Item yang dipilih melalui Route Model Binding.
     */
    public function create(Item $item): View
    {
        // Dapatkan semua lokasi untuk dropdown 'Lokasi Baru'
        $locations = Location::orderBy('name')->get();

        // Definisikan jenis pergerakan yang dibenarkan
        $movementTypes = [
            'PINDAH',
            'PINJAM',
            'PULANG',
            'GUNA',
            'TAMBAH KUANTITI',
            'SELENGGARA/BAIKI',
            'HANTAR SERVIS/BAIKI',
            'SELESAI SERVIS/BAIKI',
            'SAHKAN LOKASI',
            'LARAS STOK', // <-- TAMBAH INI
            'ROSAK',
            'HILANG',
            'LUPUS',
        ];
        // Pastikan susunan ini adalah susunan yang Tuan mahu ia muncul dalam dropdown
        // Nota: Mungkin lebih baik letak ini dalam config atau Model jika ia tetap

        // Paparkan view borang, hantar data item, lokasi, dan jenis pergerakan
        return view('movements.create', compact(
            'item',
            'locations',
            'movementTypes'
        ));
    }

    /**
     * Simpan rekod pergerakan baru dan kemas kini item asal jika perlu.
     */
    public function store(Request $request, Item $item): RedirectResponse
    {
        // 1. Tentukan jenis pergerakan DAHULU (perlu untuk logik peraturan)
        // Ambil terus dari input, belum divalidasi jenisnya lagi di sini
        $type = $request->input('movement_type');

        // 2. Definisikan peraturan validasi asas
        $rules = [
            // Sertakan validasi untuk type di sini juga
            'movement_type' => 'required|string|in:PINDAH,PINJAM,PULANG,GUNA,SELENGGARA/BAIKI,HANTAR SERVIS/BAIKI,SELESAI SERVIS/BAIKI,TAMBAH KUANTITI,SAHKAN LOKASI,LARAS STOK,ROSAK,HILANG,LUPUS',
            'quantity_moved' => 'nullable|integer|min:0',
            'to_location_id' => 'nullable|integer|exists:locations,id',
            'notes' => 'nullable|string|max:1000',
        ];

        // 3. Tambah/Ubah Suai Peraturan Berdasarkan Jenis Pergerakan
        if ($type === 'GUNA') {
            $rules['quantity_moved'] = 'required|integer|min:1|max:' . $item->quantity;
        } elseif ($type === 'TAMBAH KUANTITI') {
            $rules['quantity_moved'] = 'required|integer|min:1';
        } elseif ($type === 'LARAS STOK') {
            $rules['quantity_moved'] = 'required|integer|min:0';
            $rules['notes'] = 'required|string|max:1000';
        } elseif (in_array($type, ['PINDAH', 'PULANG', 'SELESAI SERVIS/BAIKI'])) {
            // Jadikan lokasi destinasi wajib
            $rules['to_location_id'] = 'required|integer|exists:locations,id';
        }
        // Tambah peraturan bersyarat lain jika perlu

        // 4. Laksanakan validasi dengan SEMUA peraturan yang relevan
        // Hasilnya ($validatedData) HANYA mengandungi data yang lulus validasi
        $validatedData = $request->validate($rules);

        // Ambil nilai yang telah disahkan untuk digunakan dalam logik
        // $type sudah disahkan dalam $validatedData['movement_type']
        $quantityMoved = $validatedData['quantity_moved'] ?? null;
        $toLocationId = $validatedData['to_location_id'] ?? null;

        $updateMessage = 'Pergerakan item berjaya direkodkan!';
        $itemUpdated = false;

        // 5. Gunakan transaction (Try...Catch tidak diperlukan jika hanya guna transaction)
        DB::transaction(function () use ($item, $validatedData, $type, $quantityMoved, $toLocationId, &$updateMessage, &$itemUpdated) {

            // 6. Sediakan data untuk rekod pergerakan
            $movementData = $validatedData; // Gunakan terus hasil validasi
            $movementData['item_id'] = $item->id;
            $movementData['user_id'] = Auth::id();

            // 7. Cipta rekod pergerakan baru
            ItemMovement::create($movementData);

            // 8. Logik Kemas Kini Item Asal (Sama seperti sebelum ini)
            if ($type === 'PINDAH') {
                if ($toLocationId && $item->location_id != $toLocationId) { // toLocationId sudah divalidasi wajib
                    $item->location_id = $toLocationId; $itemUpdated = true;
                    $updateMessage = 'Item berjaya dipindahkan.';
                } else { $updateMessage = 'Pergerakan PINDAH direkodkan (lokasi sama).'; } // Kes lokasi sama
            } elseif ($type === 'GUNA') {
                $item->quantity -= $quantityMoved; // Kuantiti sudah divalidasi cukup
                if ($item->quantity <= 0) { $item->quantity = 0; $item->status = 'Habis Digunakan'; }
                $itemUpdated = true;
                $updateMessage = "Penggunaan {$quantityMoved} unit item berjaya direkodkan.";
            } elseif ($type === 'PULANG') {
                $item->location_id = $toLocationId; // Lokasi sudah divalidasi wajib
                if ($item->status === 'Dipinjam') { $item->status = 'Digunakan'; }
                $itemUpdated = true;
                $updateMessage = 'Item berjaya dipulangkan ke lokasi.';
            } elseif ($type === 'PINJAM') {
                $item->status = 'Dipinjam'; $item->location_id = null; $itemUpdated = true;
                $updateMessage = 'Item berjaya direkodkan sebagai dipinjam.';
            } elseif ($type === 'TAMBAH KUANTITI') {
                $item->quantity += $quantityMoved; // Kuantiti sudah divalidasi wajib > 0
                $itemUpdated = true;
                $updateMessage = "Kuantiti item berjaya ditambah sebanyak {$quantityMoved} unit.";
            } elseif ($type === 'LARAS STOK') {
                $item->quantity = $quantityMoved; // Kuantiti baru sudah divalidasi wajib & min 0
                $itemUpdated = true;
                $updateMessage = 'Kuantiti item berjaya dilaraskan kepada ' . $quantityMoved . '.';
            } elseif ($type === 'ROSAK') {
                $item->status = 'Rosak'; $itemUpdated = true;
                $updateMessage = 'Item berjaya direkodkan sebagai rosak.';
            } elseif ($type === 'HILANG') {
                $item->status = 'Hilang'; $item->location_id = null; $itemUpdated = true;
                $updateMessage = 'Item berjaya direkodkan sebagai hilang.';
            } elseif ($type === 'LUPUS') {
                $item->status = 'Dilupuskan'; $item->location_id = null; $item->quantity = 0; $itemUpdated = true;
                $updateMessage = 'Item berjaya direkodkan sebagai dilupuskan.';
            } elseif ($type === 'HANTAR SERVIS/BAIKI') {
                $item->status = 'Dihantar Servis'; $item->location_id = null; $itemUpdated = true;
                $updateMessage = 'Status item dikemas kini: Dihantar Servis/Baiki.';
            } elseif ($type === 'SELESAI SERVIS/BAIKI') {
                $item->status = 'Digunakan'; $item->location_id = $toLocationId; // Lokasi sudah divalidasi wajib
                $itemUpdated = true;
                $updateMessage = 'Status item dikemas kini: Selesai Servis/Baiki.';
            } elseif ($type === 'SELENGGARA/BAIKI') {
                $updateMessage = 'Rekod penyelenggaraan/pembaikan berjaya disimpan.';
            } elseif ($type === 'SAHKAN LOKASI') {
                $updateMessage = 'Rekod pengesahan lokasi berjaya disimpan.';
            }

            // 9. Simpan perubahan pada item JIKA ada
            if ($itemUpdated) {
                $item->save();
            }
        }); // Akhir DB::transaction


        // 10. Redirect ke halaman butiran item dengan mesej yang sesuai
        return redirect()->route('items.show', $item->id)
                        ->with('success', $updateMessage);
    } // Akhir kaedah store()

}
