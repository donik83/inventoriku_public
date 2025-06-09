@extends('layouts.app')

@section('title', 'Tambah Peranan Baru')

@section('content')
<h1>Tambah Peranan Baru</h1>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            {{-- Nama Peranan --}}
            <x-forms.input type="text" name="name" label="Nama Peranan:" :value="old('name')" required />

            {{-- Kebenaran (Permissions) --}}
            <div class="mb-3">
                <label class="form-label">Kebenaran (Permissions):</label>
                <div class="row px-3">
                    @forelse ($permissions as $permission)
                        <div class="col-md-4 col-sm-6 mb-2"> {{-- Susunan checkbox --}}
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                       {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Tiada kebenaran (permissions) ditemui. Sila jalankan seeder dahulu.</p>
                        </div>
                    @endforelse
                </div>
                @error('permissions')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                 @error('permissions.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Peranan
                </button>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Batal</a>
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
