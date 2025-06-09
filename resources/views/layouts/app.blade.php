<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> {{-- Guna locale aplikasi --}}
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @PwaHead
    {{-- Tajuk halaman dinamik: Guna 'yield' dengan nilai lalai --}}
    <title>@yield('title', 'InventoriKu')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Tempat untuk CSS spesifik halaman (jika perlu nanti) --}}
    @stack('styles')
</head>
<body>

    {{-- Bahagian Navigasi Mudah --}}
    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/icon-72x72.png') }}" {{-- Guna laluan dari public/, tukar nama fail jika perlu --}}
                alt="{{ config('app.name', 'Laravel') }} Logo"
                height="30" {{-- Tetapkan ketinggian (laraskan ikut keperluan) --}}
                class="d-inline-block align-top me-2"> {{-- Kelas Bootstrap: papar sebaris, jajar atas, jarak kanan --}}
                InventoriKu</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                {{-- Pautan Navigasi Utama (Untuk Pengguna Log Masuk) --}}
                @auth {{-- Hanya papar jika pengguna sudah log masuk --}}
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('items.index') }}">Senarai Item</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('movements.selectItem') }}">Pergerakan Item</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Laporan</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('scanner.index') }}">Imbas QR</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                        href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Urus Tadbir
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('categories.*') ? 'active' : '' }}"
                                    href="{{ route('categories.index') }}">Urus Kategori</a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ request()->routeIs('locations.*') ? 'active' : '' }}"
                                    href="{{ route('locations.index') }}">Urus Lokasi</a>
                            </li>
                            <li><hr class="dropdown-divider"></li> {{-- Pemisah jika perlu --}}
                            <li><a class="dropdown-item {{ request()->routeIs('admin.items.transfer.form') ? 'active' : '' }}" href="{{ route('admin.items.transfer.form') }}">Pindah Pemilik Item</a></li>
                            @role('Admin') {{-- akan dipaparkan hanya jika role adalah Admin --}}
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                                href="{{ route('admin.users.index') }}">Urus Pengguna</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                                href="{{ route('admin.roles.index') }}">Urus Peranan</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}"
                                href="{{ route('admin.permissions.index') }}">Lihat Kebenaran</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item {{ request()->routeIs('admin.feedback.*') ? 'active' : '' }}"
                                href="{{ route('admin.feedback.index') }}">Lihat Maklum Balas</a></li>
                            @endrole
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('feedback.*') ? 'active' : '' }}"
                           href="{{ route('feedback.index') }}">Maklum Balas</a>
                    </li>
                    {{-- Tambah pautan lain jika perlu --}}
                </ul>
                @endauth

                {{-- Bahagian Kanan Navbar (Login/Register atau Nama Pengguna) --}}
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> {{-- ms-auto tolak ke kanan --}}
                    @guest {{-- Papar jika pengguna ialah tetamu (belum log masuk) --}}
                        @if (Route::has('login'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Log Masuk') }}</a>
                            </li>
                        @endif
                        @if (Route::has('register'))
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">{{ __('Daftar') }}</a>
                            </li>
                        @endif
                    @else {{-- Papar jika pengguna sudah log masuk --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }} {{-- Papar nama pengguna --}}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a></li>
                                <li><a class="dropdown-item" href="{{ route('feedback.create') }}">Beri Maklum Balas</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    {{-- Borang Log Keluar --}}
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            {{ __('Log Keluar') }}
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>

    {{-- === LETAK BLOK MESEJ FLASH DI SINI === --}}
    <div class="container"> {{-- Container untuk hadkan lebar alert --}}
        {{-- Beri sedikit ruang dari navbar jika perlu (sudah ada padding-top pada body) --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ... (alert untuk warning & info jika ada) ... --}}

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                 <i class="bi bi-exclamation-octagon-fill me-2"></i> <strong>Sila perbaiki ralat berikut:</strong>
                 <ul class="mb-0 mt-2">
                     @foreach ($errors->all() as $error)
                         <li>{{ $error }}</li>
                     @endforeach
                 </ul>
                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>
    {{-- === AKHIR BLOK MESEJ FLASH === --}}

    {{-- Container utama untuk kandungan halaman --}}
    <main class="container mt-4">
        {{-- === BANNER PASANG PWA === --}}
        <div id="installPwaBanner" class="alert alert-info alert-dismissible fade show m-0 rounded-0 text-center" role="alert" style="display: none; position: sticky; top: 0; z-index: 1031;">
            {{-- Letak di atas navbar? Atau di bawah navbar? --}}
            {{-- Jika di bawah navbar, 'top' perlu ambil kira ketinggian navbar --}}
            <span>Mahu pasang InventoriKu untuk akses lebih pantas?</span>
            <button type="button" class="btn btn-primary btn-sm ms-3" id="installPwaButton">
                <i class="bi bi-download"></i> Ya, Pasang Sekarang!
            </button>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" id="dismissPwaBanner"></button>
        </div>
        {{-- === AKHIR BANNER PASANG PWA === --}}
        {{-- Kandungan Utama --}}
        @yield('content')
    </main>
    {{-- Tempat untuk Skrip JS spesifik halaman (jika perlu nanti) --}}
    @stack('scripts')
    @RegisterServiceWorkerScript {{-- <-- TAMBAH BARIS INI --}}
</body>
</html>
