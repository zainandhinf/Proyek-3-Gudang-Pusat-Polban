<?php

namespace App\Models;

use App\Enums\StatusOpname;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tanggal_opname' => 'datetime',
        'status' => StatusOpname::class,
    ];

    public function dicatatOleh(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh_user_id');
    }

    public function detailStockOpnames(): HasMany
    {
        return $this->hasMany(DetailStockOpname::class);
    }
}