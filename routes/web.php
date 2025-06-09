<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController; // Pastikan use statement ada
use App\Http\Controllers\CategoryController; // <-- TAMBAH
use App\Http\Controllers\LocationController; // <-- TAMBAH
use Illuminate\Support\Facades\Auth; // <-- TAMBAH IMPORT INI
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemMovementController;
use App\Http\Controllers\QrScannerController;
use App\Http\Controllers\ReportController; // <-- Tambah ini
use App\Http\Controllers\Admin\UserController as AdminUserController; // <-- TAMBAH Use statement ini di atas
use App\Http\Controllers\Admin\RoleController as AdminRoleController; // <-- TAMBAH INI
use App\Http\Controllers\Admin\PermissionController as AdminPermissionController; // <-- TAMBAH INI
use App\Http\Controllers\FeedbackController; // Controller baru
use App\Http\Controllers\Admin\FeedbackController as AdminFeedbackController; // Controller baru
use App\Http\Controllers\Admin\ItemTransferController;

Route::get('/', function () {
    if (Auth::check()) {
        // Jika pengguna sudah log masuk, halakan ke senarai item
        return redirect()->route('items.index');
        // Alternatif: Halakan ke dashboard jika Tuan mahu gunakannya
        // return redirect()->route('dashboard');
    }
    // Jika pengguna belum log masuk, halakan ke halaman login
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // CRUD Routes - PINDAHKAN/TAMBAH DI SINI
    Route::resource('items', ItemController::class);
    Route::resource('categories', CategoryController::class); // <-- Guna nama permission ini
    Route::resource('locations', LocationController::class); // <-- Guna nama permission ini

    // Routes untuk Pergerakan Item
    Route::get('/movements/select-item', [ItemMovementController::class, 'selectItem'])->name('movements.selectItem'); // Halaman pilih item
    Route::get('/movements/create/{item}', [ItemMovementController::class, 'create'])->name('movements.create'); // Papar borang selepas item dipilih
    Route::post('/movements/{item}', [ItemMovementController::class, 'store'])->name('movements.store'); // Simpan rekod pergerakan

    Route::get('/items/{item}/qrcode', [ItemController::class, 'generateQrCode'])->name('items.qrcode');

    Route::get('/scan', [QrScannerController::class, 'index'])->name('scanner.index');

    // Routes untuk Laporan
    Route::get('/reports', [ReportController::class, 'indexReports'])->name('reports.index'); // <-- HALAMAN UTAMA LAPORAN
    Route::get('/reports/borrowed-items', [ReportController::class, 'borrowedItems'])->name('reports.borrowedItems');
    Route::get('/reports/items-by-location', [ReportController::class, 'itemsByLocation'])->name('reports.itemsByLocation');
    // Tambah route laporan lain di sini nanti

    Route::post('/barcode-lookup', [App\Http\Controllers\ItemLookupController::class, 'lookupByBarcode'])->name('barcode.lookup');
    Route::post('/qr-item-check/{item}', [App\Http\Controllers\ItemLookupController::class, 'checkQrItemAccess'])->name('qr.check');

    // Routes untuk Maklum Balas Pengguna
    Route::get('/feedback/create', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

    // === ADMIN ROUTES ===
    Route::prefix('admin') // Semua route dalam group ini bermula dengan /admin/...
         ->middleware('role:Admin') // Hanya role 'Admin' boleh akses
         ->name('admin.') // Nama route akan bermula dengan admin. (cth: admin.users.index)
         ->group(function () {

            // Route Resource untuk Pengurusan Pengguna
            Route::resource('users', AdminUserController::class);
            Route::resource('roles', AdminRoleController::class); // <-- TAMBAH INI
            Route::resource('permissions', AdminPermissionController::class)->only(['index']); // <-- TAMBAH INI (hanya index)
            Route::resource('feedback', AdminFeedbackController::class)->only(['index', 'destroy']);
            // TAMBAH ROUTE INI:
            Route::post('/users/{user}/verify', [AdminUserController::class, 'markAsVerified'])->name('users.verify');
            Route::post('/feedback/{feedback}/reply', [AdminFeedbackController::class, 'storeReply'])->name('feedback.reply');
            
            // Route untuk Pindah Pemilik Item
            Route::get('/items/transfer-ownership', [ItemTransferController::class, 'showForm'])->name('items.transfer.form');
            Route::post('/items/transfer-ownership', [ItemTransferController::class, 'transfer'])->name('items.transfer.submit');
            // Nanti kita tambah route untuk Roles & Permissions di sini
            // Route::resource('roles', AdminRoleController::class);
            // Route::resource('permissions', AdminPermissionController::class); // Mungkin index sahaja

    });
    // === AKHIR ADMIN ROUTES ===
});

require __DIR__.'/auth.php';
