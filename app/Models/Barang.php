<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi ke Master
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }
    
    // Relasi ke Transaksi
    public function detailBarangMasuks(): HasMany
    {
        return $this->hasMany(DetailBarangMasuk::class);
    }

    public function detailPermintaans(): HasMany
    {
        return $this->hasMany(DetailPermintaan::class);
    }

    public function detailStockOpnames(): HasMany
    {
        return $this->hasMany(DetailStockOpname::class);
    }

    public function barangUsangs(): HasMany
    {
        return $this->hasMany(BarangUsang::class);
    }
}
