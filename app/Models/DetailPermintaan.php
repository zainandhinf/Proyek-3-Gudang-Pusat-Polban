<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPermintaan extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false; // Tidak ada timestamps

    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
