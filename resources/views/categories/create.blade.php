@extends('layouts.app')

@section('title', 'Senarai Item Inventori')

@section('content')

    <h1>Tambah Kategori Baru</h1>

    {{-- PENTING: Paparkan ralat validasi jika ada --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Sila betulkan ralat berikut:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf

        {{-- Medan nama Kategori --}}
        <x-forms.input name="name" label="Nama Kategori" value="{{ old('name') }}" required />

        {{-- Butang --}}
        <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
            <button type="submit" class="btn btn-success"><i class="bi-check-lg"></i> Simpan</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="bi-x-circle"></i> Batal</a>
            <br>
            <span class="text-muted small">
                {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
                | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
            </span>
        </div>
    </form>
@endsection
