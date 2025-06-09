<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserFeedback extends Model
{
    use HasFactory;

    protected $table = 'user_feedback'; // Nama jadual

    protected $fillable = [
        'user_id',
        'rating',
        'subject',
        'message',
        'admin_reply',
        'replied_at',
        'replied_by_user_id',
    ];

    /**
     * Dapatkan pengguna yang memberi maklum balas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            //'is_primary' => 'boolean', // Dari ItemImage? Ini mungkin kesilapan salin tampal? Abaikan jika tiada.
            'replied_at' => 'datetime', // <-- TAMBAH INI
        ];
    }

    /**
    * Dapatkan admin yang membalas maklum balas ini.
    */
    public function repliedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by_user_id');
    }

    /**
     * Simpan balasan Admin untuk maklum balas spesifik.
     */
    public function storeReply(Request $request, UserFeedback $feedback): RedirectResponse
    {
        // Validasi input balasan
        $validated = $request->validate([
            'admin_reply' => 'required|string|max:5000', // Balasan wajib
        ]);

        // Kemas kini rekod maklum balas dengan balasan
        $feedback->admin_reply = $validated['admin_reply'];
        $feedback->replied_by_user_id = Auth::id(); // ID Admin yang log masuk
        $feedback->replied_at = now(); // Timestamp semasa
        $feedback->save();

        // Redirect kembali ke senarai maklum balas Admin dengan mesej kejayaan
        return redirect()->route('admin.feedback.index')
                        ->with('success', 'Balasan untuk maklum balas #' . $feedback->id . ' berjaya disimpan.');
    }
}
