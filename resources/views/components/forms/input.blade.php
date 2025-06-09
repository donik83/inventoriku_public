{{-- Mendefinisikan 'props' yang boleh diterima oleh komponen ini --}}
{{-- type, name, label adalah wajib. id & value adalah pilihan. --}}
@props([
    'type' => 'text', // Jenis input lalai ialah 'text'
    'name',           // Nama input (diguna untuk name="" dan ralat)
    'label',          // Teks untuk label
    'id' => null,     // ID input (jika tidak diberi, guna nama)
    'value' => null   // Nilai asal (untuk edit form),
])

{{-- Struktur HTML menggunakan kelas Bootstrap --}}
<div class="mb-3">
    <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}:</label>
    <input type="{{ $type }}"
           id="{{ $id ?? $name }}"
           name="{{ $name }}"
           value="{{ old($name, $value) }}"  {{-- Guna old() atau nilai prop $value --}}
           {{-- $attributes->merge() membenarkan penambahan atribut lain seperti 'required', 'step', 'min' dari tag komponen --}}
           {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >
    {{-- Paparkan ralat validasi jika ada untuk nama medan ini --}}
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
