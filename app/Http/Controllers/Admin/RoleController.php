<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role; // Guna model Role dari Spatie
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse; // Jika belum ada
use Illuminate\Validation\Rule; // <-- Penting untuk validasi unik semasa update

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Dapatkan semua role, susun ikut nama
        $roles = Role::orderBy('name')->paginate(15); // Guna paginate jika mahu
        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Dapatkan semua permission, susun ikut nama, untuk pilihan checkbox
        $permissions = Permission::orderBy('name')->get();
        return view('admin.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $validated = $request->validate([
            // Pastikan nama unik dalam jadual roles (guard web adalah lalai)
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'], // permissions boleh null/kosong atau mesti array
            'permissions.*' => ['string', 'exists:permissions,name'] // Setiap permission mesti wujud dalam jadual permissions
        ]);

        // 2. Cipta Role Baru
        $role = Role::create(['name' => $validated['name']]); // Guard name akan ditetapkan secara automatik (web)

        // 3. Tetapkan Permissions kepada Role (jika ada dipilih)
        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']); // SyncPermissions cara terbaik
        }

        // 4. Redirect ke halaman senarai dengan mesej kejayaan
        return redirect()->route('admin.roles.index')
                         ->with('success', 'Peranan (Role) baru "' . $role->name . '" berjaya ditambah.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View // Guna Route Model Binding untuk dapatkan $role
    {
        // Dapatkan semua permission untuk pilihan checkbox
        $permissions = Permission::orderBy('name')->get();

        // Dapatkan nama permission yang dimiliki oleh role ini sahaja
        // Kita gunakan pluck('name') untuk dapatkan Collection nama permission
        $rolePermissions = $role->permissions->pluck('name');

        // Hantar data role, semua permission, dan permission milik role ke view
        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // 1. Sekat pengeditan nama untuk role asas 'Admin' dan 'User'
        // Kita benarkan kemas kini permission untuk Admin/User jika perlu, tapi nama kekal.
        if (in_array($role->name, ['Admin', 'User'])) {
            // Jika role asas, hanya validasi permission
             $validated = $request->validate([
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['string', 'exists:permissions,name']
             ]);
             // Terus sync permission tanpa ubah nama
             $role->syncPermissions($request->input('permissions', []));

             return redirect()->route('admin.roles.index')
                              ->with('success', 'Kebenaran untuk peranan "' . $role->name . '" berjaya dikemas kini.');

        } else {
            // Jika bukan role asas, benarkan kemas kini nama & permission
            // 2. Validasi Input (termasuk nama unik, abaikan ID semasa)
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('roles', 'name')->ignore($role->id) // Nama mesti unik, abaikan role ini sendiri
                ],
                'permissions' => ['nullable', 'array'],
                'permissions.*' => ['string', 'exists:permissions,name']
            ]);

            // 3. Kemas kini Nama Role
            $role->name = $validated['name'];
            $role->save(); // Simpan perubahan nama

            // 4. Selaraskan (Sync) Permissions
            $role->syncPermissions($request->input('permissions', []));

            // 5. Redirect kembali dengan mesej kejayaan
            return redirect()->route('admin.roles.index')
                             ->with('success', 'Peranan "' . $role->name . '" berjaya dikemas kini.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role): RedirectResponse
    {
        // 1. Sekat pemadaman role asas
        if (in_array($role->name, ['Admin', 'User'])) {
            return redirect()->route('admin.roles.index')
                            ->with('error', 'Peranan asas \'Admin\' dan \'User\' tidak boleh dipadam.');
        }

        // 2. Semak jika role masih ada pengguna
        // Kaedah users() datang dari hubungan yang disediakan oleh Spatie
        if ($role->users()->count() > 0) {
            return redirect()->route('admin.roles.index')
                            ->with('error', 'Peranan "' . $role->name . '" tidak boleh dipadam kerana masih ada pengguna yang mempunyai peranan ini.');
        }

        // 3. Jika selamat, padam role
        $roleName = $role->name; // Simpan nama untuk mesej
        $role->delete(); // Spatie akan uruskan pemadaman hubungan permission

        // 4. Redirect dengan mesej kejayaan
        return redirect()->route('admin.roles.index')
                        ->with('success', 'Peranan "' . $roleName . '" berjaya dipadam.');
    }
}
