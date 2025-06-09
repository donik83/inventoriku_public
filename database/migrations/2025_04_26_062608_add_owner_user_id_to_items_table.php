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
            // Tambah lajur foreign key untuk pemilik
            $table->foreignId('owner_user_id')
                  ->nullable() // Benarkan null (mungkin untuk item lama atau sistem)
                  ->after('location_id') // Letak selepas user_id jika ada, atau 'id', atau 'location_id' - sesuaikan
                  ->constrained('users') // Menetapkan foreign key ke jadual 'users' lajur 'id'
                  ->onUpdate('cascade') // Jika ID user berubah, kemas kini di sini
                  ->onDelete('set null'); // Jika user pemilik dipadam, set owner_id ke NULL (item tidak terpadam)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Perlu buang foreign key constraint dahulu sebelum buang lajur
            $table->dropForeign(['owner_user_id']);
            $table->dropColumn('owner_user_id');
        });
    }
};
