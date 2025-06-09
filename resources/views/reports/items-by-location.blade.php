@extends('layouts.app')

@section('title', 'Laporan Item Mengikut Lokasi')

@section('content')
<h1>Laporan Item Mengikut Lokasi</h1>

<div class="card shadow-sm mt-4">
    <div class="card-header">Pilih Lokasi</div>
    <div class="card-body">
        <form action="{{ route('reports.itemsByLocation') }}" method="GET">
            <div class="row g-2">
                <div class="col-md">
                    {{-- Dropdown untuk pilih lokasi --}}
                    <select name="location_id" id="location_id" class="form-select" required>
                        <option value="">-- Sila Pilih Lokasi --</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}"
                                {{-- Tandakan sebagai selected jika ini lokasi yang sedang dipapar --}}
                                {{ $selectedLocationId == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-list-ol"></i> Papar Laporan</button>
                </div>
                <div class="col-auto">
                    <a href="{{ route('reports.itemsByLocation') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Hanya paparkan jadual jika lokasi telah dipilih --}}
@if ($selectedLocationId)
<div class="card shadow-sm mt-4">
    <div class="card-header">
        {{-- Paparkan nama lokasi yang dipilih --}}
        <h2 class="h5 mb-0">Item di Lokasi: {{ $selectedLocationName ?? 'Tidak Diketahui' }}</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th>Kuantiti</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><a href="{{ route('items.show', $item->id) }}">{{ $item->name }}</a></td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->status }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tiada item ditemui di lokasi ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
{{-- Mesej jika tiada lokasi dipilih --}}
<div class="alert alert-info mt-4">
    Sila pilih lokasi dari senarai di atas dan klik "Papar Laporan".
</div>
@endif
{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
     {{-- Tuan boleh tambah pautan ke laporan lain di sini nanti --}}
     <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-sign-turn-slight-left"></i> Pusat Laporan</a>
     <a href="{{ route('dashboard') }}" class="btn btn-info mb-3"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>

@endsection
