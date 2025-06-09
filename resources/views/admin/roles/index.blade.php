@extends('layouts.app')

@section('title', 'Pengurusan Peranan (Roles)')

@section('content')
<h1>Pengurusan Peranan (Roles)</h1>

<div class="d-flex justify-content-end mb-3">
     <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Peranan Baru
     </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Peranan</th>
                        <th>Guard</th>
                        <th>Jumlah Pengguna</th> {{-- Contoh kolum tambahan --}}
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            {{-- Nombor urutan dengan paginasi --}}
                            <td>{{ $loop->iteration + $roles->firstItem() - 1 }}</td>
                            <td>{{ $role->name }}</td>
                            <td><span class="badge bg-secondary">{{ $role->guard_name }}</span></td>
                            <td>{{ $role->users()->count() }}</td> {{-- Kira pengguna dgn role ini --}}
                            <td>
                                <div class="d-flex justify-content-start gap-1">
                                    {{-- Jangan benar edit role Admin & User melalui UI? --}}
                                    @if(!in_array($role->name, ['Admin', 'User']))
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    @else
                                        <button type="button" class="btn btn-warning btn-sm" disabled title="Tidak boleh diedit">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </button>
                                    @endif
                                         {{-- BUTANG/BORANG PADAM DENGAN SYARAT BARU --}}
                                        @if(!in_array($role->name, ['Admin', 'User']) && $role->users()->count() == 0)
                                        {{-- Jika bukan role asas DAN tiada pengguna, benarkan padam --}}
                                        <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('AWAS!\n\nPadam peranan \'{{ $role->name }}\'? Pengguna tidak akan lagi mempunyai kebenaran dari peranan ini.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Padam">
                                                <i class="bi bi-trash"></i> Padam
                                            </button>
                                        </form>
                                        @else
                                            {{-- Jika role asas ATAU masih ada pengguna, nyahaktifkan butang --}}
                                            <button type="button" class="btn btn-danger btn-sm" disabled
                                                    title="{{ in_array($role->name, ['Admin', 'User']) ? 'Tidak boleh dipadam (peranan asas)' : 'Tidak boleh dipadam (masih ada pengguna)' }}">
                                                <i class="bi bi-trash"></i> Padam
                                            </button>
                                        @endif
                                    {{-- AKHIR BUTANG/BORANG PADAM --}}
                                     {{-- @endcan --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tiada peranan ditemui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         {{-- Pautan Paginasi --}}
         <div class="mt-3">
             {{ $roles->links() }}
         </div>
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
