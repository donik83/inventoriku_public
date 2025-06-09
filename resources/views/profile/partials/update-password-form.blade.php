<div class="card shadow-sm">
    <div class="card-header">
         <h2 class="h5 mb-0">{{ __('Kemas kini Kata Laluan') }}</h2>
         <p class="mt-1 text-muted small">
             {{ __('Pastikan akaun anda menggunakan kata laluan yang panjang dan rawak untuk kekal selamat.') }}
        </p>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <x-forms.input name="current_password" type="password" label="Kata Laluan Semasa" required autocomplete="current-password" />

            <x-forms.input name="password" type="password" label="Kata Laluan Baru" required autocomplete="new-password" />

            <x-forms.input name="password_confirmation" type="password" label="Sahkan Kata Laluan Baru" required autocomplete="new-password" />

            <div class="d-flex align-items-center gap-4 mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>

                {{-- Papar mesej 'Saved.' jika status ialah 'password-updated' --}}
                @if (session('status') === 'password-updated')
                     <span class="text-success small">{{ __('Tersimpan.') }}</span>
                @endif
            </div>
        </form>
    </div>
</div>
