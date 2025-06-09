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
        Schema::create('item_images', function (Blueprint $table) {
            $table->id(); // Primary Key

            // Foreign key ke jadual 'items'
            // Jika item dipadam, rekod gambar berkaitan juga akan dipadam (cascade)
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');

            // Laluan ke fail gambar dalam storan (cth: item-images/xxxxx.jpg)
            $table->string('path');

            // Bendera (flag) untuk menandakan gambar utama/thumbnail (pilihan)
            $table->boolean('is_primary')->default(false);

            // Untuk susunan gambar jika perlu (pilihan)
            $table->unsignedInteger('order')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_images');
    }
};
