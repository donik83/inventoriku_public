@extends('layouts.app')

@section('title', 'Pengurusan Maklum Balas')

@section('content')
<h1>Maklum Balas Pengguna</h1>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Daripada</th>
                        <th>Subjek</th>
                        <th>Rating</th>
                        <th>Mesej (Petikan)</th>
                        <th>Dihantar Pada</th>
                        <th>Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feedbacks as $feedback)
                        <tr>
                            {{-- Nombor urutan --}}
                            <td>{{ $loop->iteration + $feedbacks->firstItem() - 1 }}</td>
                            {{-- Nama pengguna (jika wujud) --}}
                            <td>{{ $feedback->user->name ?? 'Pengguna Dipadam' }}</td>
                            {{-- Subjek atau tiada --}}
                            <td>{{ $feedback->subject ?? '-' }}</td>
                            {{-- Rating atau tiada --}}
                            <td class="text-center">
                                @if($feedback->rating)
                                    {{ $feedback->rating }} <i class="bi bi-star-fill text-warning"></i>
                                @else
                                    -
                                @endif
                            </td>
                            {{-- Petikan mesej --}}
                            <td>{{ Str::limit($feedback->message, 50) }}</td>
                            {{-- Tarikh dihantar --}}
                            <td class="text-nowrap">{{ $feedback->created_at->format('d/m/Y H:i') }}</td>

                            <td>
                                <div class="d-flex justify-content-start gap-1">
                                    @if(!$feedback->admin_reply)
                                    {{-- Jika belum dibalas, tunjuk butang Balas yg buka modal --}}
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#replyModal{{ $feedback->id }}">
                                        <i class="bi bi-reply-fill"></i> Balas
                                    </button>
                                    @else
                                        {{-- Jika sudah dibalas, tunjuk status --}}
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Sudah Dibalas</span>
                                    @endif
                                    {{-- Butang Padam (belum implement) --}}
                                    {{-- BORANG PADAM MAKLUM BALAS --}}
                                    <form action="{{ route('admin.feedback.destroy', $feedback->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Adakah anda pasti mahu memadam maklum balas ini? Tindakan ini tidak boleh dibuat asal.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" title="Padam Maklum Balas">
                                            <i class="bi bi-trash"></i> Padam
                                        </button>
                                    </form>
                                    {{-- AKHIR BORANG PADAM --}}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Tiada maklum balas diterima lagi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            {{-- === MODAL REPLY (Letak selepas jadual, sebelum pagination?) === --}}
            @foreach ($feedbacks as $feedback)
            {{-- Cipta modal untuk setiap feedback, hanya jika belum dibalas --}}
            @if(!$feedback->admin_reply)
            <div class="modal fade" id="replyModal{{ $feedback->id }}" tabindex="-1" aria-labelledby="replyModalLabel{{ $feedback->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="{{ route('admin.feedback.reply', $feedback) }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="replyModalLabel{{ $feedback->id }}">Balas Maklum Balas #{{ $feedback->id }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">Daripada:</label>
                                    <p class="form-control-plaintext">{{ $feedback->user->name ?? 'N/A' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subjek:</label>
                                    <p class="form-control-plaintext">{{ $feedback->subject ?? '-' }}</p>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Mesej Asal:</label>
                                    <div class="p-2 border rounded bg-light">{{ nl2br(e($feedback->message)) }}</div>
                                </div>
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
@endif
{{-- AKHIR BAHAGIAN BALASAN --}}
                                <hr>
                                {{-- Textarea untuk balasan Admin --}}
                                <x-forms.textarea name="admin_reply" label="Teks Balasan Anda:" rows="5" required></x-forms.textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i> Hantar Balasan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
            {{-- === AKHIR MODAL REPLY === --}}
        </div>
         {{-- Pautan Paginasi --}}
         <div class="mt-3">
             {{ $feedbacks->links() }}
         </div>
    </div>
</div>
    <div class="fixed-bottom bg-light p-3 border-top shadow-sm text-center">
        <a class="btn btn-info mb-3" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <br>
        <span class="text-muted small">
            {{ config('app.name', 'InventoriKu') }} Versi {{ config('app.version', '1.0.0') }}
             | Hak Cipta &copy; {{ date('Y') }} {{-- Contoh tambah copyright --}}
        </span>
    </div>
@endsection
