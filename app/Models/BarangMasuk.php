<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;


class BarangMasuk extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tanggal_masuk' => 'datetime',
    ];

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }

    public function detailBarangMasuks(): HasMany
    {
        return $this->hasMany(DetailBarangMasuk::class);
    }
    
    public function getTanggalMasukFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_masuk)->format('d-m-Y');
    }
}
