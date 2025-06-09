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
        Schema::table('items', function (Blueprint $table) {
            // Tambah lajur baru 'barcode_data' selepas 'serial_number'
            // Boleh null, jenis string (sesuaikan panjang jika perlu), tambah index
            $table->string('barcode_data')->nullable()->after('serial_number')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Semak jika lajur wujud sebelum cuba buang (amalan baik)
            if (Schema::hasColumn('items', 'barcode_data')) {
                $table->dropColumn('barcode_data');
            }
        });
    }
};
