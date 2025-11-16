<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBarangMasuk extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false; // Tidak ada timestamps

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
