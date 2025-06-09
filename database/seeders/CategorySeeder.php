<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category; // Import Model Category

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Contoh data kategori
        Category::create(['name' => 'Elektronik']);
        Category::create(['name' => 'Perabot']);
        Category::create(['name' => 'Alatan Tangan']);
        Category::create(['name' => 'Perkakasan Dapur']);
        Category::create(['name' => 'Dokumen Penting']);
        Category::create(['name' => 'Pakaian']);
        Category::create(['name' => 'Hiasan']);
        // Tambah lagi jika perlu...
    }
}
