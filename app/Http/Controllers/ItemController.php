<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category; // <-- Import Model Category
use App\Models\Location; // <-- Import Model Location
use Illuminate\Http\Request; // <-- Import class Request
use Illuminate\Http\RedirectResponse; // <-- Import class RedirectResponse
use Illuminate\View\View; // <-- Import class View
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN BARIS INI
use Intervention\Image\Laravel\Facades\Image; // <-- Guna fasad Intervention v3
use Illuminate\Support\Str; // <-- Untuk menjana nama fail unik
use SimpleSoftwareIO\QrCode\Facades\QrCode; // <-- TAMBAH INI
use App\Models\ItemImage; // <-- TAMBAH/PASTIKAN ADA
use Illuminate\Support\Facades\DB; // <-- TAMBAH/PASTIKAN ADA untuk Transaction
use Illuminate\Validation\ValidationException; // <-- Tambah untuk had fail
use Illuminate\Support\Facades\Auth; // Untuk dapatkan pengguna log masuk
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // <-- PASTIKAN INI ADA
use Illuminate\Support\Facades\Gate; // <-- TAMBAH INI


class ItemController extends Controller
{
    use AuthorizesRequests; // <-- TAMBAH ATAU PASTIKAN BARIS INI ADA
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Gantikan $this->authorize('viewAny', Item::class); dengan:
        if (! Gate::allows('viewAny', Item::class)) {
            abort(403);
        }
        // 1. Dapatkan input carian dan penapis dari query string
        $searchTerm = $request->query('search');
        $selectedCategoryId = $request->query('category_id'); // Input baru
        $selectedLocationId = $request->query('location_id'); // Input baru

        // 2. Dapatkan SEMUA kategori & lokasi untuk pilihan dropdown dalam view
        $userId = Auth::id();
        // Dapatkan semua kategori, susun ikut nama
        $categories = \App\Models\Category::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();
        // Dapatkan semua lokasi, susun ikut nama
        $locations = \App\Models\Location::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();

        // 3. Mulakan pertanyaan Eloquent untuk Item, termasuk eager loading
        $query = Item::with(['category', 'location', 'primaryImage']);
        $user = auth()->user(); // Dapatkan pengguna semasa

        // Guna skop jika pengguna BUKAN Admin
        if (!$user->hasRole('Admin')) {
            $query->where(function ($q) use ($user) {
                $q->where('is_private', false) // Item adalah umum (public)
                ->orWhere('owner_user_id', $user->id); // ATAU item ini milik pengguna semasa
            });
        }

        // 4. Tambah syarat 'where' untuk carian teks (jika ada)
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // 5. Tambah syarat 'where' untuk penapis Kategori (jika dipilih & sah)
        if ($selectedCategoryId) { // Hanya tambah jika ada nilai dipilih
            $query->where('category_id', $selectedCategoryId);
        }

        // 6. Tambah syarat 'where' untuk penapis Lokasi (jika dipilih & sah)
        if ($selectedLocationId) { // Hanya tambah jika ada nilai dipilih
            $query->where('location_id', $selectedLocationId);
        }

        // 7. Laksanakan pertanyaan dengan susunan & paginasi
        //    withQueryString() akan sertakan SEMUA parameter (search, category_id, location_id)
        $items = $query->sortable()->paginate(10)->withQueryString();

        // 8. Hantar semua data yang diperlukan ke view
        return view('items.index', compact(
            'items',
            'searchTerm',
            'categories', // Hantar senarai penuh kategori
            'locations',  // Hantar senarai penuh lokasi
            'selectedCategoryId', // Hantar ID kategori yang dipilih
            'selectedLocationId'  // Hantar ID lokasi yang dipilih
        ));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View // <-- Tetapkan jenis pulangan ialah View
    {
        // Gantikan $this->authorize('create', Item::class); dengan:
        if (! Gate::allows('create', Item::class)) {
            abort(403);
        }
        
        $userId = Auth::id();
        // Dapatkan semua kategori, susun ikut nama
        $categories = \App\Models\Category::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();
        // Dapatkan semua lokasi, susun ikut nama
        $locations = \App\Models\Location::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();

        // Ambil barcode_data dari query string (jika ada)
        $barcodeData = $request->query('barcode_data');

        // Hantar ke view // Muatkan view 'items.create' dan hantar data $categories dan $locations dan barcodeData
        return view('items.create', compact('categories', 'locations', 'barcodeData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse // <-- Tetapkan jenis pulangan ialah RedirectResponse
    {
        // Gantikan $this->authorize('create', Item::class); dengan:
        if (! Gate::allows('create', Item::class)) {
            abort(403);
        }
        // 1. Pengesahan Data (Validation)
        // Tentukan peraturan untuk setiap medan borang
        $validatedData = $request->validate([
            'name' => 'required|string|max:255', // Wajib ada, jenis string, maks 255 char
            'description' => 'nullable|string', // Boleh kosong, jenis string
            'category_id' => 'nullable|integer|exists:categories,id', // Boleh kosong, integer, mesti wujud dalam jadual categories lajur id
            'location_id' => 'nullable|integer|exists:locations,id', // Boleh kosong, integer, mesti wujud dalam jadual locations lajur id
            'purchase_date' => 'nullable|date', // Boleh kosong, jenis tarikh
            'purchase_price' => 'nullable|numeric|min:0', // Boleh kosong, nombor, min 0
            'quantity' => 'required|integer|min:1', // Wajib ada, integer, min 1
            'serial_number' => 'nullable|string|max:255', // Boleh kosong, string, maks 255
            'barcode_data' => 'nullable|string|max:255',
            'warranty_expiry_date' => 'nullable|date|after_or_equal:purchase_date', // Boleh kosong, tarikh, mesti selepas/sama dgn tarikh beli
            'images' => 'nullable|array|max:5', // Boleh null, mesti array, maks 5 fail
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:20000', // Setiap fail mesti imej, jenis dibenarkan, maks 10MB
            'receipt' => 'nullable|file|mimes:pdf,txt,doc,docx,xlsx,xls|max:20000', // Contoh: PDF sahaja, maks 5MB
            'status' => 'required|string|max:50', // Wajib ada, string, maks 50
            'is_private' => 'required|boolean', // <-- TAMBAH INI
            // nullable: Gambar tidak wajib
            // image: Mesti fail gambar
            // mimes: Jenis fail dibenarkan (jpeg, png, jpg, gif)
            // max: Saiz maksimum dalam kilobyte (2048KB = 2MB)
        ]);

        // --- TAMBAH BAHAGIAN INI ---
        // Tetapkan pemilik item kepada ID pengguna yang sedang log masuk
        $validatedData['owner_user_id'] = Auth::id();
        // ---------------------------

        // Uruskan Muat Naik Gambar jika ada
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            // Jana nama fail unik berdasarkan UUID + extension asal
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            // Tentukan laluan simpanan
            $storagePath = 'item-images/' . $filename;

            // Baca imej menggunakan Intervention Image
            $image = Image::read($file);

            // Kecilkan saiz imej jika perlu (contoh: maks 1024px lebar/tinggi, kekalkan aspek rasio)
            // Nilai 1024px selalunya menghasilkan fail < 1MB-2MB kecuali imej sangat kompleks.
            // Tuan boleh laraskan nilai maxWidth/maxHeight ini.
            $image->scaleDown(1024, 1024);

            // Encode imej (contoh: sebagai JPEG dengan kualiti 80%)
            // Tuan boleh guna ->toPng(), ->toGif(), ->toWebp() juga.
            $encodedImage = $image->toJpeg(80);

            // Simpan imej yang telah diubahsuai ke storage 'public'
            Storage::disk('public')->put($storagePath, $encodedImage);

            // Simpan laluan fail BARU ke dalam data untuk disimpan ke DB
            $validatedData['image_path'] = $storagePath;
        }

        // Logik simpan PDF (BARU)
        if ($request->hasFile('receipt') && $request->file('receipt')->isValid()) {
            // Simpan fail PDF dalam storage/app/public/item-receipts
            // Nama fail unik akan dijana secara automatik
            $receiptPath = $request->file('receipt')->store('item-receipts', 'public');

            // Simpan laluan fail PDF ke dalam array data
            $validatedData['receipt_path'] = $receiptPath;

        }

        // Cipta Rekod Baru dalam Pangkalan Data (termasuk image_path jika ada)
        // Jika validasi gagal, Laravel akan automatik redirect kembali ke borang dengan mesej ralat

        // 2. Cipta Rekod Baru dalam Pangkalan Data
        // Gunakan kaedah create() pada Model Item.
        // Ini berfungsi kerana kita sudah tetapkan $fillable dalam Model Item.
        // 1. Cipta rekod item dahulu (tanpa image_path lagi)
        // Pastikan 'image_path' sudah dibuang dari $fillable dalam Item.php
        $item = Item::create($validatedData);

        // 2. Proses Muat Naik Pelbagai Gambar (Selepas item dicipta)
        if ($request->hasFile('images')) {
            $isFirstImage = true; // Flag untuk gambar pertama
            foreach ($request->file('images') as $index => $imageFile) {
                if ($imageFile->isValid()) {
                    // Guna Intervention Image untuk resize
                    $image = Image::read($imageFile);
                    $image->scaleDown(width: 1024, height: 1024); // Atau saiz lain Tuan mahu

                    // Jana nama fail unik & laluan
                    $extension = $imageFile->getClientOriginalExtension();
                    $filename = Str::uuid() . '.' . $extension;
                    $path = 'item-images/' . $filename;

                    // Simpan imej yang diubahsuai ke disk 'public'
                    Storage::disk('public')->put($path, (string) $image->encode()); // Pastikan encode ke string

                    // Cipta rekod dalam jadual item_images
                    $item->images()->create([ // Guna hubungan 'images()'
                        'path' => $path,
                        'is_primary' => $isFirstImage, // Set true jika ia gambar pertama
                        'order' => $index + 1 // Contoh simpan susunan
                    ]);

                    $isFirstImage = false; // Reset flag selepas gambar pertama
                }
            }
        } // Akhir Proses Gambar


        // 3. Redirect ke Halaman Senarai Item dengan Mesej Kejayaan
        // Gunakan route() helper untuk jana URL ke index
        // Gunakan with() untuk hantar mesej kilat (flash message) kejayaan
        return redirect()->route('items.index')
                     ->with('success', 'Item inventori baru (bersama gambar) berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item): View // Terima objek $item secara automatik
    {
        //dd($item); // <-- TAMBAH INI UNTUK DEBUG
        //$this->authorize('view', Item::class); // <-- TAMBAH INI
        if (! Gate::allows('view', $item)) {
            abort(403); // Hasilkan ralat 403 jika Gate::allows() pulangkan false
        }
        // Eager load hubungan yang diperlukan untuk paparan butiran DAN sejarah
        $item->load([
            'category', // Untuk butiran item
            'location', // Untuk butiran item
            // TAMBAH 'images' di sini, susun ikut order/id
            'images' => function ($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
            },
            'itemMovements' => function ($query) { // Muatkan sejarah pergerakan
                // Untuk setiap pergerakan, muatkan juga pengguna & lokasi destinasi
                // Susun ikut yang terbaru dahulu
                $query->with(['user', 'destinationLocation'])->latest();
            }
        ]);

        // Hanya perlu hantar objek $item ke view 'items.show'
        // Data hubungan (category, location) akan dimuatkan secara 'lazy loading'
        // apabila diakses dalam view, yang OK untuk paparan satu item.

        // Hantar objek $item (yang kini mengandungi data pergerakan) ke view
        return view('items.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item): View // Terima objek $item secara automatik
    {
        // Gantikan $this->authorize('update', $item); dengan:
        if (! Gate::allows('update', $item)) {
            abort(403);
        }
        // Eager load hubungan yang diperlukan untuk borang
        $item->load(['category', 'location', 'images' => function ($query) {
                $query->orderBy('order', 'asc')->orderBy('id', 'asc');
        }]); // <-- Tambah 'images' dengan susunan
        // Dapatkan semua kategori dan lokasi (sama seperti create())
        $userId = Auth::id();
        // Dapatkan semua kategori, susun ikut nama
        $categories = \App\Models\Category::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();
        // Dapatkan semua lokasi, susun ikut nama
        $locations = \App\Models\Location::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->get();

        // Muatkan view 'items.edit' dan hantar data item ($item),
        // serta senarai kategori ($categories) dan lokasi ($locations)
        return view('items.edit', compact('item', 'categories', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item): RedirectResponse // Terima $request dan objek $item
    {
        // Gantikan $this->authorize('update', $item); dengan:
        if (! Gate::allows('update', $item)) {
            abort(403);
        }
        // 1. Validasi Asas untuk Medan Item (seperti dalam store, tapi unik diubah)
        $validatedItemData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'location_id' => 'nullable|integer|exists:locations,id', // <-- Ubah validasi jika perlu
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'quantity' => 'required|integer|min:0', // Benarkan 0 jika LARAS STOK guna edit? Atau min:1?
            'serial_number' => 'nullable|string|max:255',
            'warranty_expiry_date' => 'nullable|date|after_or_equal:purchase_date',
            'status' => 'required|string|max:50',
            'is_private' => 'required|boolean', // <-- TAMBAH INI
            'receipt' => 'nullable|file|mimes:pdf,txt,doc,docx,xlsx,xls|max:20000',
            'barcode_data' => 'nullable|string|max:100', // Validasi barcode
            // Jangan validasi 'image' atau 'receipt' di sini lagi jika sudah tiada medan itu
        ]);

        // 2. Validasi Input Berkaitan Gambar
        $validatedImageData = $request->validate([
            'images' => 'nullable|array', // 'images' kini array fail baru
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:20000', // Validasi setiap fail baru
            'delete_images' => 'nullable|array', // Array ID gambar untuk dipadam
            'delete_images.*' => 'nullable|integer|exists:item_images,id', // Pastikan ID wujud
            'primary_image_id' => 'nullable|integer|exists:item_images,id', // ID gambar utama baru
        ]);

        // Ambil input spesifik
        $imagesToDelete = $request->input('delete_images', []);
        $newPrimaryImageId = $request->input('primary_image_id');
        $newImageFiles = $request->file('images');

        // Mulakan DB Transaction
        try {
            DB::transaction(function () use ($request, $item, $validatedItemData, $imagesToDelete, $newPrimaryImageId, $newImageFiles) {

                // A: Uruskan Kemas Kini Fail Resit/Manual DAHULU
                if ($request->hasFile('receipt') && $request->file('receipt')->isValid()) {
                    // Padam resit lama jika wujud
                    if ($item->receipt_path) {
                        Storage::disk('public')->delete($item->receipt_path);
                    }
                    // Simpan resit baru
                    $receiptPath = $request->file('receipt')->store('item-receipts', 'public');
                    // Terus kemas kini atribut pada model Item
                    $item->receipt_path = $receiptPath;
                    $itemUpdated = true; // Tandakan item perlu disimpan
                }
                // Nota: Tiada logik untuk padam resit secara eksplisit tanpa ganti fail baru buat masa ini.

                // A: Kemas kini data Item asas dahulu
                $item->update($validatedItemData);

                // B: Proses Pemadaman Gambar Sedia Ada
                $deletedImageIds = [];
                if (!empty($imagesToDelete)) {
                    $images = $item->images()->whereIn('id', $imagesToDelete)->get();
                    foreach ($images as $imageToDelete) {
                        // Pastikan gambar milik item ini (double check)
                        if ($imageToDelete->item_id == $item->id) {
                            Storage::disk('public')->delete($imageToDelete->path); // Padam fail fizikal
                            $imageToDelete->delete(); // Padam rekod dari DB
                            $deletedImageIds[] = $imageToDelete->id; // Simpan ID yang dipadam
                        }
                    }
                }

                // C: Proses Muat Naik Gambar Baru
                $newlyUploadedImageIds = [];
                $firstNewImagePath = null; // Untuk set primary jika tiada pilihan dibuat
                $firstNewImageId = null;

                if ($request->hasFile('images')) {
                    // Kira jumlah gambar semasa selepas pemadaman
                    $currentImageCount = $item->images()->count(); // DB akan beri count terkini
                    $newImageCount = count($newImageFiles);

                    // Semak had maksimum 5 gambar
                    if (($currentImageCount + $newImageCount) > 5) {
                        // Lemparkan validation exception jika melebihi had
                        throw ValidationException::withMessages([
                            'images' => 'Jumlah gambar tidak boleh melebihi 5 (termasuk yang baru dimuat naik).',
                        ]);
                    }

                    // Proses setiap gambar baru
                    foreach ($newImageFiles as $index => $imageFile) {
                        if ($imageFile->isValid()) {
                            // Saiz semula
                            $image = Image::read($imageFile);
                            $image->scaleDown(width: 1024, height: 1024);
                            // Simpan
                            $extension = $imageFile->getClientOriginalExtension();
                            $filename = Str::uuid() . '.' . $extension;
                            $path = 'item-images/' . $filename;
                            Storage::disk('public')->put($path, (string) $image->encode());

                            // Cipta rekod ItemImage baru
                            $newImage = $item->images()->create([
                                'path' => $path,
                                'is_primary' => false, // Set false dahulu
                                'order' => $currentImageCount + $index + 1 // Contoh logik susunan
                            ]);
                            $newlyUploadedImageIds[] = $newImage->id;
                            if ($firstNewImageId === null) {
                                $firstNewImageId = $newImage->id; // Simpan ID gambar baru pertama
                            }
                        }
                    }
                }

                // D: Proses Penetapan Gambar Utama
                $newPrimarySet = false;
                if (!empty($newPrimaryImageId)) {
                    // Semak jika ID primary yang dipilih MASIH wujud (tidak dipadam)
                    // dan ia milik item ini ATAU ia baru dimuat naik
                    $allPossibleImageIds = $item->images()->pluck('id')->toArray(); // Ambil ID terkini selepas tambah/padam

                    if (in_array($newPrimaryImageId, $allPossibleImageIds)) {
                        // Reset semua gambar item ini kepada bukan primary
                        $item->images()->update(['is_primary' => false]);
                        // Tetapkan gambar yang dipilih sebagai primary
                        ItemImage::where('id', $newPrimaryImageId)->update(['is_primary' => true]);
                        $newPrimarySet = true;
                    }
                }

                // E: Jika tiada primary baru ditetapkan (atau yang lama dipadam),
                //    dan masih ada gambar, lantik satu sebagai primary.
                if (!$newPrimarySet) {
                     // Muatkan semula hubungan images untuk dapatkan data terkini selepas tambah/padam
                    $item->load('images');
                    $currentPrimary = $item->images()->where('is_primary', true)->first();

                    // Jika tiada primary langsung atau primary lama dipadam
                    if (!$currentPrimary && $item->images()->exists()) {
                        // Jadikan gambar pertama yang baru dimuat naik sebagai primary (jika ada)
                        if ($firstNewImageId) {
                             ItemImage::where('id', $firstNewImageId)->update(['is_primary' => true]);
                        } else {
                            // Atau jadikan gambar sedia ada yang pertama sebagai primary
                            $item->images()->first()?->update(['is_primary' => true]);
                        }
                    } elseif (!$currentPrimary && !$item->images()->exists()) {
                         // Tiada gambar langsung selepas delete/add, pastikan tiada primary
                         // (sudah direset di atas)
                    }
                }

            }); // Akhir DB Transaction

        } catch (ValidationException $e) {
             // Tangkap ralat validasi had jumlah gambar
             return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Tangkap ralat umum lain
            return back()->withInput()->with('error', 'Gagal mengemas kini item: ' . $e->getMessage());
        }

        // Redirect jika berjaya
        return redirect()->route('items.index')
                         ->with('success', 'Item inventori berjaya dikemas kini!');
    } // Akhir kaedah update()

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item): RedirectResponse // Terima objek $item
    {
        // Gantikan $this->authorize('delete', $item); dengan:
        if (! Gate::allows('delete', $item)) {
            abort(403);
        }
        // 1. Semak jika ada laluan gambar & padam fail dari storan
        if ($item->image_path) {
            // Gunakan Storage facade untuk memadam fail dari cakera 'public'
            Storage::disk('public')->delete($item->image_path);
        }

        // 2. Padam resit/manual PDF dari storan jika wujud <--- TAMBAH BLOK INI
        if ($item->receipt_path) {
            Storage::disk('public')->delete($item->receipt_path);
        }
        // --- AKHIR TAMBAHAN ---

        // 2. Laksanakan pemadaman rekod item dari pangkalan data
        $item->delete();

        // 3. Redirect kembali ke halaman senarai dengan mesej kejayaan
        return redirect()->route('items.index')
                         ->with('success', 'Item inventori (dan gambar jika ada) berjaya dipadam!'); // Ubah suai mesej sedikit
    }

    /**
     * Menjana dan mengembalikan imej Kod QR untuk item spesifik.
     */
    public function generateQrCode(Item $item) // Terima Item melalui Route Model Binding
    {
        // Data yang ingin kita masukkan dalam Kod QR
        // Pilihan terbaik biasanya ialah URL unik ke halaman item itu sendiri
        $urlToItem = route('items.show', $item->id);

        // Jana Kod QR sebagai data imej PNG, saiz 200x200 piksel
        // Tuan boleh ubah format (svg), saiz, tambah margin, logo, dll.
        $qrCodeImage = QrCode::format('png')->size(200)->generate($urlToItem);

        // Kembalikan respons HTTP yang mengandungi data imej PNG
        return response($qrCodeImage)->header('Content-Type', 'image/png');
    }
}
