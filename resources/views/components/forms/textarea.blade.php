{{-- Props: name & label wajib, value & rows & id pilihan --}}
@props([
    'name',
    'label',
    'value' => null,
    'rows' => 3, // Nilai lalai untuk atribut rows
    'id' => null
])

<div class="mb-3">
    <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}:</label>
    {{-- Gunakan tag <textarea> --}}
    <textarea id="{{ $id ?? $name }}"
              name="{{ $name }}"
              rows="{{ $rows }}" {{-- Guna prop rows --}}
              {{-- $attributes->merge() untuk kelas dan atribut lain --}}
              {{ $attributes->merge(['class' => 'form-control ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >{{ old($name, $value) }}</textarea> {{-- Nilai diletak di antara tag --}}

    {{-- Paparan Ralat --}}
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
