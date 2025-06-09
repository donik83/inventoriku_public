@extends('layouts.app') {{-- Guna layout utama --}}

@section('title', 'Sahkan Alamat Emel')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header">{{ __('Sahkan Alamat Emel Anda') }}</div>

            <div class="card-body">
                <div class="mb-4 text-muted small">
                    {{ __('Terima kasih kerana mendaftar! Sebelum bermula, bolehkah anda mengesahkan alamat emel anda dengan mengklik pada pautan yang baru kami emelkan kepada anda? Jika anda tidak menerima emel tersebut, kami dengan senang hati akan menghantar yang lain.') }}
                </div>

                {{-- Papar mesej jika pautan baru telah dihantar --}}
                @if (session('status') == 'verification-link-sent')
                    <div class="alert alert-success mb-4" role="alert">
                        {{ __('Pautan pengesahan baru telah dihantar ke alamat emel yang anda berikan semasa pendaftaran.') }}
                    </div>
                @endif

                <div class="mt-4 d-flex align-items-center justify-content-between">
                    {{-- Butang untuk Hantar Semula Emel --}}
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            {{ __('Hantar Semula Emel Pengesahan') }}
                        </button>
                    </form>

                    {{-- Butang Log Keluar --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-secondary ms-3">
                            {{ __('Log Keluar') }}
                        </button>
                    </form>
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
