<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import

class ItemImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'path',
        'is_primary',
        'order',
    ];

    /**
     * Tetapkan atribut yang patut di-cast.
     */
    protected function casts(): array // Guna sintaks PHP 8.1+ casts
    {
        return [
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Dapatkan item pemilik gambar ini.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
