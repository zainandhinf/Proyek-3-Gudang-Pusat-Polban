<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class MutasiBarang extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tanggal_mutasi' => 'datetime',
    ];


    public function detail(): HasMany
    {
        return $this->hasMany(MutasiBarangDetail::class);
    }


    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }


    public function permintaan(): BelongsTo
    {
        return $this->belongsTo(Permintaan::class);
    }

    public function getTanggalMutasiFormattedAttribute()
    {
        return Carbon::parse($this->tanggal_mutasi)->format('d-m-Y');
    }
}
