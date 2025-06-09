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
        Schema::create('item_movements', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap rekod pergerakan

            // Pautan ke item yang bergerak
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            // constrained() akan rujuk ke jadual 'items' lajur 'id'
            // onDelete('cascade') bermakna jika item dipadam, rekod pergerakannya juga dipadam

            // Pautan ke pengguna yang merekodkan pergerakan
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            // Boleh null jika pengguna dipadam, rekod pergerakan masih disimpan
            // onDelete('set null') akan set user_id kepada NULL jika pengguna dipadam

            // Jenis pergerakan
            $table->string('movement_type'); // Cth: PINJAM, GUNA, PINDAH, PULANG

            // Kuantiti yang terlibat (jika relevan, cth: GUNA)
            $table->integer('quantity_moved')->nullable();

            // Lokasi destinasi (jika relevan, cth: PINDAH, PULANG)
            $table->foreignId('to_location_id')->nullable()->constrained('locations')->onDelete('set null');

            // Nota tambahan
            $table->text('notes')->nullable();

            // Timestamp bila pergerakan direkodkan (created_at akan digunakan)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_movements');
    }
};
