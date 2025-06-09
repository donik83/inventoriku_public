<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Anggap Controller asas Tuan
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule; // <-- Penting untuk validasi unik semasa update
use Illuminate\Validation\Rules; // <-- TAMBAH BARIS INI
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered; // Jika Tuan guna event()
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Verified; // Import event Verified

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Dapatkan semua pengguna dengan peranannya (eager load), susun & paginate
        $users = User::with('roles')->orderBy('name')->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Dapatkan semua roles yang ada untuk dipaparkan dalam borang
        $roles = Role::orderBy('name')->get();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'], // Roles boleh null atau mesti array
            'roles.*' => ['string', 'exists:roles,name'] // Setiap role mesti wujud dalam jadual roles
        ]);

        // 2. Cipta Pengguna Baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            // 'email_verified_at' => now(), // (Pilihan) Terus sahkan emel jika dicipta oleh Admin?
        ]);

        // 3. Tetapkan Peranan (Assign Roles)
        if ($request->filled('roles')) {
            $user->assignRole($request->input('roles'));
        }

        // 4. (Pilihan) Cetuskan event untuk hantar emel verifikasi?
        // event(new Registered($user));
        // Atau abaikan jika pengguna dicipta admin dianggap verified

        // 5. Redirect ke halaman senarai dengan mesej kejayaan
        return redirect()->route('admin.users.index')
                        ->with('success', 'Pengguna baru berjaya ditambah.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        // Dapatkan semua role untuk pilihan checkbox/select
        $roles = Role::orderBy('name')->get();

        // Dapatkan nama role yang dimiliki oleh pengguna ini sahaja
        // getRoleNames() memulangkan satu Collection nama role
        $userRoles = $user->getRoleNames();

        // Hantar data pengguna, semua role, dan role pengguna ke view
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        /// 1. Validasi Input
        // Serupa dengan store(), tetapi ada sedikit perbezaan:
        // - Validasi unik untuk emel perlu mengabaikan ID pengguna semasa.
        // - Kata laluan kini adalah pilihan (nullable).
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                // Pastikan emel unik, TAPI abaikan rekod milik pengguna ini sendiri
                Rule::unique('users')->ignore($user->id)
            ],
            // Hanya validasi password jika ia diisi (nullable)
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => ['nullable', 'array'], // Boleh jadi array kosong
            'roles.*' => ['string', 'exists:roles,name'] // Sahkan nama role wujud
        ]);

        // 2. Sediakan Data Asas untuk Dikemaskini
        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            // JANGAN masukkan password di sini dahulu
        ];

        // 3. Semak Jika Ada Kata Laluan Baru Dimasukkan
        if (!empty($validatedData['password'])) {
            // Jika ada, baru hash dan masukkan ke data kemas kini
            $updateData['password'] = Hash::make($validatedData['password']);
        }
        // Jika medan password dibiarkan kosong, ia tidak akan dimasukkan ke $updateData,
        // jadi password lama tidak akan ditimpa.

        // 4. Kemas Kini Data Asas Pengguna dalam Pangkalan Data
        $user->update($updateData);

        // 5. Selaraskan (Sync) Peranan Pengguna
        // Kaedah syncRoles() akan secara automatik:
        // - Membuang peranan lama yang tiada dalam array baru.
        // - Menambah peranan baru yang ada dalam array.
        // - Mengekalkan peranan yang sama.
        // Jika $request->input('roles') kosong atau tiada, semua role akan dibuang.
        $user->syncRoles($request->input('roles', []));

        // 6. Redirect Kembali ke Senarai Pengguna dengan Mesej Kejayaan
        return redirect()->route('admin.users.index')
                        ->with('success', 'Data pengguna (' . $user->name . ') berjaya dikemas kini.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        /// Penting: Jangan benarkan pengguna padam akaun sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                            ->with('error', 'Anda tidak boleh memadam akaun anda sendiri!');
        }

        // Jika bukan akaun sendiri, teruskan dengan pemadaman
        // Nota: Hubungan Spatie (roles/permissions) sepatutnya dipadam secara automatik dari pivot tables
        $userName = $user->name; // Simpan nama untuk mesej
        $user->delete();

        return redirect()->route('admin.users.index')
                        ->with('success', 'Pengguna (' . $userName . ') berjaya dipadam.');
    }

    /**
     * Mark the user's email as verified manually by an Admin.
     */
    public function markAsVerified(User $user): RedirectResponse
    {
        // Semak jika pengguna belum disahkan lagi
        if (!$user->hasVerifiedEmail()) {
            // Guna kaedah helper dari trait MustVerifyEmail
            $user->markEmailAsVerified();

            // (Pilihan) Cetuskan event 'Verified' supaya listener lain (jika ada) boleh bertindak
            event(new Verified($user));

            return redirect()->route('admin.users.edit', $user)
                            ->with('success', 'Emel untuk pengguna ' . $user->name . ' berjaya disahkan secara manual.');
        }

        // Jika sudah disahkan sebelum ini
        return redirect()->route('admin.users.edit', $user)
                        ->with('info', 'Emel pengguna ini sudah pun disahkan sebelum ini.');
    }
}
