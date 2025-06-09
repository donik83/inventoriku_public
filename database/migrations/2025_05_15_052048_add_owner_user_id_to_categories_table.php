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
        Schema::table('categories', function (Blueprint $table) {
        $table->foreignId('owner_user_id')
              ->nullable() // Benarkan null untuk yang sedia ada / dicipta oleh sistem
              ->after('name') // Atau lajur terakhir lain
              ->constrained('users')
              ->onDelete('set null'); // Jika user dipadam, pemilik jadi null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
        $table->dropForeign(['owner_user_id']);
        $table->dropColumn('owner_user_id');
    });
    }
};
