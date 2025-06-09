@extends('layouts.app')

@section('title', 'Senarai Kebenaran (Permissions)')

@section('content')
<h1>Senarai Kebenaran Sistem</h1>

<div class="alert alert-info mt-3">
    <i class="bi bi-info-circle-fill me-2"></i>
    Kebenaran (Permissions) biasanya didefinisikan dalam kod aplikasi (Seeders) dan diberikan kepada Peranan (Roles). Paparan ini adalah untuk rujukan sahaja.
</div>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Kebenaran (Permission Name)</th>
                        <th>Guard</th>
                        {{-- Tiada Tindakan biasanya --}}
                    </tr>
                </thead>
                <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            {{-- Nombor urutan dengan paginasi --}}
                            <td>{{ $loop->iteration + $permissions->firstItem() - 1 }}</td>
                            <td>{{ $permission->name }}</td>
                            <td><span class="badge bg-secondary">{{ $permission->guard_name }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Tiada kebenaran ditemui. Sila jalankan seeder.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         {{-- Pautan Paginasi --}}
         <div class="mt-3">
             {{ $permissions->links() }}
         </div>
    </div>
</div>
{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Kembali ke Senarai Peranan</a>
    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>
@endsection
