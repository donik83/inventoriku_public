<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Auth; // <-- Tambah jika belum
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    /**
     * Display the application dashboard.
     */
    public function index(): View
    {
        $user = Auth::user(); // Dapatkan pengguna semasa

        // Bina query asas dengan skop visibility
        $itemsQuery = Item::query();
        if (!$user->hasRole('Admin')) {
            $itemsQuery->where(function ($q) use ($user) {
                $q->where('is_private', false) // Item adalah umum (public)
                  ->orWhere('owner_user_id', $user->id); // ATAU item ini milik pengguna semasa
            });
        }

        // Dapatkan tarikh 90 hari dari sekarang
        $ninetyDaysLater = now()->addDays(90);

        // Query item yang visible, ada tarikh jaminan, dan akan tamat dalam 90 hari
        $expiringSoonItems = (clone $itemsQuery) // Guna clone dari query yg sudah diskop
        ->whereNotNull('warranty_expiry_date')
        ->whereBetween('warranty_expiry_date', [now(), $ninetyDaysLater])
        ->orderBy('warranty_expiry_date', 'asc') // Yang paling hampir tamat dahulu
        ->limit(5) // Hadkan kepada 5 untuk paparan dashboard
        ->get();

        // Kira statistik berdasarkan query yang telah ditapis
        // Guna clone() supaya query asal tidak terjejas untuk pengiraan lain
        $totalItems = (clone $itemsQuery)->count();
        $totalValue = (clone $itemsQuery)->sum('purchase_price');

        // Kiraan kategori (mungkin perlu join atau cara lain jika mahu tepat ikut skop)
        // Cara mudah: kira semua kategori, tapi mungkin tidak 100% tepat jika ada kategori tanpa item yang visible
        // Gantikan logik lama untuk $categoriesWithCount dengan ini:
        $categoriesWithCount = Category::withCount(['items' => function ($query) use ($user) {
            // Aplikasikan skop visibility yang sama di dalam subquery count
            if (!$user->hasRole('Admin')) {
                $query->where(function ($q) use ($user) {
                    $q->where('is_private', false)
                    ->orWhere('owner_user_id', $user->id);
                });
            }
        }])
        ->orderByDesc('items_count') // Susun ikut bilangan item (terbanyak dahulu)
        ->orderBy('name') // Susunan kedua ikut nama
        ->take(5) // Ambil 5 teratas
        ->get();
        // (Pilihan: Tapis $categoriesWithCount untuk buang yang count=0 jika perlu dalam view)

        // === LOGIK BARU: Item Kuantiti Rendah ===
        $lowQuantityThreshold = 3; // Tetapkan had rendah (cth: bawah 3)
        $lowQuantityItems = (clone $itemsQuery)
                ->where('quantity', '<', $lowQuantityThreshold)
                // ->where('quantity', '>', 0) // Nyahkomen jika mahu abaikan yg kuantiti 0
                ->orderBy('quantity', 'asc') // Papar yang paling kritikal dahulu
                ->limit(5) // Hadkan 5 di dashboard
                ->get(['id', 'name', 'quantity']); // Ambil data perlu sahaja
        // =========================================

        // === LOGIK BARU: Item Sedang Dipinjam ===
        $borrowedItemsDashboard = (clone $itemsQuery)
            ->where('status', 'Dipinjam') // Status spesifik
            ->with(['primaryImage', 'location']) // Muat data untuk paparan
            ->orderBy('updated_at', 'desc') // Ikut bila status dikemas kini
            ->limit(5) // Hadkan 5 di dashboard
            ->get();
        // =======================================

        // === LOGIK BARU: Kiraan Item Mengikut Status ===
        $statusCounts = (clone $itemsQuery) // Guna clone dari query yg sudah diskop
                        ->select('status', DB::raw('count(*) as total')) // Pilih status dan kira jumlah
                        ->groupBy('status') // Kumpulkan ikut status
                        ->orderBy('status') // Susun ikut nama status
                        ->pluck('total', 'status'); // Hasil: ['Dipinjam' => 2, 'Digunakan' => 10, ...]
        // ============================================

        // Tambah logik ini jika Tuan ada Top 5 Lokasi
        $locationsWithCount = Location::withCount(['items' => function ($query) use ($user) {
            // Aplikasikan skop visibility yang sama di dalam subquery count
            if (!$user->hasRole('Admin')) {
                $query->where(function ($q) use ($user) {
                    $q->where('is_private', false)
                    ->orWhere('owner_user_id', $user->id);
                });
            }
        }])
        ->orderByDesc('items_count') // Susun ikut bilangan item (terbanyak dahulu)
        ->orderBy('name') // Susunan kedua ikut nama
        ->take(5) // Ambil 5 teratas
        ->get();
        // (Pilihan: Tapis $locationsWithCount untuk buang yang count=0 jika perlu dalam view)

        // Dapatkan 5 item yang paling baru ditambah
        // Gantikan logik lama untuk recent items dengan ini:
        $recentItems = (clone $itemsQuery) // Guna clone untuk elak kesan query lain
            ->with(['category', 'location', 'primaryImage']) // Muatkan data perlu untuk paparan
            ->latest() // Susun ikut tarikh dicipta (terbaru dahulu)
            ->take(5)  // Ambil 5 teratas
            ->get();

        // Hantar semua data ke view 'dashboard'
        return view('dashboard', compact(
            'totalItems',
            'totalValue',
            'categoriesWithCount',
            'locationsWithCount',
            'recentItems',
            'expiringSoonItems',
            'lowQuantityItems',
            'borrowedItemsDashboard',
            'statusCounts'
        ));
    }
}
