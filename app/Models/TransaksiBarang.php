<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiBarang extends Model
{
    use HasFactory;

    protected $fillable = ['barang_id', 'tanggal', 'jenis', 'jumlah', 'keterangan'];

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}