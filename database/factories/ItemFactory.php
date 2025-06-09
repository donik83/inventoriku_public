<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category; // Import jika belum
use App\Models\Location; // Import jika belum

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Senarai status yang mungkin
        $statuses = ['Digunakan', 'Disimpan', 'Rosak', 'Dipinjam', 'Hilang', 'Dilupuskan', 'Dihantar Servis'];

        // 1. Jana objek tarikh Carbon yang mungkin null dahulu
        $warrantyDateObject = fake()->optional(0.7)->dateTimeBetween('now', '+3 years');

        return [
            'name' => fake()->words(3, true), // Nama item
            'description' => fake()->sentence(), // Deskripsi ringkas
            // ID Kategori & Lokasi biasanya akan ditetapkan dalam Seeder nanti
            'category_id' => Category::factory(), // Atau null jika dibenarkan & akan ditetapkan di Seeder
            'location_id' => Location::factory(), // Atau null jika dibenarkan & akan ditetapkan di Seeder
            'purchase_date' => fake()->dateTimeBetween('-5 years', 'now')->format('Y-m-d'), // Tarikh beli dalam 5 tahun lepas
            'purchase_price' => fake()->randomFloat(2, 5, 2000), // Harga antara 5.00 - 2000.00
            'quantity' => fake()->numberBetween(1, 50), // Kuantiti antara 1 - 50
            'serial_number' => fake()->optional()->ean13(), // No siri (kadang ada, kadang tiada)
            // 2. Format HANYA jika objek tarikh wujud, jika tidak, gunakan null
            'warranty_expiry_date' => $warrantyDateObject ? $warrantyDateObject->format('Y-m-d') : null,
            'status' => fake()->randomElement($statuses), // Pilih status secara rawak
            // image_path & receipt_path dibiarkan kosong (NULL)
        ];
    }
}
