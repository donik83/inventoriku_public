<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserFeedback;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    /**
     * Paparkan senarai semua maklum balas pengguna.
     */
    public function index(): View
    {
        // Dapatkan semua feedback, susun ikut terbaru, muatkan nama pengguna sekali
        $feedbacks = UserFeedback::with(['user', 'repliedByAdmin']) // <-- Tambah 'repliedByAdmin'
                         ->latest()
                         ->paginate(15);

        return view('admin.feedback.index', compact('feedbacks'));
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

    /**
     * Remove the specified feedback from storage.
     */
    public function destroy(UserFeedback $feedback): RedirectResponse
    {
        // Pastikan Admin sahaja boleh padam (ini sudah diurus oleh middleware 'role:Admin' pada group route)
        // Jika Tuan mahu semakan tambahan di sini, Tuan boleh lakukannya.

        $feedbackId = $feedback->id; // Simpan ID untuk mesej
        $feedback->delete(); // Padam rekod maklum balas

        return redirect()->route('admin.feedback.index')
                        ->with('success', 'Maklum balas #' . $feedbackId . ' berjaya dipadam.');
    }

}
