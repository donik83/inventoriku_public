<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0">{{ __('Informasi Profil') }}</h2>
        <p class="mt-1 text-muted small">
            {{ __("Kemas kini informasi profil dan alamat emel akaun anda.") }}
        </p>
    </div>
    <div class="card-body">
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>

        <form method="post" action="{{ route('profile.update') }}">
            @csrf
            @method('patch')

            <x-forms.input name="name" label="Nama Penuh" :value="old('name', $user->name)" required autofocus autocomplete="name" />

            <div>
                <x-forms.input name="email" type="email" label="Alamat Emel" :value="old('email', $user->email)" required autocomplete="username" />

                {{-- Bahagian untuk pengesahan emel jika perlu --}}
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 small text-muted">
                        {{ __('Alamat emel anda belum disahkan.') }}

                        <button form="send-verification" class="btn btn-link p-0 m-0 align-baseline text-decoration-none">
                            {{ __('Klik di sini untuk menghantar semula emel pengesahan.') }}
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 small text-success">
                            {{ __('Pautan pengesahan baru telah dihantar ke alamat emel anda.') }}
                        </p>
                    @endif
                @endif
            </div>

            <div class="d-flex align-items-center gap-4 mt-4">
                <button type="submit" class="btn btn-primary">{{ __('Simpan') }}</button>

                {{-- Papar mesej 'Saved.' jika status ialah 'profile-updated' --}}
                @if (session('status') === 'profile-updated')
                    <span class="text-success small">{{ __('Tersimpan.') }}</span>
                @endif
            </div>
        </form>
    </div>
</div>
