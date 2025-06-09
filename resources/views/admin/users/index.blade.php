@extends('layouts.app')

@section('title', 'Pengurusan Pengguna')

@section('content')
<h1>Pengurusan Pengguna</h1>

<div class="d-flex justify-content-end mb-3">
    {{-- Akan ditambah kemudian: --}}
    {{-- <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Pengguna</a> --}}
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Emel</th>
                        <th>Peranan (Roles)</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                           <i class="bi bi-plus-lg"></i> Tambah Pengguna Baru
                        </a>
                    </div>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                {{-- Paparkan nama roles --}}
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $roleName)
                                        <span class="badge bg-info me-1">{{ $roleName }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary">Tiada</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-start gap-1">
                                    {{-- Akan ditambah kemudian: --}}
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil-square"></i> Edit</a>
                                    {{-- Butang Padam - HANYA jika BUKAN pengguna semasa --}}
                                    @if(Auth::id() !== $user->id)
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('AWAS!\n\nAdakah anda pasti mahu memadam pengguna \'{{ $user->name }}\'?\nSemua data berkaitan pengguna ini mungkin akan hilang.\nTindakan ini tidak boleh dibuat asal!');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Padam">
                                            <i class="bi bi-trash"></i> Padam
                                        </button>
                                    </form>
                                    @endif
                                    {{-- Akhir Butang Padam --}}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tiada pengguna ditemui.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pautan Paginasi --}}
        <div class="mt-3">
            {{ $users->links() }}
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
