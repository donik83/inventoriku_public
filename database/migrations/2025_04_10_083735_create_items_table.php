<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Cipta jadual 'items'
        Schema::create('items', function (Blueprint $table) {
            $table->id(); // Lajur ID auto-increment primary key
            $table->string('name'); // Lajur nama (VARCHAR)
            $table->text('description')->nullable(); // Lajur deskripsi (TEXT), boleh kosong
            $table->unsignedBigInteger('category_id')->nullable(); // ID untuk kategori, boleh kosong
            $table->unsignedBigInteger('location_id')->nullable(); // ID untuk lokasi, boleh kosong
            $table->date('purchase_date')->nullable(); // Tarikh beli, boleh kosong
            $table->decimal('purchase_price', 10, 2)->nullable(); // Harga beli (10 digit total, 2 selepas perpuluhan), boleh kosong
            $table->unsignedInteger('quantity')->default(1); // Kuantiti, lalai 1
            $table->string('serial_number')->nullable(); // Nombor siri, boleh kosong
            $table->date('warranty_expiry_date')->nullable(); // Tarikh luput jaminan, boleh kosong
            $table->string('image_path')->nullable(); // Laluan gambar, boleh kosong
            $table->string('status')->default('Digunakan'); // Status, lalai 'Digunakan'
            $table->timestamps(); // Cipta lajur created_at dan updated_at

            // Cipta index untuk foreign key (baik untuk prestasi carian)
            $table->index('category_id');
            $table->index('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Padam jadual 'items' jika migrasi diundur
        Schema::dropIfExists('items');
    }
};
