<?php

namespace App\Models;

use App\Enums\JenisKeperluan;
use App\Enums\StatusPermintaan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Permintaan extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'jenis_keperluan' => JenisKeperluan::class,
        'status' => StatusPermintaan::class,
        'tanggal_pengajuan' => 'datetime',
        'tanggal_disetujui' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'is_transcribed' => 'boolean',
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemohon_user_id');
    }

    public function approval(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_user_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_user_id');
    }

    public function detailPermintaans(): HasMany
    {
        return $this->hasMany(DetailPermintaan::class);
    }
}
