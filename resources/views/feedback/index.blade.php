@extends('layouts.app')

@section('title', 'Lihat Maklum Balas')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Maklum Balas Komuniti</h1>
    <a href="{{ route('feedback.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Beri Maklum Balas Anda
    </a>
</div>

@if ($feedbacks->isEmpty())
    <div class="alert alert-info">Tiada maklum balas untuk dipaparkan pada masa ini. Jadilah yang pertama!</div>
@else
    {{-- Loop melalui setiap feedback --}}
    @foreach ($feedbacks as $feedback)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <strong class="me-2">{{ $feedback->subject ?? 'Tiada Subjek' }}</strong>
                    @if($feedback->rating)
                        <span style="color: #ffc107;"> @for ($i = 1; $i <= 5; $i++)
                                <i class="bi {{ $i <= $feedback->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                            @endfor
                        </span>
                    @endif
                </div>
                <small class="text-muted">
                    Oleh: {{ $feedback->user->name ?? 'Pengguna Tidak Diketahui' }} | {{ $feedback->created_at->diffForHumans() }}
                </small>
            </div>
            <div class="card-body">
                <p class="card-text">{!! nl2br(e($feedback->message)) !!}</p> {{-- Papar mesej dengan line break --}}
            </div>
            {{-- Bahagian untuk Balasan Admin akan ditambah di sini nanti --}}
            {{-- @if($feedback->admin_reply) ... @endif --}}
        </div>
        {{-- ... (kod mesej asal) ... --}}
    </div> {{-- Tutup card-body asal --}}

    {{-- BAHAGIAN BALASAN ADMIN --}}
    @if($feedback->admin_reply)
        <div class="card-footer bg-success-subtle text-success-emphasis"> {{-- Guna footer kad untuk beza --}}
             <p class="small mb-1">
                <strong><i class="bi bi-arrow-return-right"></i> Dibalas oleh:</strong>
                {{ $feedback->repliedByAdmin->name ?? 'Admin' }}
                pada {{ $feedback->replied_at?->format('d/m/Y H:i') }}
             </p>
             <p class="mb-0 small fst-italic">{!! nl2br(e($feedback->admin_reply)) !!}</p>
        </div>
        <br>
        <br>
    @endif
    {{-- AKHIR BAHAGIAN BALASAN --}}

</div> {{-- Tutup div card --}}
    @endforeach

    {{-- Pautan Paginasi --}}
    <div class="mt-4">
        {{ $feedbacks->links() }}
    </div>
@endif

    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
             | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>

@endsection
