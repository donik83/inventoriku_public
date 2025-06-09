<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;
// Mungkin juga perlukan Request, RedirectResponse nanti
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CategoryController extends Controller
{
    use AuthorizesRequests; // <-- TAMBAH ATAU PASTIKAN BARIS INI ADA
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $userId = Auth::id(); 
        
        $this->authorize('viewAny', Category::class);
        $categories = \App\Models\Category::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->paginate(10); // Atau ->get() jika senarai pendek

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);
        return view('categories.create'); // Hanya paparkan borang kosong
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name' // Wajib, unik dalam jadual categories
        ]);

        $validatedData['owner_user_id'] = Auth::id();
        Category::create($validatedData);

        return redirect()->route('categories.index')
                        ->with('success', 'Kategori baru berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View // Guna Route Model Binding
    {
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $this->authorize('update', $category);
        $validatedData = $request->validate([
            // Pastikan unik, kecuali untuk ID kategori ini sendiri
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id
        ]);

        $category->update($validatedData);

        return redirect()->route('categories.index')
                        ->with('success', 'Kategori berjaya dikemas kini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);
        // SEMAKAN PENTING: Jangan padam jika masih ada item menggunakan kategori ini
        if ($category->items()->count() > 0) {
            return redirect()->route('categories.index')
                            ->with('error', 'Ralat: Kategori "' . $category->name . '" tidak boleh dipadam kerana masih digunakan oleh item.');
        }

        // Jika tiada item berkaitan, teruskan pemadaman
        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Kategori berjaya dipadam!');
    }
}
