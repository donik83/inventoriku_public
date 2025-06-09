@extends('layouts.app')

@section('title', 'Senarai Item Inventori')

@section('content')

    <h1>Senarai Item Inventori</h1>

    {{-- Borang Carian & Penapis --}}
    <form action="{{ route('items.index') }}" method="GET" class="mb-4">
        <div class="row g-2"> {{-- Grid row dengan gutter --}}
            {{-- Input Carian Teks --}}
            <div class="col-md">
                <label for="search" class="visually-hidden">Carian Teks</label> {{-- Label tersembunyi untuk aksesibiliti --}}
                <input type="text" name="search" id="search" class="form-control"
                       placeholder="Cari nama atau deskripsi..."
                       value="{{ $searchTerm ?? '' }}">
            </div>

            {{-- Dropdown Kategori --}}
            <div class="col-md">
                 <label for="category_id" class="visually-hidden">Kategori</label>
                 <select name="category_id" id="category_id" class="form-select">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}"
                                {{-- Pilih option ini jika IDnya sama dengan yang dipilih --}}
                                {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Lokasi --}}
             <div class="col-md">
                 <label for="location_id" class="visually-hidden">Lokasi</label>
                 <select name="location_id" id="location_id" class="form-select">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}"
                                {{-- Pilih option ini jika IDnya sama dengan yang dipilih --}}
                                {{ $selectedLocationId == $location->id ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Butang --}}
            <div class="col-md-auto"> {{-- Auto lebar untuk butang --}}
                <button class="btn btn-secondary" type="submit"><i class="bi bi-funnel"></i> Tapis</button>
                {{-- Hanya papar Reset jika ada filter aktif --}}
                @if ($searchTerm || $selectedCategoryId || $selectedLocationId)
                    <a href="{{ route('items.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-square"></i> Reset</a>
                @endif
            </div>
        </div>
    </form>
    {{-- Akhir Borang Carian & Penapis --}}

    {{-- ... (Jadual item bermula di sini) ... --}}
    <div class="card card-body shadow-sm mt-4">
    <div class="table-responsive d-none d-md-block"> {{-- <-- TAMBAH d-none d-md-block --}}
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th class="d-none d-lg-table-cell">@sortablelink('id', '#')</th>
                <th>Gambar</th> {{-- <-- TAMBAH INI --}}
                <th>@sortablelink('name', 'Nama Item')</th>
                <th>Kategori</th>
                <th>Lokasi</th>
                <th class="d-none d-lg-table-cell">@sortablelink('purchase_date', 'Tarikh Beli')</th>
                <th class="d-none d-lg-table-cell text-end">@sortablelink('purchase_price', 'Harga Beli (RM)')</th>
                <th class="text-end">@sortablelink('quantity', 'Kuantiti')</th>
                <th>@sortablelink('status', 'Status')</th>
                <th class="text-center">Tindakan</th> {{-- Untuk butang Edit/Delete nanti --}}
            </tr>
        </thead>
        <tbody id="item-table-body">
            {{-- Semak jika ada item untuk dipaparkan --}}
            @forelse ($items as $item)
                <tr class="clickable-row" data-href="{{ route('items.show', $item->id) }}">
                    <td class="d-none d-lg-table-cell">{{ $item->id }}</td>

                    {{-- Sel Gambar (Kini guna data dari primaryImage) --}}
                    <td class="align-middle text-center"> {{-- Tambah text-center jika mahu --}}
                        {{-- Akses hubungan primaryImage. withDefault dalam model akan beri laluan placeholder jika tiada gambar langsung --}}
                        @if ($item->primaryImage)
                            {{-- Jika ada primaryImage dan ia BUKAN placeholder, paparkan --}}
                            <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                                alt="Thumbnail {{ $item->name }}"
                                style="max-width: 60px; max-height: 60px; object-fit: cover;"
                                class="img-thumbnail shadow-sm"> {{-- Guna kelas img-thumbnail Bootstrap --}}
                        @endif
                    </td>

                    {{-- Sel Nama Item: Boleh juga tambah align-middle jika perlu --}}
                    <td class="align-middle">
                    {{-- Tambah Ikon Privasi --}}
                    @if($item->is_private)
                        <i class="bi bi-lock-fill text-secondary me-1" title="Peribadi (Hanya Pemilik & Admin)"></i>
                    @else
                        <i class="bi bi-globe-americas text-success me-1" title="Umum (Boleh Dilihat Semua Pengguna)"></i>
                    @endif
                    {{-- Akhir Ikon Privasi --}}

                    <a href="{{ route('items.show', $item->id) }}">    
                    {{ $item->name }}</a>
                    </td>

                    {{-- Sel Kategori: Boleh juga tambah align-middle jika perlu --}}
                    <td class="align-middle">{{ $item->category->name ?? 'Tiada Kategori' }}</td>

                    {{-- Sel Lokasi: Boleh juga tambah align-middle jika perlu --}}
                    <td class="align-middle">{{ $item->location->name ?? 'Tiada Lokasi' }}</td>

                    {{-- ... sel data lain (tambah align-middle jika perlu untuk konsistensi) ... --}}
                    <td class="align-middle d-none d-lg-table-cell">{{ $item->purchase_date }}</td>
                    <td class="align-middle d-none d-lg-table-cell text-end">{{ number_format($item->purchase_price, 2) }}</td>
                    <td class="align-middle text-end">{{ $item->quantity }}</td>
                    <td class="align-middle">{{ $item->status }}</td>

                    {{-- Sel Tindakan: Kekalkan d-flex/gap, tambah align-middle --}}
                    <td class="align-middle">
                        <div class="d-flex gap-1">
                            {{-- Butang/Pautan diletak di dalam DIV ini --}}
                            <a class="btn btn-info btn-sm" href="{{ route('items.show', $item->id) }}"><i class="bi-eye"></i>Lihat</a>
                            @can('update', $item)
                            <a class="btn btn-warning btn-sm" href="{{ route('items.edit', $item->id) }}"><i class="bi-pencil-square"></i> Edit</a>
                            @endcan
                            @can('delete', $item)
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Adakah anda pasti ingin memadam item ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="bi-trash"></i> Padam</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                {{-- Dipaparkan jika tiada item langsung --}}
                <tr>
                    <td colspan="10">Tiada item inventori ditemui.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    </div>
    {{-- Paparan Kad untuk Skrin Kecil --}}
    <div class="d-block d-md-none mt-3"> {{-- <-- TAMBAH d-block d-md-none --}}
        @forelse ($items as $item)
            {{-- Masukkan partial view kad di sini --}}
            @include('items._item-card', ['item' => $item])
        @empty
            <div class="alert alert-warning">Tiada item inventori ditemui.</div>
        @endforelse
    </div>
    {{-- Paparkan pautan navigasi halaman (jika guna paginate) --}}
    <div style="margin-top: 15px;">
        {{ $items->links() }}
    </div>

    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        <a class="btn btn-primary mb-3" href="{{ route('items.create') }}"><i class="bi bi-file-earmark-plus"></i> Item</a>
        <a class="btn btn-info mb-3" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan"></i> Imbas</a>
        <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
            | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>

@endsection

