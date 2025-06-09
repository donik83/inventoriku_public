@extends('layouts.app') {{-- Pastikan mewarisi layout induk --}}

@section('title', 'Tambah Item Inventori Baru') {{-- Tetapkan tajuk halaman --}}

@section('content') {{-- Mulakan seksyen kandungan --}}

    <h1>Tambah Item Baru</h1>

    {{-- PENTING: Paparkan ralat validasi umum jika ada (selain ralat medan spesifik) --}}
    @if ($errors->any() && !$errors->hasAny(['name', 'description', 'category_id', 'location_id', 'purchase_date', 'purchase_price', 'quantity', 'serial_number', 'warranty_expiry_date', 'status', 'image']))
        <div class="alert alert-danger">
            <strong>Ralat!</strong> Terdapat masalah dengan input Tuan.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Medan Nama Item --}}
        {{-- KOD BARU (MENGGUNAKAN KOMPONEN) --}}
        <x-forms.input name="name" label="Nama Item" required />

        {{-- Medan Deskripsi --}}
        <x-forms.textarea name="description" label="Deskripsi" />

        {{-- Medan Kategori --}}
        <x-forms.select name="category_id" label="Kategori" :options="$categories" />

        {{-- Medan Lokasi --}}
        <x-forms.select name="location_id" label="Lokasi" :options="$locations" />

        {{-- Medan Tarikh Beli --}}
        <x-forms.input name="purchase_date" label="Tarikh Beli" type="date" />

        {{-- Medan Harga Beli --}}
        <x-forms.input name="purchase_price" label="Harga Beli (RM)" type="number" step="0.01" min="0" />

        {{-- Medan Kuantiti --}}
        <x-forms.input name="quantity" label="Kuantiti" type="number" />

        {{-- Medan Nombor Siri --}}
        <x-forms.input name="serial_number" label="Nombor Siri" type="text" />

        {{-- Medan barcode_data --}}
        <x-forms.input type="text" name="barcode_data" label="Data Kod Bar Produk (EAN/UPC):" :value="old('barcode_data', $barcodeData ?? null)" />

         {{-- Medan Tarikh Luput Jaminan --}}
         <x-forms.input name="warranty_expiry_date" label="Tarikh Luput Jaminan" type="date" />

         {{-- Medan Status --}}
         <div class="mb-3">
            <label for="status" class="form-label">Status:</label>
            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                <option value="Digunakan" {{ old('status', 'Digunakan') == 'Digunakan' ? 'selected' : '' }}>Digunakan</option>
                <option value="Disimpan" {{ old('status') == 'Disimpan' ? 'selected' : '' }}>Disimpan</option>
                <option value="Rosak" {{ old('status') == 'Rosak' ? 'selected' : '' }}>Rosak</option>
                <option value="Dijual" {{ old('status') == 'Dijual' ? 'selected' : '' }}>Dijual</option>
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
                <input class="form-check-input" type="radio" name="is_private" id="is_private_true" value="1" {{ old('is_private', true) ? 'checked' : '' }}> {{-- Lalai: checked --}}
                <label class="form-check-label" for="is_private_true">
                    Peribadi (Hanya saya & Admin boleh lihat/urus)
                </label>
            </div>
            <div class="form-check">
                {{-- Nilai 0 untuk Public (false) --}}
                <input class="form-check-input" type="radio" name="is_private" id="is_private_false" value="0" {{ !old('is_private', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_private_false">
                    Kongsi (Semua pengguna boleh lihat)
                </label>
            </div>
            @error('is_private')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
        <hr> {{-- Pemisah visual sebelum butang --}}
        {{-- Input untuk Pelbagai Gambar Item --}}
        <div class="mb-3">
            <label for="images" class="form-label">Gambar Item (Pilih sehingga 5):</label>
            {{-- Guna input fail HTML biasa dengan 'multiple' --}}
            <input class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                type="file"
                id="images"
                name="images[]" {{-- Nama mesti ada [] untuk array --}}
                multiple {{-- Benarkan pilih lebih dari satu fail --}}
                accept="image/jpeg,image/png,image/gif"
                capture="environment"> {{-- Jenis fail dibenarkan --}}

            {{-- Papar ralat jika array 'images' ada masalah (cth: lebih dari 5 fail) --}}
            @error('images')
                <div class="invalid-feedback d-block"> {{-- d-block penting untuk papar ralat array --}}
                    {{ $message }}
                </div>
            @enderror
            {{-- Papar ralat jika salah satu fail dalam array ada masalah (cth: bukan imej, terlalu besar) --}}
            @error('images.*')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
            <small class="form-text text-muted">Tahan kekunci Ctrl (atau Cmd pada Mac) untuk memilih lebih dari satu gambar.</small>
        </div>
        <hr> {{-- Pemisah visual sebelum butang --}}
        {{-- Medan Fail Lampiran PDF --}}
        <x-forms.input type="file" name="receipt" label="Lampiran Resit/Manual (cth: PDF):" accept=".pdf,.txt,.doc,.docx,.xlsx,.xls" />
        {{-- Atribut 'accept' membantu pelayar menapis jenis fail, tetapi validasi server tetap utama --}}

        {{-- Butang --}}
        <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
            <a class="btn btn-info mb-3" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan"></i> Imbas</a>
            <button class="btn btn-success mb-3" type="submit" ><i class="bi-check-lg"></i> Simpan</button> {{-- Butang Success (hijau) --}}
            <a class="btn btn-secondary mb-3" href="{{ route('items.index') }}"><i class="bi-x-circle"></i> Batal</a> {{-- Butang Secondary (kelabu) --}}
            <br>
            <span class="text-muted small">
                {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
                | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
            </span>
        </div>
    </form>

@endsection {{-- Tutup seksyen content --}}
