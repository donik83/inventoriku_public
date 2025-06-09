<?php

return [
    App\Providers\AppServiceProvider::class,
    // Mungkin ada provider lain di sini...
    Intervention\Image\Laravel\ServiceProvider::class, // <-- PASTIKAN BARIS INI ADA
];
