@extends('layouts.app')

@section('title', 'Laporan Item Dipinjam')

@section('content')
<h1>Laporan Item Yang Sedang Dipinjam</h1>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Item</th>
                        <th>Kategori</th>
                        <th>Tarikh/Masa Dipinjam</th>
                        <th>Direkod Oleh</th>
                        <th>Nota (Peminjam)</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop melalui item yang dipinjam --}}
                    @forelse ($borrowedItems as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            {{-- Pautkan ke halaman butiran item --}}
                            <td><a href="{{ route('items.show', $item->id) }}">{{ $item->name }}</a></td>
                            <td>{{ $item->category->name ?? '-' }}</td>
                            {{-- Akses pergerakan pinjam terakhir & format tarikh --}}
                            {{-- Guna optional chaining (?->) untuk elak ralat jika data tiada --}}
                            <td>{{ $item->itemMovements->first()?->created_at->format('d/m/Y H:i') ?? 'N/A' }}</td>
                            {{-- Akses nama pengguna yang merekod --}}
                            <td>{{ $item->itemMovements->first()?->user?->name ?? 'N/A' }}</td>
                            {{-- Akses nota dari pergerakan --}}
                            <td>{{ $item->itemMovements->first()?->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">Tiada item yang sedang direkodkan sebagai dipinjam pada masa ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary mb-3"><i class="bi bi-sign-turn-slight-left"></i> Pusat Laporan</a>
    <a href="{{ route('dashboard') }}" class="btn btn-info mb-3"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>

@endsection
