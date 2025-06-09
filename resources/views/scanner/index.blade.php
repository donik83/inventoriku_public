@extends('layouts.app')

@section('title', 'Imbas Kod QR Item')

@section('content')
<div class="container">
    <h1 class="mb-3">Imbas Kod QR/UPC Item</h1>
    <p class="text-muted">Halakan kamera peranti anda pada Kod QR/UPC yang dilekatkan pada item.</p>

    {{-- Elemen div ini akan digunakan oleh pustaka JS untuk memaparkan kamera --}}
    <div id="qr-reader" style="width: 100%; max-width: 500px;" class="mx-auto border rounded p-2"></div>

    {{-- Tempat untuk memaparkan status atau hasil imbasan (pilihan) --}}
    <div id="qr-reader-results" class="mt-3 text-center"></div>

</div>
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
    <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
             | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
</div>
@endsection
