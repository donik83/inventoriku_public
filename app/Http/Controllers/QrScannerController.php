<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // <-- Tambah

class QrScannerController extends Controller
{
    /**
     * Paparkan halaman pengimbas QR.
     */
    public function index(): View // <-- Tambah method ini
    {
        return view('scanner.index'); // Akan pulangkan view scanner.index
    }
}
