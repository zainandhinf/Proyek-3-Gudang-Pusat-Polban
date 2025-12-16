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
        'tanggal_catat' => 'date',
    ];

    public function detail()
    {
        return $this->hasMany(DetailBarangUsang::class, 'barang_usang_id');
    }

    public function dicatatOleh()
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }
}
