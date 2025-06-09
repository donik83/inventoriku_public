@extends('layouts.app')

@section('title', 'Edit Pengguna: ' . $user->name)

@section('content')
<h1>Edit Pengguna: {{ $user->name }}</h1>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        {{-- Borang menghantar ke route update, guna method PUT/PATCH --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT') {{-- Atau PATCH --}}

            {{-- Nama Pengguna --}}
            <x-forms.input type="text" name="name" label="Nama Penuh:" :value="old('name', $user->name)" required />

            {{-- Emel Pengguna --}}
            {{-- Nota: Validasi unik untuk emel semasa update perlu lebih berhati-hati --}}
            <x-forms.input type="email" name="email" label="Alamat Emel:" :value="old('email', $user->email)" required />

            {{-- Kata Laluan - BIARKAN KOSONG JIKA TIDAK MAHU TUKAR --}}
            <div class="row">
                <div class="col-md-6">
                    <x-forms.input type="password" name="password" label="Kata Laluan Baru (biarkan kosong jika tidak tukar):" />
                </div>
                <div class="col-md-6">
                    <x-forms.input type="password" name="password_confirmation" label="Sahkan Kata Laluan Baru:" />
                </div>
            </div>
             <small class="form-text text-muted mb-3 d-block">Hanya isi medan kata laluan jika anda mahu menukarnya.</small>


            {{-- Pilihan Peranan (Roles) --}}
            <div class="mb-3">
                <label class="form-label">Peranan (Roles):</label>
                <div>
                    @foreach ($roles as $role)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}_edit"
                                   {{-- Semak jika nama role ini ada dalam $userRoles ATAU dalam old input --}}
                                   {{ $userRoles->contains($role->name) || in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}_edit">
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
                    <i class="bi bi-save"></i> Kemas Kini Pengguna
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
        <hr> {{-- Pemisah --}}
        <h4 class="h6 mt-4">Tindakan Pengesahan Emel</h4>
        @if (!$user->hasVerifiedEmail()) {{-- Semak jika user BELUM verify --}}
            <form action="{{ route('admin.users.verify', $user) }}" {{-- Route baru yang akan kita cipta --}}
                method="POST" class="mt-2"
                onsubmit="return confirm('Adakah anda pasti mahu sahkan alamat emel untuk {{ $user->name }} ({{ $user->email }}) secara manual? Pengguna ini akan mendapat akses penuh selepas ini.');">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="bi bi-check-circle"></i> Sahkan Emel Pengguna Ini Secara Manual
                </button>
                <small class="d-block text-muted mt-1">Klik ini jika pengguna tidak mempunyai emel atau menghadapi masalah untuk mengesahkannya sendiri.</small>
            </form>
        @else
            {{-- Jika user SUDAH verify, paparkan status --}}
            <p class="text-success mt-2 mb-0">
                <i class="bi bi-check-circle-fill"></i> Emel pengguna ini telah disahkan pada: {{ $user->email_verified_at->format('d/m/Y H:i:s') }}.
            </p>
        @endif
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
