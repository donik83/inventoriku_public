@extends('layouts.app')
@section('title', 'Pengurusan Kategori')
@section('content')
    <h1>Pengurusan Kategori</h1>

    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Kategori</th>
                <th>Jumlah Item</th> {{-- Tambahan: kira item berkaitan --}}
                <th>Tindakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($categories as $category) {{-- Semak jika ada item untuk dipaparkan --}}
                <tr>
                    <td class="align-middle">{{ $category->id }}</td>
                    <td class="align-middle">{{ $category->name }}</td>
                    <td class="align-middle">{{ $category->items()->count() }}</td> {{-- Kira item guna relationship --}}
                    <td class="align-middle">
                        <div class="d-flex gap-1">
                            @can('update', $category)
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                            @endcan
                            @can('delete', $category)
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Adakah anda pasti? Memadam kategori mungkin menjejaskan item berkaitan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i> Padam</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Tiada kategori ditemui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pautan Paginasi --}}
    <div style="margin-top: 15px;">
        {{ $categories->links() }}
    </div>

    <hr>
    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        @can('create', App\Models\Category::class)
            <a href="{{ route('categories.create') }}" class="btn btn-primary mb-3"><i class="bi bi-boxes"></i> Tambah</a>
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
