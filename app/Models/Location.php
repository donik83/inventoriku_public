<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // Import class HasMany
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User; // Jika belum ada

class Location extends Model
{
    use HasFactory;


	/**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'owner_user_id']; // Hanya 'name' boleh diisi secara besar-besaran

    // Kaedah hubungan items() di sini...


    /**
     * Mendapatkan semua item yang dimiliki oleh lokasi ini.
     */
    public function items(): HasMany
    {
        // Satu Location 'hasMany' (mempunyai banyak) Item
        // Laravel akan mencari 'location_id' dalam jadual 'items'
        return $this->hasMany(Item::class);
    }

    public function incomingItemMovements(): HasMany
    {
        // Merujuk kepada foreign key 'to_location_id' dalam jadual item_movements
        return $this->hasMany(ItemMovement::class, 'to_location_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    // Properti $fillable akan ditambah di bawah nanti
}
