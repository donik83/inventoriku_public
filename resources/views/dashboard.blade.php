@extends('layouts.app') {{-- Guna layout utama --}}

@section('title', 'Dashboard Inventori')

@section('content')

    <h1>Dashboard Inventori</h1>

    <div class="row gy-4"> {{-- Grid dengan gutter --}}

        {{-- Kad Statistik Utama --}}
        <div class="col-md-6 col-lg-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Jumlah Item</h5>
                    <p class="card-text fs-1 fw-bold">{{ $totalItems }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-4">
            <div class="card text-center shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title">Anggaran Nilai (RM)</h5>
                    <p class="card-text fs-1 fw-bold">{{ number_format($totalValue, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Kad Item Terbaru --}}
        <div class="col-md-6 col-lg-4">
             <div class="card shadow-sm h-100">
                <div class="card-header">Item Terbaru Ditambah</div>
                <div class="card-body p-0"> {{-- p-0 untuk list-group flush --}}
                    @if($recentItems->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach ($recentItems as $item)
                                <li class="list-group-item">
                                   <a href="{{ route('items.show', $item->id) }}" class="text-decoration-none">{{ $item->name }}</a>
                                   <small class="text-muted d-block">
                                       {{ $item->category->name ?? 'Tiada Kategori' }} | {{ $item->location->name ?? 'Tiada Lokasi' }}
                                   </small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="p-3 text-muted">Tiada item baru.</p>
                    @endif
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('items.index') }}">Lihat Semua Item</a>
                </div>
            </div>
        </div>

        {{-- Kad Item Kuantiti Rendah --}}
        <div class="col-md-6"> {{-- Contoh saiz kolum --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-box-fill me-2"></i> Item Kuantiti Rendah (< {{ $lowQuantityThreshold ?? 3 }})
                </div>
                @if($lowQuantityItems->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($lowQuantityItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <a href="{{ route('items.show', $item->id) }}" class="me-2">{{ $item->name }}</a>
                                <span class="badge bg-danger rounded-pill">
                                    {{ $item->quantity }} Unit
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="card-body">
                        <p class="text-muted mb-0">Tiada item kuantiti rendah buat masa ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kad Item Sedang Dipinjam --}}
        <div class="col-md-6"> {{-- Contoh saiz kolum --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-person-up-arrow me-2"></i> Item Sedang Dipinjam (Terbaru)
                </div>
                @if($borrowedItemsDashboard->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($borrowedItemsDashboard as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center">
                                {{-- Papar thumbnail jika ada --}}
                                @if ($item->primaryImage && $item->primaryImage->path !== 'placeholders/no-image.png')
                                    <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="Thumb" style="width: 30px; height: 30px; object-fit: cover;" class="me-2 img-thumbnail">
                                @endif
                                <a href="{{ route('items.show', $item->id) }}">{{ $item->name }}</a>
                            </div>
                                {{-- Mungkin tambah lokasi semasa? --}}
                                <span class="text-muted small">{{ $item->location->name ?? '' }}</span>
                            </li>
                        @endforeach
                        {{-- Pautan ke Laporan Penuh --}}
                        <li class="list-group-item text-center">
                            <a href="{{ route('reports.borrowedItems') }}" class="btn btn-outline-primary btn-sm">Lihat Laporan Penuh</a>
                        </li>
                    </ul>
                @else
                    <div class="card-body">
                        <p class="text-muted mb-0">Tiada item sedang dipinjam buat masa ini.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Item Hampir Tamat Jaminan (90 Hari) --}}
        <div class="col-md-6"> {{-- Atau saiz kolum lain --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-calendar-x me-2"></i> Item Hampir Tamat Jaminan (90 Hari)
                </div>
                {{-- Guna list-group untuk paparan kemas --}}
                @if($expiringSoonItems->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        @foreach($expiringSoonItems as $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                <a href="{{ route('items.show', $item->id) }}" class="me-2">{{ $item->name }}</a>
                                <span class="badge bg-warning rounded-pill">
                                    {{ $item->warranty_expiry_date->format('d/m/Y') }}
                                    ({{ $item->warranty_expiry_date->diffForHumans(null, true) }} lagi)
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="card-body">
                        <p class="text-muted mb-0">Tiada item dengan jaminan akan tamat dalam 90 hari.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kad Ringkasan Status Item --}}
        <div class="col-md-6"> {{-- Contoh saiz kolum --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <i class="bi bi-tags-fill me-2"></i> Ringkasan Status Item
                </div>
                @if($statusCounts->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        {{-- Loop melalui status dan bilangannya --}}
                        @foreach($statusCounts as $status => $count)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $status }}
                                <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="card-body">
                        <p class="text-muted mb-0">Tiada data status item untuk dipaparkan.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kad Item Top 5 Kategori --}}
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">Top 5 Kategori (Mengikut Bilangan Item)</div>
                 <div class="card-body">
                    @if($categoriesWithCount->count() > 0)
                        <ul class="list-group list-group-flush">
                             @foreach ($categoriesWithCount as $category)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $category->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $category->items_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                         <p class="text-muted">Tiada data kategori.</p>
                    @endif
                 </div>
                 <div class="card-footer text-center">
                    <a href="{{ route('categories.index') }}">Urus Kategori</a>
                </div>
            </div>
        </div>

        {{-- Kad Item Top 5 Lokasi --}}
        <div class="col-md-6">
             <div class="card shadow-sm">
                <div class="card-header">Top 5 Lokasi (Mengikut Bilangan Item)</div>
                 <div class="card-body">
                     @if($locationsWithCount->count() > 0)
                        <ul class="list-group list-group-flush">
                             @foreach ($locationsWithCount as $location)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $location->name }}
                                    <span class="badge bg-primary rounded-pill">{{ $location->items_count }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                         <p class="text-muted">Tiada data lokasi.</p>
                    @endif
                 </div>
                  <div class="card-footer text-center">
                    <a href="{{ route('locations.index') }}">Urus Lokasi</a>
                </div>
            </div>
        </div>

    </div> {{-- Akhir .row --}}
    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        <a class="btn btn-primary mb-3" href="{{ route('items.create') }}"><i class="bi bi-file-earmark-plus"></i> Item Baru</a>
        <a class="btn btn-info mb-3" href="{{ route('scanner.index') }}"><i class="bi bi-qr-code-scan"></i> Imbas</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
             | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>
@endsection
