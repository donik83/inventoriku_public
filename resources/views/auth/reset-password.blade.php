@extends('layouts.app')

@section('title', 'Tetapkan Semula Kata Laluan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('Tetapkan Semula Kata Laluan') }}</div>

            <div class="card-body">

                 {{-- Paparkan Ralat Validasi Umum --}}
                 @if ($errors->any() && !$errors->hasAny(['email', 'password']))
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}"> {{-- Guna route 'password.store' --}}
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Pra-isi dengan emel dari pautan, guna old() sebagai sandaran --}}
                    <x-forms.input name="email" type="email" label="Alamat Emel" :value="old('email', $request->email)" required autofocus />

                    <x-forms.input name="password" type="password" label="Kata Laluan Baru" required autocomplete="new-password" />

                    <x-forms.input name="password_confirmation" type="password" label="Sahkan Kata Laluan Baru" required autocomplete="new-password" />

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Tetapkan Semula Kata Laluan') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Butang/footer --}}
<div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">

    <br>
    <span class="text-muted small">
        {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
        | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
    </span>
</div>
@endsection
