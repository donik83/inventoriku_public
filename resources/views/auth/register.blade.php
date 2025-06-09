@extends('layouts.app') {{-- Guna layout utama Tuan --}}

@section('title', 'Daftar Akaun Baru') {{-- Tetapkan tajuk halaman --}}

@section('content')
<div class="row justify-content-center"> {{-- Letak kad di tengah --}}
    <div class="col-md-8 col-lg-6"> {{-- Hadkan lebar kad --}}
        <div class="card shadow-sm"> {{-- Kad Bootstrap --}}
            <div class="card-header">{{ __('Daftar') }}</div>

            <div class="card-body">

                 {{-- Paparkan Ralat Validasi Umum (jika ada) --}}
                 @if ($errors->any() && !$errors->hasAny(['name', 'email', 'password']))
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <x-forms.input name="name" label="Nama Penuh" :value="old('name')" required autofocus autocomplete="name" />

                    <x-forms.input name="email" type="email" label="Alamat Emel" :value="old('email')" required autocomplete="username" />

                    <x-forms.input name="password" type="password" label="Kata Laluan" required autocomplete="new-password" />

                    <x-forms.input name="password_confirmation" type="password" label="Sahkan Kata Laluan" required autocomplete="new-password" />


                    <div class="d-flex justify-content-end align-items-center mt-4">
                        {{-- Pautan ke halaman Log Masuk --}}
                        <a class="text-muted me-3 small text-decoration-none" href="{{ route('login') }}">
                            {{ __('Sudah berdaftar?') }}
                        </a>

                        {{-- Butang Daftar --}}
                        <button type="submit" class="btn btn-primary">
                            {{ __('Daftar') }}
                        </button>
                    </div>
                </form>
            </div> {{-- Akhir .card-body --}}
        </div> {{-- Akhir .card --}}
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
