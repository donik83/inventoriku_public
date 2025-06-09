<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import class BelongsTo
use Illuminate\Database\Eloquent\Relations\HasMany; // Tambah use statement
use Illuminate\Database\Eloquent\Relations\HasOne;  // Tambah
use Illuminate\Support\Facades\Storage;
use App\Models\ItemImage;
use Kyslik\ColumnSortable\Sortable; // <-- TAMBAH INI

class Item extends Model
{
	use HasFactory, Sortable; // <-- TAMBAH Sortable DI SINI

	/**
     * Atribut yang boleh diisi secara besar-besaran.
     */

    // Definisikan lajur yang boleh disusun oleh pakej ini
    public $sortable = [
        'id',
        'name',
        'purchase_date',
        'purchase_price',
        'quantity',
        'serial_number', // Contoh jika mahu
        'barcode_data',  // Contoh jika mahu
        'warranty_expiry_date',
        'status',
        'created_at',
        'updated_at'
        // Nota: Menyusun ikut Kategori/Lokasi (hubungan) memerlukan konfigurasi tambahan,
        // kita fokus pada lajur dalam jadual 'items' dahulu.
    ];

     // Properti $fillable akan ditambah di bawah nanti
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'location_id',
        'purchase_date',
        'purchase_price',
        'quantity',
        'serial_number',
        'barcode_data', // <-- TAMBAH INI
        'warranty_expiry_date',
        //'image_path', // Mungkin mahu urus secara berasingan nanti
        'receipt_path', // <-- TAMBAH INI
        'status',
        'owner_user_id',
        'is_private',   // <-- TAMBAH INI
    ];

    // Kaedah hubungan category() dan location() di sini...
	/**
     * Mendapatkan kategori yang dimiliki oleh item ini.
     */
    public function category(): BelongsTo
    {
        // Satu Item 'belongsTo' (milik) satu Category
        // Laravel akan secara automatik mencari foreign key 'category_id'
        return $this->belongsTo(Category::class);
    }

    /**
     * Mendapatkan lokasi yang dimiliki oleh item ini.
     */
    public function location(): BelongsTo
    {
        // Satu Item 'belongsTo' (milik) satu Location
        // Laravel akan secara automatik mencari foreign key 'location_id'
        return $this->belongsTo(Location::class);
    }

    public function itemMovements(): HasMany
    {
        return $this->hasMany(ItemMovement::class);
    }

    /**
     * Dapatkan semua gambar untuk item ini.
     */
    public function images(): HasMany
    {
        // Satu Item mempunyai banyak ItemImage
        return $this->hasMany(ItemImage::class)->orderBy('order', 'asc')->orderBy('id', 'asc'); // Susun ikut order, kemudian ID
    }

    /**
     * Dapatkan gambar utama (primary) untuk item ini.
     * Berguna untuk paparan thumbnail.
     */
    public function primaryImage(): HasOne
    {
        // Satu Item mempunyai satu ItemImage yang primary
        // Jika tiada yang primary, ia akan pulangkan null ATAU yang pertama ditambah
        return $this->hasOne(ItemImage::class)->where('is_primary', true)
                   ->withDefault(function (ItemImage $image, Item $item) {
                        // Jika tiada primary image, cuba dapatkan gambar pertama
                        $firstImage = $item->images()->first();
                        if ($firstImage) {
                            $image->path = $firstImage->path;
                        } else {
                            // Letak laluan ke imej placeholder lalai jika tiada langsung gambar
                            $image->path = 'placeholders/no-image.png'; // Contoh
                        }
                   });
    }

    /**
     * The "booted" method of the model.
     * Digunakan untuk mendaftar Model Events.
     */
    protected static function booted(): void // Guna booted() adalah cara moden > boot()
    {
        // parent::boot(); // Tidak perlu panggil parent::boot() untuk booted()

        // Daftar event listener untuk 'deleting' event
        static::deleting(function (Item $item) { // Terima model Item yang sedang dipadam
            // Loop melalui setiap gambar yang berkaitan dengan item ini
            $item->images()->each(function ($image) { // Guna type hint jika mahu: function (ItemImage $image)
                // Padam fail fizikal dari storan 'public'
                Storage::disk('public')->delete($image->path);
            });
            // Nota: Rekod dalam jadual 'item_images' akan dipadam secara automatik
            // oleh 'onDelete('cascade')' yang kita set dalam migrasi.
            // Jadi kita hanya perlu padam fail fizikal di sini.
        });
    }

    /**
     * The attributes that should be cast.
     */

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'warranty_expiry_date' => 'date',
            'purchase_price' => 'decimal:2',
            'is_private' => 'boolean', // <-- TAMBAH INI
        ];
    }

}
