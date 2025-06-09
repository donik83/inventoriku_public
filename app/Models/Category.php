<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import class HasMany
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; // Jika belum ada

class Category extends Model
{
    use HasFactory;

	/**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'owner_user_id'];

    // Kaedah hubungan items() di sini...

    /**
     * Mendapatkan semua item yang dimiliki oleh kategori ini.
     */
    public function items(): HasMany
    {
        // Satu Category 'hasMany' (mempunyai banyak) Item
        // Laravel akan mencari 'category_id' dalam jadual 'items'
        return $this->hasMany(Item::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    // Properti $fillable akan ditambah di bawah nanti
}
