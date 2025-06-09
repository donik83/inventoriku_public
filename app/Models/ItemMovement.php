<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemMovement extends Model
{
    use HasFactory;

    /**
     * Atribut yang boleh diisi secara besar-besaran.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'item_id',
        'user_id',
        'movement_type',
        'quantity_moved',
        'to_location_id',
        'notes',
        // created_at & updated_at diuruskan secara automatik
    ];

    /**
     * Dapatkan item yang berkaitan dengan pergerakan ini.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Dapatkan pengguna yang merekodkan pergerakan ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Dapatkan lokasi destinasi (jika ada) untuk pergerakan ini.
     */
    public function destinationLocation(): BelongsTo
    {
        // Kita namakan 'destinationLocation' untuk elak konflik jika ada 'from_location'
        // Foreign key ialah 'to_location_id'
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
