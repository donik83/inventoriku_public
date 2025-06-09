@extends('layouts.app')

@section('title', 'Pindah Pemilik Item (Admin)')

@section('content')
<h1>Pindah Pemilik Item</h1>
<p>Fungsi ini membolehkan Admin menukar pemilik asal sesuatu item kepada pengguna lain.</p>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form action="{{ route('admin.items.transfer.submit') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="item_id" class="form-label">Pilih Item:</label>
                <select name="item_id" id="item_id" class="form-select @error('item_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Item --</option>
                    @foreach ($items as $item)
                        {{-- Paparkan nama item dan pemilik semasa untuk rujukan --}}
                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }} (Pemilik Semasa: {{ $item->owner->name ?? 'Tiada/Sistem' }})
                        </option>
                    @endforeach
                </select>
                @error('item_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="new_owner_user_id" class="form-label">Pilih Pemilik Baru:</label>
                <select name="new_owner_user_id" id="new_owner_user_id" class="form-select @error('new_owner_user_id') is-invalid @enderror" required>
                    <option value="">-- Pilih Pengguna Sebagai Pemilik Baru --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ old('new_owner_user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('new_owner_user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- (Pilihan) Tambah checkbox untuk menukar status is_private semasa pindah --}}
            {{--
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="make_public" id="make_public" value="1">
                <label class="form-check-label" for="make_public">
                    Jadikan item ini Umum (Public) selepas pindah pemilik?
                </label>
            </div>
            --}}


            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-arrow-repeat"></i> Pindahkan Pemilik
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Kembali ke Senarai Pengguna</a>
            </div>
        </form>
    </div>
</div>
@endsection