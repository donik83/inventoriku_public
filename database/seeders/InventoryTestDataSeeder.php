<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // Import DB facade
use App\Models\Category;
use App\Models\Location;
use App\Models\Item;
use App\Models\ItemMovement; // Import ItemMovement untuk truncate

class InventoryTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ----- Langkah 2: Truncate Jadual -----
        // Matikan semakan foreign key sementara untuk truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Kosongkan jadual dalam susunan yang betul (bergantung dahulu)
        ItemMovement::truncate();
        Item::truncate();
        Category::truncate();
        Location::truncate();
        // Jangan truncate 'users' jika Tuan mahu kekalkan pengguna sedia ada

        // Hidupkan semula semakan foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Jadual inventori berjaya dikosongkan.');

        // ----- Langkah 3: Masukkan Kategori -----
        $categories = [
            'Alatan Kebun', 'Alatan Pertukangan', 'Alatan Elektrik (Power Tools)', 'Alatan Paip',
            'Alatan Mengecat', 'Perkakasan Kecil', 'Tangga', 'Perkakas Dapur Kecil',
            'Perkakas Dapur Besar', 'Pencucian & Pembersihan', 'Penyejukan & Pengudaraan',
            'Alat Memasak', 'Pinggan Mangkuk Harian', 'Pinggan Mangkuk Kenduri/Khas',
            'Bekas Penyimpanan Makanan', 'Khemah / Kanopi', 'Kerusi Plastik / Lipat',
            'Meja Lipat / Panjang', 'Peralatan BBQ', 'Lampu / Wayar Sambungan',
            'Pembesar Suara / PA Mudah Alih', 'Perabot Ruang Tamu', 'Perabot Bilik Tidur',
            'Perabot Ruang Makan', 'Perabot Luar / Taman', 'Televisyen & Audio Visual',
            'Komputer & Aksesori', 'Gajet & Aksesori Lain', 'Stok Bahan Pencuci',
            'Stok Mentol / Bateri', 'Stok Baja / Racun', 'Linen Katil / Selimut', 'Tuala',
            'Alas Meja / Kain Hiasan', 'Peralatan Sukan / Rekreasi', 'Mainan Kanak-kanak',
            'Buku / Majalah', 'Hiasan Rumah', 'Lain-lain',
        ];

        foreach ($categories as $categoryName) {
            Category::create(['name' => $categoryName]);
        }
        $this->command->info(count($categories) . ' Kategori berjaya dicipta.');

        // ----- Langkah 4: Masukkan Lokasi -----
        $locations = [
            // Rumah
            'Rumah Bapa', 'Rumah Kakak Anis', 'Rumah Abang Ali', 'Rumah Abang Bob', 'Rumah Saya (Donny)',
            // Kawasan Umum (Contoh)
            'Rumah Bapa - Stor Utama', 'Rumah Bapa - Garaj', 'Rumah Saya - Pejabat',
            'Rumah Saya - Dapur', 'Rumah Kakak Anis - Bawah Tangga', 'Rumah Abang Ali - Bangsal Luar',
            // Lokasi Spesifik (Contoh)
            'Rumah Saya - Pejabat - Rak Fail', 'Rumah Saya - Pejabat - Folder Waranti',
            'Rumah Bapa - Garaj - Kotak Alatan Paip', 'Rumah Kakak Anis - Dapur - Kabinet Atas',
            // Lokasi Status (Pilihan jika mahu)
            // 'Dipinjamkan Keluar', 'Dalam Servis/Baiki'
        ];
         foreach ($locations as $locationName) {
            Location::create(['name' => $locationName]);
        }
        $this->command->info(count($locations) . ' Lokasi berjaya dicipta.');


        // ----- Langkah 5, 6, 7, 8: Cipta & Masukkan Item Palsu -----
        $this->command->info('Menjana data item palsu...');

        // Dapatkan semua ID kategori dan lokasi yang baru dicipta
        $categoryIds = Category::pluck('id');
        $locationIds = Location::pluck('id');

        if ($categoryIds->isEmpty() || $locationIds->isEmpty()) {
             $this->command->error('Tiada Kategori atau Lokasi ditemui. Sila pastikan ia dicipta dahulu.');
             return;
        }

        $totalItemsCreated = 0;
        // Loop setiap kategori
        foreach ($categoryIds as $catId) {
            // Cipta 2 hingga 7 item palsu untuk setiap kategori
            $numberOfItems = rand(2, 7);
            Item::factory()->count($numberOfItems)->create([
                'category_id' => $catId, // Tetapkan kategori semasa
                'location_id' => $locationIds->random(), // Pilih lokasi secara rawak
                // Factory akan mengisi medan lain secara automatik
                // berdasarkan definisi dalam ItemFactory.php
            ]);
            $totalItemsCreated += $numberOfItems;
        }

         $this->command->info($totalItemsCreated . ' Item palsu berjaya dicipta.');
    }
}
