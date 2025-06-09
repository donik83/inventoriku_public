<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission; // Guna model Permission dari Spatie

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Dapatkan semua permission, susun ikut nama
        $permissions = Permission::orderBy('name')->paginate(20); // Paginate jika banyak
        return view('admin.permissions.index', compact('permissions'));
    }
}
