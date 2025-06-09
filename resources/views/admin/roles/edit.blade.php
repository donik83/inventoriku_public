@extends('layouts.app')

@section('title', 'Edit Peranan: ' . $role->name)

@section('content')
<h1>Edit Peranan: <span class="text-primary">{{ $role->name }}</span></h1>

{{-- Peringatan jika edit role asas --}}
@if(in_array($role->name, ['Admin', 'User']))
    <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> AWAS: Mengubah suai kebenaran untuk peranan asas 'Admin' atau 'User' boleh memberi kesan besar pada fungsi aplikasi. Lakukan dengan berhati-hati. Mengubah nama peranan ini tidak dibenarkan.
    </div>
@endif

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Guna PUT atau PATCH untuk update --}}

            {{-- Nama Peranan --}}
            {{-- Kita mungkin tidak mahu benarkan nama 'Admin' atau 'User' diubah --}}
            <x-forms.input type="text" name="name" label="Nama Peranan:"
                           :value="old('name', $role->name)"
                           required
                           :readonly="in_array($role->name, ['Admin', 'User'])" />
                           {{-- Guna :readonly untuk elak ubah nama role asas --}}


            {{-- Kebenaran (Permissions) --}}
            <div class="mb-3">
                <label class="form-label">Kebenaran (Permissions):</label>
                <div class="row px-3">
                    @forelse ($permissions as $permission)
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}_edit"
                                       {{-- Semak jika permission ini ada dalam $rolePermissions ATAU dalam old input (jika ada ralat validasi) --}}
                                       {{ $rolePermissions->contains($permission->name) || in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm_{{ $permission->id }}_edit">
                                    {{ $permission->name }}
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-muted">Tiada kebenaran (permissions) ditemui.</p>
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
                    <i class="bi bi-save"></i> Kemas Kini Peranan
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
