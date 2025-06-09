<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Request mungkin tidak perlu tapi standard
use Illuminate\View\View;
use App\Models\Item;
use App\Models\Category; // Walaupun tidak query terus, eager load perlukannya
use App\Models\ItemMovement;
use App\Models\User;
use App\Models\Location; // <-- Tambah
use Illuminate\Support\Facades\Auth; // <-- Pastikan use Auth ditambah/ada

class ReportController extends Controller
{
    /**
     * Paparkan halaman utama yang menyenaraikan semua laporan tersedia.
     */
    public function indexReports(): View
    {
        // Cipta array yang mengandungi butiran untuk setiap laporan
        // Ini memudahkan kita menambah laporan baru atau mengawal paparan di view
        $reports = [
            [
                'name' => 'Laporan Item Dipinjam',
                'description' => 'Menyenaraikan semua item inventori yang sedang dalam status "Dipinjam" beserta maklumat berkaitan.',
                'route' => route('reports.borrowedItems'),
                // 'permission' => 'view borrowed items report' // Contoh jika ada permission spesifik per laporan
            ],
            [
                'name' => 'Laporan Item Mengikut Lokasi',
                'description' => 'Membolehkan anda memilih lokasi dan melihat senarai semua item yang berada di lokasi tersebut.',
                'route' => route('reports.itemsByLocation'),
                // 'permission' => 'view location items report'
            ],
            // Tuan boleh tambah definisi laporan lain di sini pada masa hadapan
            // Contoh:
            // [
            //     'name' => 'Laporan Jaminan Hampir Tamat',
            //     'description' => 'Item yang jaminannya akan tamat tidak lama lagi.',
            //     'route' => route('reports.warrantyExpiry'), // Perlu cipta route ini
            //     'permission' => 'view warranty report'
            // ],
        ];

        // Hanya hantar laporan yang pengguna dibenarkan lihat (jika ada logik permission per laporan)
        // Buat masa ini, kita hantar semua dan kawal akses pada route laporan individu.
        // $availableReports = collect($reports)->filter(function ($report) {
        //     return auth()->user()->can($report['permission'] ?? 'view reports'); // Andaian ada permission 'view reports'
        // })->all();

        return view('reports.index', compact('reports')); // Hantar array $reports ke view
    }
    /**
    * Paparkan laporan item yang sedang dipinjam.
    */
    public function borrowedItems(): View
    {
        $user = Auth::user(); // Dapatkan pengguna semasa

        // Mulakan query pada Item
        $query = Item::with([ // Eager load data berkaitan
            'category',
            'itemMovements' => function ($query) {
                $query->where('movement_type', 'PINJAM')
                      ->with('user')
                      ->latest()
                      ->limit(1);
            }
        ])->where('status', 'Dipinjam'); // Syarat utama: item mesti berstatus 'Dipinjam'

        // === TAMBAH BLOK SKOP PRIVASI INI ===
        // Aplikasikan skop visibility jika pengguna BUKAN Admin
        if (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('is_private', false) // Item adalah umum (public)
                  ->orWhere('owner_user_id', $user->id); // ATAU item ini milik pengguna semasa
            });
        }
        // ====================================

        // Dapatkan hasil query yang telah ditapis & disusun
        $borrowedItems = $query->orderBy('name')->get();

        // Hantar data ke view
        return view('reports.borrowed-items', compact('borrowedItems'));

        // Nota: Jika Tuan guna status 'Sebahagian Dipinjam', tukar where('status','Dipinjam') di atas
    }

    /**
     * Paparkan laporan item mengikut lokasi yang dipilih.
     */
    public function itemsByLocation(Request $request): View
    {
        $locations = Location::orderBy('name')->get();

        $validated = $request->validate([
            'location_id' => 'nullable|integer|exists:locations,id'
        ]);
        $selectedLocationId = $validated['location_id'] ?? null;

        $items = collect();
        $selectedLocationName = null;

        if ($selectedLocationId) {
            $selectedLocation = Location::find($selectedLocationId);
            $selectedLocationName = $selectedLocation?->name;

            // Mulakan query dengan syarat lokasi
            $user = Auth::user(); // Dapatkan pengguna semasa
            $query = Item::with('category')
                        ->where('location_id', $selectedLocationId);

            // === TAMBAH BLOK SKOP PRIVASI INI JUGA ===
            // Aplikasikan skop visibility jika pengguna BUKAN Admin
            if (!$user->hasRole('Admin')) {
                $query->where(function ($q) use ($user) {
                    $q->where('is_private', false) // Item adalah umum (public)
                      ->orWhere('owner_user_id', $user->id); // ATAU item ini milik pengguna semasa
                });
            }
            // =========================================

            // Dapatkan hasil query
            $items = $query->orderBy('name')->get();
        }

        // 5. Hantar data ke view
        return view('reports.items-by-location', compact(
            'locations',            // Senarai semua lokasi untuk dropdown
            'selectedLocationId',   // ID lokasi yang sedang dipilih (jika ada)
            'selectedLocationName', // Nama lokasi yang dipilih (jika ada)
            'items'                 // Senarai item di lokasi terpilih (mungkin kosong)
        ));
    }
}
