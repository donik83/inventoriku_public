@extends('layouts.app')

@section('title', 'Pilih Item - Rekod Pergerakan')

@section('content')
    <h1>Rekod Pergerakan Baru: Pilih Item</h1>
    <p class="text-muted">Sila cari dan pilih item yang pergerakannya hendak direkodkan.</p>

    {{-- Borang Carian (Arahkan ke route ini sendiri) --}}
    <form action="{{ route('movements.selectItem') }}" method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama atau deskripsi item..."
                   value="{{ $searchTerm ?? '' }}">
            <button class="btn btn-secondary" type="submit"><i class="bi bi-search"></i> Cari</button>
            @if ($searchTerm)
                <a href="{{ route('movements.selectItem') }}" class="btn btn-outline-secondary"><i class="bi bi-x-square"></i> Reset</a>
            @endif
        </div>
    </form>

    {{-- Jadual Senarai Item --}}
    <div class="table-responsive"> {{-- Tambah untuk skrin kecil --}}
        <table class="table table-striped table-hover table-sm"> {{-- table-sm untuk lebih padat --}}
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama Item</th>
                    <th>Kategori</th>
                    <th>Lokasi Semasa</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name ?? 'Tiada' }}</td>
                        <td>{{ $item->location->name ?? 'Tiada' }}</td>
                        <td>
                            {{-- Pautan ke borang create pergerakan untuk item ini --}}
                            <a href="{{ route('movements.create', $item->id) }}" class="btn btn-primary btn-sm"><i class="bi bi-hand-index"></i> Pilih Item Ini</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Tiada item ditemui.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginasi --}}
    <div class="mt-3">
        {{ $items->links() }}
    </div>

    {{-- Butang/footer --}}
    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        <a href="{{ route('items.index') }}" class="btn btn-primary mb-3"><i class="bi bi-card-list"></i> Senarai Item</a>
        <a class="btn btn-info mb-3" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan"></i> Imbas</a>
        <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
            | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>
@endsection
