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
        Schema::table('user_feedback', function (Blueprint $table) {
            $table->text('admin_reply')->nullable()->after('message'); // Kandungan balasan admin
            $table->timestamp('replied_at')->nullable()->after('admin_reply'); // Masa dibalas
            // Foreign key ke user admin yang membalas
            $table->foreignId('replied_by_user_id')
                  ->nullable()
                  ->after('replied_at')
                  ->constrained('users') // Rujuk ke jadual users
                  ->onDelete('set null'); // Jika admin dipadam, set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_feedback', function (Blueprint $table) {
            $table->dropForeign(['replied_by_user_id']);
            $table->dropColumn(['admin_reply', 'replied_at', 'replied_by_user_id']);
        });
    }
};
