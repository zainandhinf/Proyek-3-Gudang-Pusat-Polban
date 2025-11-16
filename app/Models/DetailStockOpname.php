<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailStockOpname extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false; // Tidak ada timestamps

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
