<div class="card shadow-sm">
    <div class="card-header">
        <h2 class="h5 mb-0">{{ __('Padam Akaun') }}</h2>
        <p class="mt-1 text-muted small">
            {{ __('Setelah akaun anda dipadamkan, semua sumber dan datanya akan dipadamkan secara kekal. Sebelum memadamkan akaun anda, sila muat turun sebarang data atau maklumat yang ingin anda simpan.') }}
       </p>
   </div>
    <div class="card-body">
       {{-- Butang untuk membuka modal --}}
       <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
           {{ __('Padam Akaun') }}
       </button>
   </div>
</div>

<div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="confirmUserDeletionModalLabel" aria-hidden="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <form method="post" action="{{ route('profile.destroy') }}">
               @csrf
               @method('delete')

               <div class="modal-header">
                   <h5 class="modal-title" id="confirmUserDeletionModalLabel">{{ __('Adakah anda pasti?') }}</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <p>{{ __('Setelah akaun anda dipadamkan, semua sumber dan datanya akan dipadamkan secara kekal.') }}</p>
                   <p>{{ __('Sila masukkan kata laluan anda untuk mengesahkan anda ingin memadamkan akaun anda secara kekal.') }}</p>

                   {{-- Input Kata Laluan Pengesahan --}}
                   <x-forms.input name="password" type="password" label="Kata Laluan" required placeholder="Kata Laluan" autofocus/>
                    {{-- Nota: Ralat validasi untuk modal mungkin perlu pengendalian khas jika mahu --}}

               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Batal') }}</button>
                   <button type="submit" class="btn btn-danger">{{ __('Padam Akaun') }}</button>
               </div>
           </form>
       </div>
   </div>
</div>
