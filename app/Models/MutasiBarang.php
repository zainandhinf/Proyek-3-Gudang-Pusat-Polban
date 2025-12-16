<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;
use App\Models\DetailMutasiBarang;

class MutasiBarang extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tanggal_mutasi' => 'date',
    ];


    public function detail(): HasMany
    {
        return $this->hasMany(DetailMutasiBarang::class);
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

    // generate nomor mutasi otomatis: MB-<YEAR>-<0001>
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->nomor_mutasi)) {
                $year = date('Y');
                $count = self::whereYear('created_at', $year)->count() + 1;
                $model->nomor_mutasi = sprintf('MB-%s-%04d', $year, $count);
            }
        });
    }
}
