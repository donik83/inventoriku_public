@extends('layouts.app') {{-- Pastikan mewarisi layout induk --}}

@section('title', 'InventoriKu') {{-- Tetapkan tajuk halaman --}}

@section('content') {{-- Mulakan seksyen kandungan --}}

    <h1>Edit Kategori: {{ $category->name }}</h1>

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

    <form action="{{ route('categories.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Method Spoofing --}}
        <x-forms.input name="name" label="Nama Kategori" :value="old('name', $category->name)" required />

        {{-- Butang/footer --}}
        <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
            <button type="submit" class="btn btn-success"><i class="bi-check-lg"></i> Kemaskini</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary"><i class="bi-x-circle"></i> Batal</a>
            <br>
            <span class="text-muted small">
                {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
                | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
            </span>
        </div>
    </form>
@endsection
