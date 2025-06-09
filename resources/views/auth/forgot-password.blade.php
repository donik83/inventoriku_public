@extends('layouts.app')

@section('title', 'Lupa Kata Laluan')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('Lupa Kata Laluan?') }}</div>

            <div class="card-body">
                <div class="mb-4 text-muted small">
                    {{ __('Lupa kata laluan anda? Tiada masalah. Hanya beritahu kami alamat emel anda dan kami akan emelkan pautan tetapan semula kata laluan yang membolehkan anda memilih yang baru.') }}
                </div>

                @if (session('status'))
                    <div class="alert alert-success mb-4" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Paparkan Ralat Validasi Umum --}}
                 @if ($errors->any() && !$errors->hasAny(['email']))
                    <div class="alert alert-danger mb-4">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <x-forms.input name="email" type="email" label="Alamat Emel" :value="old('email')" required autofocus />

                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Hantar Pautan Tetapan Semula Kata Laluan') }}
                        </button>
                    </div>
                </form>
                <div class="text-center mt-3">
                     <a href="{{ route('login') }}">Kembali ke Log Masuk</a>
                </div>
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
