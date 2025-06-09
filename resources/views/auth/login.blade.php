@extends('layouts.app') {{-- Memastikan ia guna layout utama Tuan --}}

@section('title', 'Log Masuk') {{-- Menetapkan tajuk halaman --}}

@section('content')
<div class="row justify-content-center"> {{-- Menggunakan grid Bootstrap untuk meletakkan di tengah --}}
    <div class="col-md-8 col-lg-6"> {{-- Mengehadkan lebar borang pada skrin sederhana/besar --}}
        <div class="card shadow-sm"> {{-- Komponen Kad Bootstrap untuk membungkus borang --}}
            <div class="card-header">{{ __('Log Masuk') }}</div>

            <div class="card-body">

                {{-- Menggantikan komponen Breeze dengan Alert Bootstrap standard --}}
                @if (session('status'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Paparan Ralat Validasi Umum (jika ada) --}}
                {{-- Ini mungkin berguna jika ada ralat yang tidak spesifik pada medan --}}
                 @if ($errors->any() && !$errors->hasAny(['email', 'password']))
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    {{-- Menggunakan komponen input Tuan --}}
                    <x-forms.input name="email" type="email" label="Alamat Emel" :value="old('email')" required autofocus autocomplete="username" />

                    {{-- Menggunakan komponen input Tuan --}}
                    <x-forms.input name="password" type="password" label="Kata Laluan" required autocomplete="current-password" />

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" name="remember" id="remember">
                        <label class="form-check-label" for="remember">
                            {{ __('Ingat saya') }}
                        </label>
                    </div>

                    {{-- Bahagian Bawah Borang (Pautan & Butang) --}}
                    <div class="d-flex justify-content-end align-items-center mt-4">
                        {{-- Pautan Lupa Kata Laluan --}}
                        @if (Route::has('password.request'))
                            <a class="text-muted me-3 small text-decoration-none" href="{{ route('password.request') }}">
                                {{ __('Lupa kata laluan?') }}
                            </a>
                        @endif

                        {{-- Butang Log Masuk --}}
                        <button type="submit" class="btn btn-primary">
                            {{ __('Log Masuk') }}
                        </button>
                    </div>
                </form>
            </div> {{-- Akhir .card-body --}}
        </div> {{-- Akhir .card --}}

        {{-- Pautan ke halaman Pendaftaran --}}
        <div class="text-center mt-3">
            <p>Belum ada akaun? <a href="{{ route('register') }}">Daftar di sini</a></p>
        </div>

    </div> {{-- Akhir .col --}}
</div> {{-- Akhir .row --}}
{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">

    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>
@endsection {{-- Akhir section content --}}
