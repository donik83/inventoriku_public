{{-- resources/views/items/_item-card.blade.php --}}
{{-- Menerima $item sebagai input --}}
<div class="card shadow-sm mb-3"> {{-- Kad untuk setiap item --}}
    <div class="card-body">
        <div class="row g-3">
            {{-- Kolum Kiri: Gambar --}}
            <div class="col-3 text-center">
                @if ($item->primaryImage && $item->primaryImage->path !== 'placeholders/no-image.png')
                    <img src="{{ asset('storage/' . $item->primaryImage->path) }}" alt="Thumb" style="width: 60px; height: 60px; object-fit: cover;" class="img-thumbnail">
                @else
                     <div class="img-thumbnail d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background-color: #eee;">
                         <small class="text-muted">Tiada</small>
                     </div>
                @endif
            </div>

            {{-- Kolum Kanan: Butiran Utama --}}
            <div class="col-9">
                <h5 class="card-title mb-1" style="font-size: 1rem;">
                    @if($item->is_private)
                        <i class="bi bi-lock-fill text-secondary me-1" title="Peribadi"></i>
                    @else
                        <i class="bi bi-globe-americas text-success me-1" title="Umum"></i>
                    @endif
                    <a href="{{ route('items.show', $item->id) }}">{{ $item->name }}</a>
                </h5>
                <p class="card-text mb-1">
                    <small class="text-muted">
                        <i class="bi bi-tag-fill"></i> {{ $item->category->name ?? '-' }} <br>
                        <i class="bi bi-geo-alt-fill"></i> {{ $item->location->name ?? '-' }}
                    </small>
                </p>
                 <p class="card-text mb-0">
                    <small>Qty: {{ $item->quantity }} | Status: {{ $item->status }}</small>
                </p>
            </div>

             {{-- Baris Bawah: Tindakan --}}
             <div class="col-12">
                  <hr class="my-2">
                  <div class="d-flex justify-content-end gap-1">
                      {{-- Butang Lihat/Edit/Padam dengan @can --}}
                      <a href="{{ route('items.show', $item->id) }}" class="btn btn-info btn-sm" title="Lihat">
                          <i class="bi bi-eye"></i> <span class="d-none d-sm-inline">Lihat</span> {{-- Sembunyi teks pada xs --}}
                      </a>
                      @can('update', $item)
                          <a href="{{ route('items.edit', $item->id) }}" class="btn btn-warning btn-sm" title="Edit">
                              <i class="bi bi-pencil-square"></i> <span class="d-none d-sm-inline">Edit</span>
                          </a>
                      @endcan
                      @can('delete', $item)
                          <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('AWAS!\n\nPadam item \'{{ $item->name }}\'?');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger btn-sm" title="Padam">
                                  <i class="bi bi-trash"></i> <span class="d-none d-sm-inline">Padam</span>
                              </button>
                          </form>
                      @endcan
                  </div>
             </div>

        </div>
    </div>
</div>
