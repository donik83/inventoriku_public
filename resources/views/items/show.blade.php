@extends('layouts.app') {{-- Pastikan mewarisi layout induk --}}

@section('title', 'Senarai Item Inventori') {{-- Tetapkan tajuk halaman --}}

@section('content') {{-- Mulakan seksyen kandungan --}}

<div class="card">
    <div class="card-header">
        Butiran Item: {{ $item->name }}
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush"> {{-- Atau guna <dl>/<dt>/<dd> --}}
            <li class="list-group-item"><strong>ID:</strong> {{ $item->id }}</li>
            <li class="list-group-item"><strong>Nama:</strong> {{ $item->name }}</li>
            <li class="list-group-item"><strong>Deskripsi:</strong> {{ $item->description ?? 'Tiada' }}</li>
            <li class="list-group-item"><strong>Kategori:</strong> {{ $item->category->name ?? 'Tiada Kategori' }}</li>
            <li class="list-group-item"><strong>Lokasi:</strong> {{ $item->location->name ?? 'Tiada Lokasi' }}</li>
            <li class="list-group-item"><strong>Tarikh Beli:</strong> {{ $item->purchase_date ?? 'Tidak Diketahui' }}</li>
            <li class="list-group-item"><strong>Harga Beli (RM):</strong> {{ isset($item->purchase_price) ? number_format($item->purchase_price, 2) : 'Tidak Diketahui' }}</li>
            <li class="list-group-item"><strong>Kuantiti:</strong> {{ $item->quantity }}</li>
            <li class="list-group-item"><strong>No Siri:</strong> {{ $item->serial_number ?? 'Tiada' }}</li>
            <li class="list-group-item"><strong>Data Kod Bar 2D:</strong> {{ $item->barcode_data ?? 'Tiada' }}</li>
            <li class="list-group-item"><strong>Luput Jaminan:</strong> {{ $item->warranty_expiry_date ?? 'Tiada' }}</li>
            <li class="list-group-item"><strong>Status:</strong> {{ $item->status }}</li>
            <li class="list-group-item"><strong>Tarikh Dicipta:</strong> {{ $item->created_at }}</li>
            <li class="list-group-item"><strong>Tarikh Dikemaskini:</strong> {{ $item->updated_at }}</li>
            {{-- ... semua butiran lain ... --}}
            @if ($item->image_path)
                <li class="list-group-item">
                    <strong>Gambar:</strong><br>
                    <img src="{{ asset('storage/' . $item->image_path) }}" alt="..." style="max-width: 300px; max-height: 300px; margin-top: 10px;">
                </li>
            @endif
        </ul>
        {{-- Bahagian Galeri Gambar Item --}}
        <div class="mt-4">
            <strong>Gambar Item:</strong>
            @if ($item->images->isNotEmpty())
                <div class="row g-2 mt-2"> {{-- Grid dengan jarak kecil --}}
                    @foreach ($item->images as $image)
                        <div class="col-6 col-sm-4 col-md-3 col-lg-2"> {{-- Saiz kolum responsif --}}
                            <div class="position-relative">
                                <a href="{{ asset('storage/' . $image->path) }}" {{-- href kini ke gambar saiz penuh --}}
                                    data-fancybox="gallery-{{ $item->id }}" {{-- Kumpulan gambar untuk item ini --}}
                                    data-caption="Gambar {{ $loop->iteration }} untuk {{ $item->name }}" {{-- Caption pilihan --}}
                                    class="d-block position-relative">

                                    {{-- Imej thumbnail (kekal sama) --}}
                                    <img src="{{ asset('storage/' . $image->path) }}"
                                        alt="Gambar Item {{ $loop->iteration }}"
                                        class="img-fluid img-thumbnail shadow-sm"
                                        style="aspect-ratio: 1 / 1; object-fit: cover;">
                                </a>
                                {{-- Tanda jika ia gambar utama --}}
                                @if($image->is_primary)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                                        style="font-size: 0.6rem; padding: 0.3em 0.5em;">
                                        <i class="bi bi-star"></i>
                                        <span class="visually-hidden">Gambar Utama</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Jika tiada gambar langsung, panggil primaryImage yang akan beri placeholder --}}
                <img src="{{ asset('storage/' . $item->primaryImage->path) }}"
                alt="Tiada Gambar"
                class="img-thumbnail shadow-sm mt-2"
                style="max-width: 150px;"> {{-- Saiz placeholder --}}
            @endif
        </div>
        {{-- Akhir Bahagian Galeri --}}
        <hr class="my-4"> {{-- Garisan pemisah --}}
        {{-- Bahagian untuk Pautan Lampiran PDF --}}
        @if ($item->receipt_path)
            <div class="mt-4"> {{-- Beri jarak atas --}}
                <strong>Lampiran Resit/Manual:</strong><br>
                <a href="{{ asset('storage/' . $item->receipt_path) }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-2">
                    {{-- Pilihan Ikon + Teks --}}
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf me-1" viewBox="0 0 16 16">
                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 1.5H4a1 1 0 0 1-1-1V14a1 1 0 0 1 1 1h8a1 1 0 0 1 1-1V4.5h-2z"/>
                    <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.625c.378-.1.598-.27.712-.454.114-.184.106-.377-.038-.515-.145-.138-.375-.255-.635-.255-.225 0-.426.04-.593.113a.85.85 0 0 0-.38.494c-.057.12-.057.25-.057.375q0 .13.04.248c.041.119.105.234.188.333a.8.8 0 0 1-.43.444.8.8 0 0 1-.6.046.8.8 0 0 1-.43-.444q-.044-.118-.044-.243c0-.264.068-.522.206-.722.133-.195.338-.346.6-.454a3.2 3.2 0 0 1 1.085-.28c.317-.002.63.045.924.134.3.09.554.21.77.358.217.148.38.33.498.542.12.21.174.456.174.714 0 .41-.153.747-.456.985-.305.237-.727.354-1.225.354-.27 0-.53-.03-.78-.085a2 2 0 0 0-.577-.156 4.1 4.1 0 0 0-.93.195c-.253.11-.476.25-.658.41a.9.9 0 0 0-.38.619c-.044.15-.044.3-.044.45q0 .18.05.36a.8.8 0 0 0 .108.326.8.8 0 0 0 .445.43.8.8 0 0 0 .605.046.8.8 0 0 0 .43-.445q.043-.117.043-.239c0-.1.008-.196-.023-.285a.5.5 0 0 0-.154-.192.4.4 0 0 0-.217-.064c-.08-.004-.155.01-.217.036a1.1 1.1 0 0 0-.403.165c-.09.08-.156.19-.188.316-.032.12-.032.25-.032.38q0 .28.108.522a1.3 1.3 0 0 0 .438.42q.18.14.39.203c.21.062.43.09.665.09.36 0 .705-.068 1.025-.206.315-.136.58-.328.785-.572.207-.245.354-.54.44-.88.085-.34.117-.7.117-1.075 0-.418-.08-.78-.243-1.075a2 2 0 0 0-.648-.861 3.9 3.9 0 0 0-1.05-.534q-.54-.195-1.06-.195c-.37 0-.7.06-.965.175-.265.115-.47.28-.615.49q-.145.21-.205.468c-.06.255-.09.55-.09.88q0 .23.045.46c.045.23.13.437.255.613a.8.8 0 0 1-.34.63.8.8 0 0 1-.61.21Zm-1.01-6.267c-.19-.145-.36-.275-.51-.39a.7.7 0 0 1-.19-.42c0-.15.05-.28.15-.38.1-.1.24-.15.41-.15.15 0 .28.04.39.11s.19.17.23.28q.04.11.04.22 0 .1-.03.19a.5.5 0 0 1-.15.19.5.5 0 0 1-.2.06c-.07 0-.13-.01-.18-.04a.7.7 0 0 0-.31-.12Zm-1.99.447a.6.6 0 0 1-.18-.497V6.61a.6.6 0 0 1 .18-.497c.11-.11.25-.165.42-.165h.5c.17 0 .31.055.41.165.1.11.15.254.15.43v.42c0 .17-.055.31-.165.41-.11.1-.25.15-.41.15h-.5a.6.6 0 0 1-.41-.15Z"/>
                    </svg>
                    Lihat/Muat Turun PDF
                </a>
            </div>
         @endif
    </div>
    <hr class="my-4"> {{-- Garisan pemisah --}}
    {{-- Contoh diletak selepas kad butiran utama, sebelum sejarah --}}
    <div class="my-4 text-center"> {{-- Jarak atas bawah & tengah --}}
        <h3 class="h5">Kod QR Item</h3>
        {{-- Tag img yang src nya memanggil route penjanaan QR --}}
        <img src="{{ route('items.qrcode', $item->id) }}" alt="Kod QR untuk {{ $item->name }}" width="200" height="200">
        <p class="small text-muted mt-2">Imbas kod ini untuk akses cepat.</p>
   </div>
   <hr class="my-4"> {{-- Pemisah sebelum sejarah --}}
   {{-- ... (Bahagian Sejarah Pergerakan) ... --}}
    {{-- Bahagian Sejarah Pergerakan --}}
    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h2 class="h5 mb-0">Sejarah Pergerakan</h2>
        </div>
        <div class="card-body">
            @if ($item->itemMovements->isNotEmpty()) {{-- Semak jika ada sejarah --}}
                <div class="table-responsive">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>Tarikh/Masa</th>
                                <th>Jenis Pergerakan</th>
                                <th>Kuantiti</th>
                                <th>Ke Lokasi</th>
                                <th>Direkod Oleh</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Loop melalui sejarah pergerakan ($item->itemMovements sudah dimuatkan) --}}
                            @foreach ($item->itemMovements as $movement)
                                <tr>
                                    {{-- Format tarikh --}}
                                    <td>{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $movement->movement_type }}</td>
                                    <td>{{ $movement->quantity_moved ?? '-' }}</td>
                                    {{-- Papar nama lokasi destinasi jika ada --}}
                                    <td>{{ $movement->destinationLocation->name ?? '-' }}</td>
                                    {{-- Papar nama pengguna jika ada --}}
                                    <td>{{ $movement->user->name ?? 'N/A' }}</td>
                                    <td>{{ $movement->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">Tiada rekod pergerakan untuk item ini.</p>
            @endif
        </div>
    </div>
    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center"> {{-- Letak pautan/butang tindakan di footer kad --}}
        @can('update', $item)
        <a class="btn btn-warning btn-sm ms-1" href="{{ route('items.edit', $item->id) }}"><i class="bi bi-pencil-square"></i></a>
        @endcan

        {{-- Borang Padam dengan butang btn-danger --}}
        @can('delete', $item)
        <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Adakah anda pasti?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger btn-sm ms-1" type="submit"><i class="bi bi-trash"></i> </button>
        </form>
        @endcan

        <a class="btn btn-primary btn-sm ms-1" href="{{ route('items.index') }}"><i class="bi bi-card-list"></i> </a>
        <a class="btn btn-primary btn-sm ms-1" href="{{ route('movements.create', $item->id) }}"><i class="bi bi-arrow-left-right"></i> Pergerakan</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
            | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>
</div>
@endsection
