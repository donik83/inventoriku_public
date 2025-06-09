@extends('layouts.app')

@section('title', 'Rekod Pergerakan Item')

@section('content')
    {{-- Paparkan nama item yang sedang diproses --}}
    <h1 class="mb-3">Rekod Pergerakan untuk: {{ $item->name }}</h1>
    <p class="text-muted">Lokasi Semasa: {{ $item->location->name ?? 'Tiada Lokasi' }} | Kuantiti Semasa: {{ $item->quantity }}</p>

    <div class="card shadow-sm">
        <div class="card-header">Masukkan Butiran Pergerakan</div>
        <div class="card-body">

            {{-- Paparkan Ralat Validasi Umum --}}
            @if ($errors->any() && !$errors->hasAny(['movement_type', 'quantity_moved', 'to_location_id', 'notes']))
                <div class="alert alert-danger mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Borang menghantar ke route 'movements.store' dengan ID item --}}
            <form action="{{ route('movements.store', $item->id) }}" method="POST">
                @csrf

                {{-- Jenis Pergerakan (Dropdown Statik) --}}
                <div class="mb-3">
                    <label for="movement_type" class="form-label">Jenis Pergerakan:</label>
                    <select name="movement_type" id="movement_type" class="form-select @error('movement_type') is-invalid @enderror" required>
                        <option value="">-- Sila Pilih Jenis --</option>
                        {{-- Loop melalui array $movementTypes dari Controller --}}
                        @foreach ($movementTypes as $type)
                            <option value="{{ $type }}" {{ old('movement_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('movement_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Medan Kuantiti Terlibat --}}
                <div class="mb-3">
                    {{-- Nota tambahan --}}
                    <small class="text-muted d-block mb-1">Masukkan kuantiti fizikal sebenar jika Jenis Pergerakan = LARAS STOK.</small>
                    <x-forms.input type="number" name="quantity_moved" label="Kuantiti Terlibat / Kuantiti Sebenar" :value="old('quantity_moved')" min="0" /> {{-- Benarkan min 0 untuk LARAS STOK --}}
                    {{-- Nota: Kita ubah min="1" kepada min="0" untuk benarkan set kuantiti ke 0 semasa laras stok --}}
                </div>

                {{-- Lokasi Baru/Destinasi (Dropdown Dinamik) --}}
                 {{-- Nota: Input ini mungkin hanya relevan untuk jenis 'PINDAH' atau 'PULANG'. --}}
                <x-forms.select name="to_location_id" label="Lokasi Baru / Destinasi" :options="$locations" :selected="old('to_location_id')" />
                 {{-- Kita guna komponen select yang sudah ada --}}

                {{-- Medan Nota Tambahan --}}
                {{-- Tambah asterisk (*) pada label jika mahu --}}
                <x-forms.textarea name="notes" label="Nota Tambahan (Wajib jika LARAS STOK)" :value="old('notes')" rows="3" />

                {{-- Butang Hantar --}}
                {{-- <div class="mt-4"> --}}
                <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
                    <button type="submit" class="btn btn-success mb-3"><i class="bi bi-arrow-left-right"></i> Simpan</button>
                    <a class="btn btn-info mb-3" href="{{ route('movements.selectItem') }}"><i class="bi bi-hand-index"></i> Pilih</a>
                    <a class="btn btn-info mb-3" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan"></i></a>
                    <a href="{{ route('items.show', $item->id) }}" class="btn btn-outline-secondary mb-3"><i class="bi-x-circle"></i></a>
                    <br>
                    <span class="text-muted small">
                        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
                        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
                    </span>
                </div>

            </form>
        </div>
    </div>
@endsection
