<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location; // Import Model Location

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
	// Contoh data lokasi
        Location::create(['name' => 'Ruang Tamu']);
        Location::create(['name' => 'Bilik Tidur Utama']);
        Location::create(['name' => 'Bilik Tidur Anak']);
        Location::create(['name' => 'Dapur']);
        Location::create(['name' => 'Bilik Stor']);
        Location::create(['name' => 'Pejabat Rumah']);
        Location::create(['name' => 'Garaj']);
        // Tambah lagi jika perlu...
    }
}
