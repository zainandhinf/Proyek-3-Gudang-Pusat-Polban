<?php

namespace App\Models;

use App\Enums\TipeUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitKerja extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'tipe_unit' => TipeUnit::class,
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
