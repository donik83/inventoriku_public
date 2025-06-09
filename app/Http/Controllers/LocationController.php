<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\View\View;
// Mungkin juga perlukan Request, RedirectResponse nanti
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
     
     
    public function index()
    {
        $userId = Auth::id();
        $locations = \App\Models\Location::orderByRaw("CASE WHEN owner_user_id = ? THEN 0 ELSE 1 END, name ASC", [$userId])->paginate(10); // Atau ->get() jika senarai pendek
        return view('locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('locations.create'); // Hanya paparkan borang kosong
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:locations,name' // Wajib, unik dalam jadual locations
        ]);

        $validatedData['owner_user_id'] = Auth::id();
        Location::create($validatedData);

        return redirect()->route('locations.index')
                        ->with('success', 'Lokasi baru berjaya ditambah!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location): View // Guna Route Model Binding
    {
        return view('locations.edit', compact('location'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location): RedirectResponse
    {
        $validatedData = $request->validate([
            // Pastikan unik, kecuali untuk ID lokasi ini sendiri
            'name' => 'required|string|max:255|unique:locations,name,' . $location->id
        ]);

        $location->update($validatedData);

        return redirect()->route('locations.index')
                        ->with('success', 'Lokasi berjaya dikemas kini!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location): RedirectResponse
    {
        // SEMAKAN PENTING: Jangan padam jika masih ada item menggunakan lokasi ini
        if ($location->items()->count() > 0) {
            return redirect()->route('locations.index')
                            ->with('error', 'Ralat: Lokasi "' . $location->name . '" tidak boleh dipadam kerana masih digunakan oleh item.');
        }

        // Jika tiada item berkaitan, teruskan pemadaman
        $location->delete();

        return redirect()->route('locations.index')
                        ->with('success', 'Lokasi berjaya dipadam!');
    }
}
