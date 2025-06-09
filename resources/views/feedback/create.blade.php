@extends('layouts.app')

@section('title', 'Beri Maklum Balas')

@section('content')
<h1>Maklum Balas Pengguna</h1>
<p>Kami amat menghargai maklum balas anda untuk membantu kami menambah baik InventoriKu.</p>

<div class="card shadow-sm mt-4">
    <div class="card-body">
        <form action="{{ route('feedback.store') }}" method="POST">
            @csrf

            {{-- Rating (Contoh guna Select) --}}
             <div class="mb-3">
                <label for="rating" class="form-label">Rating Keseluruhan (Pilihan):</label>
                <select class="form-select @error('rating') is-invalid @enderror" name="rating" id="rating">
                    <option value="">-- Tiada Rating --</option>
                    <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>5 - Sangat Baik</option>
                    <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>4 - Baik</option>
                    <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>3 - Sederhana</option>
                    <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>2 - Kurang Baik</option>
                    <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>1 - Sangat Teruk</option>
                </select>
                 @error('rating')
                     <div class="invalid-feedback">{{ $message }}</div>
                 @enderror
             </div>

            {{-- Subjek (Pilihan) --}}
            <x-forms.input type="text" name="subject" label="Subjek (Pilihan):" :value="old('subject')" />

            {{-- Mesej (Wajib) --}}
            <x-forms.textarea name="message" label="Mesej Maklum Balas:" required>{{ old('message') }}</x-forms.textarea>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send"></i> Hantar Maklum Balas
                </button>
                <a href="{{ url()->previous('/') }}" class="btn btn-secondary">Batal</a> {{-- Kembali ke halaman sebelum ini --}}
            </div>
        </form>
    </div>
</div>
@endsection
