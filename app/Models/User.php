<?php

namespace App\Models;

use App\Enums\Role;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Ganti $fillable dengan $guarded untuk kemudahan development.
     */
    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
        ];
    }

    // Relasi ke Master
    public function unitKerja(): BelongsTo
    {
        return $this->belongsTo(UnitKerja::class);
    }

    // Relasi Transaksi (jika user adalah Pemohon)
    public function permintaansDiajukan(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'pemohon_user_id');
    }

    // Relasi Transaksi (jika user adalah Approval)
    public function permintaansDisetujui(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'approval_user_id');
    }

    // Relasi Transaksi (jika user adalah Operator)
    public function barangMasuksDicatat(): HasMany
    {
        return $this->hasMany(BarangMasuk::class, 'dicatat_oleh_user_id');
    }

    public function permintaansDiterima(): HasMany
    {
        return $this->hasMany(Permintaan::class, 'operator_user_id');
    }
    
    public function stockOpnamesDicatat(): HasMany
    {
        return $this->hasMany(StockOpname::class, 'dicatat_oleh_user_id');
    }

    public function barangUsangsDicatat(): HasMany
    {
        return $this->hasMany(BarangUsang::class, 'dicatat_oleh_user_id');
    }
}
