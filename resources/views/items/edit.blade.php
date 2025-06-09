@extends('layouts.app') {{-- Pastikan mewarisi layout induk --}}

@section('title', 'Edit Item Inventori') {{-- Tetapkan tajuk halaman --}}

@section('content') {{-- Mulakan seksyen kandungan --}}

    <h1>Edit Item Inventori</h1>

    {{-- Borang akan hantar data ke kaedah 'store' dalam ItemController menggunakan method POST --}}
    <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT') {{-- atau PATCH --}}

        {{-- Medan Nama Item --}}
        <x-forms.textarea name="name" label="Nama Item" :value="$item->name" />

        {{-- Medan Deskripsi --}}
        <x-forms.textarea name="description" label="Deskripsi" :value="$item->description" />

        {{-- Medan Kategori --}}
        <x-forms.select name="category_id" label="Kategori" :options="$categories" :selected="$item->category_id" />

        {{-- Medan Lokasi --}}
        <x-forms.select name="location_id" label="Lokasi" :options="$locations" :selected="$item->location_id" />

        {{-- Medan Tarikh Beli --}}
        <x-forms.input type="date" name="purchase_date" label="Tarikh Beli" :value="$item->purchase_date" />

        {{-- Medan Harga Beli --}}
        <x-forms.input name="purchase_price" label="Harga Beli (RM)" type="number" step="0.01" min="0" :value="$item->purchase_price" />

        {{-- Medan Kuantiti --}}
        <x-forms.input name="quantity" label="Kuantiti" type="number" min="1" :value="$item->quantity" />

        {{-- Medan Nombor Siri --}}
        <x-forms.input name="serial_number" label="Nombor Siri" type="text" :value="$item->serial_number" />

        {{-- Medan barcode_data --}}
        <x-forms.input type="text" name="barcode_data" label="Data Kod Bar 2D (jika ada)" :value="old('barcode_data', $item->barcode_data)" />

        {{-- Medan Tarikh Luput Jaminan --}}
        <x-forms.input name="warranty_expiry_date" label="Tarikh Luput Jaminan" type="date" :value="$item->warranty_expiry_date" />

        {{-- Medan Status --}}
        <div class="mb-3">
            <label class="form-label" for="status">Status:</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
                <option value="Digunakan" {{ old('status', $item->status) == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                <option value="Disimpan" {{ old('status', $item->status) == 'Disimpan' ? 'selected' : '' }}>Disimpan</option>
                <option value="Rosak" {{ old('status', $item->status) == 'Rosak' ? 'selected' : '' }}>Rosak</option>
                <option value="Dijual" {{ old('status', $item->status) == 'Dijual' ? 'selected' : '' }}>Dijual</option>
            </select>
            @error('status')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        {{-- Pilihan Keterlihatan --}}
        <div class="mb-3">
            <label class="form-label">Keterlihatan Item:</label>
            <div class="form-check">
                {{-- Nilai 1 untuk Private (true) --}}
                <input class="form-check-input" type="radio" name="is_private" id="is_private_true_edit" value="1" {{ old('is_private', $item->is_private) ? 'checked' : '' }}> {{-- Guna $item->is_private --}}
                <label class="form-check-label" for="is_private_true_edit">
                    Peribadi (Hanya saya & Admin boleh lihat/urus)
                </label>
            </div>
            <div class="form-check">
                {{-- Nilai 0 untuk Public (false) --}}
                <input class="form-check-input" type="radio" name="is_private" id="is_private_false_edit" value="0" {{ !old('is_private', $item->is_private) ? 'checked' : '' }}> {{-- Guna !$item->is_private --}}
                <label class="form-check-label" for="is_private_false_edit">
                    Kongsi (Semua pengguna boleh lihat)
                </label>
            </div>
            @error('is_private')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <hr> {{-- Pemisah visual --}}

        {{-- BAHAGIAN 1: Urus Gambar Sedia Ada --}}
        <div class="mb-4">
            <h4 class="h6">Gambar Sedia Ada</h4>
            @if ($item->images->isNotEmpty())
                <div class="row g-3">
                    @foreach ($item->images as $image)
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card h-100 shadow-sm">
                                <img src="{{ asset('storage/' . $image->path) }}"
                                    class="card-img-top" alt="Gambar Item {{ $loop->iteration }}"
                                    style="aspect-ratio: 1 / 1; object-fit: cover;">
                                <div class="card-body text-center p-2">
                                    {{-- Pilihan untuk Padam --}}
                                    <div class="form-check form-check-inline mb-1">
                                        <input class="form-check-input" type="checkbox"
                                            name="delete_images[]" {{-- Nama array untuk deletion --}}
                                            value="{{ $image->id }}" {{-- Nilai ialah ID gambar --}}
                                            id="delete_image_{{ $image->id }}">
                                        <label class="form-check-label small" for="delete_image_{{ $image->id }}">
                                            Padam?
                                        </label>
                                    </div>
                                    <br> {{-- Baris baru jika perlu --}}
                                    {{-- Pilihan untuk Gambar Utama --}}
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio"
                                            name="primary_image_id" {{-- Nama untuk pilihan primary --}}
                                            value="{{ $image->id }}" {{-- Nilai ialah ID gambar --}}
                                            id="primary_image_{{ $image->id }}"
                                            {{-- Tandakan checked jika ia memang gambar utama --}}
                                            {{ $image->is_primary ? 'checked' : '' }}>
                                        <label class="form-check-label small" for="primary_image_{{ $image->id }}">
                                            Utama
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <small class="form-text text-danger mt-2">Peringatan: Menanda "Padam?" akan membuang gambar ini secara kekal selepas Tuan klik "Kemas kini Item".</small>
                {{-- Tampilkan ralat validasi khusus untuk delete_images jika ada --}}
                @error('delete_images.*')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
                @error('primary_image_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            @else
                <p class="text-muted">Tiada gambar sedia ada untuk item ini.</p>
            @endif
        </div>
        {{-- AKHIR BAHAGIAN 1 --}}


        {{-- BAHAGIAN 2: Tambah Gambar Baru --}}
        <div class="mb-3">
            <label for="images" class="form-label">Tambah Gambar Baru (Maks 5 Jumlah Keseluruhan):</label>
            {{-- Input fail multiple seperti dalam create.blade.php --}}
            <input class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                type="file"
                id="images"
                name="images[]"
                multiple
                accept="image/jpeg,image/png,image/gif"
                capture="environment">

            @error('images')
                <div class="invalid-feedback d-block"> {{ $message }} </div>
            @enderror
            @error('images.*')
                <div class="invalid-feedback d-block"> {{ $message }} </div>
            @enderror
            <small class="form-text text-muted">Tahan Ctrl/Cmd untuk pilih lebih dari satu gambar.</small>
        </div>
        {{-- AKHIR BAHAGIAN 2 --}}
        <hr> {{-- Pemisah visual sebelum butang --}}
        {{-- Medan Fail PDF --}}
        <div class="mb-3">
            <label class="form-label">Resit/Manual Sedia Ada:</label><br>
            @if ($item->receipt_path)
                <a href="{{ asset('storage/' . $item->receipt_path) }}" target="_blank">{{ basename($item->receipt_path) }}</a>
                {{-- Tambah butang/checkbox untuk padam resit jika perlu nanti --}}
            @else
                <span>Tiada lampiran.</span>
            @endif
        </div>
        {{-- Diikuti dengan komponen input fail --}}
        <x-forms.input type="file" name="receipt" label="Muat Naik Resit/Manual Baru (cth: PDF):" accept=".pdf,.txt,.doc,.docx,.xlsx,.xls" />

        <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
            {{-- Butang --}}
            <button type="submit" class="btn btn-success"><i class="bi bi-check-square"></i> Kemaskini</button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary"><i class="bi bi-x-square"></i> Batal</a>
            <br>
            <span class="text-muted small">
                {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
                | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
            </span>
        </div>
    </form>

@endsection {{-- Tutup seksyen content --}}
