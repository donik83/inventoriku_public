@extends('layouts.app') {{-- Guna layout utama Bootstrap Tuan --}}

@section('title', 'Edit Profil') {{-- Tetapkan Tajuk --}}

@section('content') {{-- Mulakan seksyen kandungan --}}
    {{-- Tajuk Halaman (menggunakan gaya Bootstrap dari layout Tuan jika ada, atau tambah di sini) --}}
    <h1 class="mb-4">Edit Profil</h1> {{-- Contoh tajuk --}}

    {{-- Susun partials menggunakan Grid Bootstrap --}}
    <div class="row gy-4"> {{-- gy-4 memberi jarak menegak antara kad --}}

        {{-- Bahagian Info Profil --}}
        <div class="col-12 col-lg-6"> {{-- Ambil separuh lebar pada skrin besar --}}
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Bahagian Kemas kini Kata Laluan --}}
        <div class="col-12 col-lg-6">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Bahagian Padam Akaun --}}
        <div class="col-12 col-lg-6">
            @include('profile.partials.delete-user-form')
        </div>

    </div>
    {{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">

    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>
@endsection {{-- Tutup seksyen kandungan --}}
