<?php

namespace App\Providers;

//use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator; // <-- TAMBAH BARIS INI
use App\Models\Item;      // <-- TAMBAH/PASTIKAN ADA
use App\Policies\ItemPolicy; // <-- TAMBAH/PASTIKAN ADA
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Category;
use App\Policies\CategoryPolicy;
use App\Models\Location;
use App\Policies\LocationPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     *
     */
     /*@var array<class-string, class-string>*/

     protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy', // Mungkin ada contoh ini
        Item::class => ItemPolicy::class, // <-- TAMBAH BARIS INI
        Category::class => CategoryPolicy::class, // <-- TAMBAH
        Location::class => LocationPolicy::class, // <-- TAMBAH
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Beritahu Laravel untuk guna view pagination Bootstrap 5
        Paginator::useBootstrapFive(); // <-- TAMBAH BARIS INI

        // Mungkin ada kod lain di sini nanti
    }
}
