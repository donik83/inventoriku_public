{{--
Props:
- name: Nama atribut untuk <select> (wajib)
- label: Teks untuk <label> (wajib)
- options: Collection atau Array objek/data untuk pilihan dropdown (wajib). Diandaikan setiap option ada ->id dan ->name.
- selected: Nilai (biasanya ID) bagi pilihan yang perlu dipilih secara lalai (cth: $item->category_id). Pilihan (nullable).
- id: ID untuk elemen <select> (pilihan, guna nama jika tiada)
--}}
@props([
    'name',
    'label',
    'options', // Menerima senarai pilihan
    'selected' => null, // Nilai yang dipilih secara lalai (untuk edit)
    'id' => null
])

<div class="mb-3">
    <label for="{{ $id ?? $name }}" class="form-label">{{ $label }}:</label>
    <select name="{{ $name }}"
            id="{{ $id ?? $name }}"
            {{-- Gabungkan kelas form-select dan is-invalid jika ada ralat --}}
            {{ $attributes->merge(['class' => 'form-select ' . ($errors->has($name) ? 'is-invalid' : '')]) }}
    >
        {{-- Pilihan default --}}
        <option value="">-- Sila Pilih {{ $label }} --</option>

        {{-- Loop melalui $options yang dihantar dari Controller --}}
        @foreach ($options as $option)
            <option value="{{ $option->id }}"
                    {{-- Semak jika ID option ini sama dengan nilai old() atau nilai $selected --}}
                    {{ old($name, $selected ?? '') == $option->id ? 'selected' : '' }}
            >
                {{ $option->name }} {{-- Paparkan nama option --}}
            </option>
        @endforeach
    </select>

    {{-- Paparkan ralat validasi --}}
    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>
