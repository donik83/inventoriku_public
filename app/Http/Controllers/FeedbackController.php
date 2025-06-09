<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\UserFeedback;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    /**
     * Paparkan senarai semua maklum balas (untuk semua pengguna).
     */
    public function index(): View
    {
        // Dapatkan semua feedback, susun ikut terbaru, muatkan nama pengguna
        $feedbacks = UserFeedback::with(['user', 'repliedByAdmin']) // <-- Tambah 'repliedByAdmin'
                         ->latest()
                         ->paginate(15);

        return view('feedback.index', compact('feedbacks'));
    }

    /**
     * Paparkan borang maklum balas.
     */
    public function create(): View
    {
        return view('feedback.create');
    }

    /**
     * Simpan maklum balas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'rating' => ['nullable', 'integer', 'min:1', 'max:5'], // Rating 1-5, boleh kosong
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'], // Mesej wajib
        ]);

        // Simpan maklum balas ke DB, kaitkan dengan pengguna log masuk
        UserFeedback::create([
            'user_id' => Auth::id(),
            'rating' => $validated['rating'] ?? null, // Guna null jika tiada
            'subject' => $validated['subject'] ?? null,
            'message' => $validated['message'],
        ]);

        // Redirect kembali ke Dashboard (atau halaman lain) dengan mesej kejayaan
        return redirect()->route('dashboard')
                         ->with('success', 'Terima kasih! Maklum balas anda telah berjaya dihantar.');
    }
}
