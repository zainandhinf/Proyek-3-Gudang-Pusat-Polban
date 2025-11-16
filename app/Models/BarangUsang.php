<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangUsang extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tanggal_catat' => 'datetime',
    ];

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
