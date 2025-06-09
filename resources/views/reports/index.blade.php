@extends('layouts.app')

@section('title', 'Halaman Utama Laporan')

@section('content')
<h1>Pusat Laporan InventoriKu</h1>
<p class="lead">Pilih laporan yang ingin anda lihat daripada senarai di bawah.</p>

<div class="row mt-4 g-3"> {{-- g-3 untuk jarak antara kad --}}
    @forelse ($reports as $report)
        {{-- Di sini Tuan boleh tambah @can($report['permission'] ?? 'view_reports') jika Tuan mahu
             kawal akses ke setiap jenis laporan menggunakan permission yang lebih spesifik nanti --}}
        <div class="col-md-6 col-lg-4"> {{-- Responsif untuk saiz skrin berbeza --}}
            <div class="card shadow-sm h-100"> {{-- h-100 untuk pastikan kad sama tinggi dalam baris --}}
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">{{ $report['name'] }}</h5>
                    <p class="card-text text-muted small flex-grow-1">{{ $report['description'] }}</p>
                    <a href="{{ $report['route'] }}" class="btn btn-primary mt-auto">
                        <i class="bi bi-file-earmark-text me-2"></i> Buka Laporan
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col">
            <div class="alert alert-warning">Tiada laporan tersedia pada masa ini.</div>
        </div>
    @endforelse
</div>

{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
    <a href="{{ route('items.index') }}" class="btn btn-primary mb-3"><i class="bi bi-card-list"></i> Senarai Item</a>
    <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>
@endsection
