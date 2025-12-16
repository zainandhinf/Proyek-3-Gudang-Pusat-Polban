<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailBarangUsang extends Model
{
    use HasFactory;
    protected $guarded = [];

    public $timestamps = false; // Tidak ada timestamps

    public function mutasi(): BelongsTo
    {
        return $this->belongsTo(BarangUsang::class, 'mutasi_barang_id');
    }

    public function barangUsang(): BelongsTo
    {
        return $this->belongsTo(BarangUsang::class, 'barang_usang_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
