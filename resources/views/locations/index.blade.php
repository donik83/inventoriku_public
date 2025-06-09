@extends('layouts.app')
@section('title', 'Pengurusan Lokasi')
@section('content')
    <h1>Pengurusan Lokasi</h1>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Lokasi</th>
                <th>Jumlah Item</th> {{-- Tambahan: kira item berkaitan --}}
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($locations as $location)
                <tr>
                    <td class="align-middle">{{ $location->id }}</td>
                    <td class="align-middle">{{ $location->name }}</td>
                    <td class="align-middle">{{ $location->items()->count() }}</td> {{-- Kira item guna relationship --}}
                    <td class="align-middle">
                        <div class="d-flex gap-1">
                            @can('update', $location)
                            <a href="{{ route('locations.edit', $location->id) }}" class="btn btn-info btn-sm"><i class="bi-pencil-square"></i> Edit</a>
                            @endcan
                           @can('delete', $location)
                            <form action="{{ route('locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti? Memadam Lokasi mungkin menjejaskan item berkaitan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi-trash"></i> Padam</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tiada lokasi ditemui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pautan Paginasi --}}
    <div style="margin-top: 15px;">
        {{ $locations->links() }}
    </div>

    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        @can('create', App\Models\Location::class)
        <a href="{{ route('locations.create') }}" class="btn btn-primary mb-3"><i class="bi bi-geo-alt"></i> Tambah</a>
        @endcan
        <a href="{{ route('items.index') }}" class="btn btn-primary mb-3"><i class="bi bi-card-list"></i> Item</a>
        <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
             | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>
@endsection
