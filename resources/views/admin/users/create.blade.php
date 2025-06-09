@extends('layouts.app')

@section('title', 'Tambah Pengguna Baru')

@section('content')
<h1>Tambah Pengguna Baru</h1>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            {{-- Nama Pengguna --}}
            <x-forms.input type="text" name="name" label="Nama Penuh:" :value="old('name')" required />

            {{-- Emel Pengguna --}}
            <x-forms.input type="email" name="email" label="Alamat Emel:" :value="old('email')" required />

            {{-- Kata Laluan --}}
            <x-forms.input type="password" name="password" label="Kata Laluan:" required />

            {{-- Pengesahan Kata Laluan --}}
            <x-forms.input type="password" name="password_confirmation" label="Sahkan Kata Laluan:" required />

            {{-- Pilihan Peranan (Roles) --}}
            <div class="mb-3">
                <label class="form-label">Peranan (Roles):</label>
                <div>
                    @foreach ($roles as $role)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                                   {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                @error('roles')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
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
@endsection
