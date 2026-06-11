<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nazhir extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'nazhirs';

    protected $fillable = [
        'nama',
        'no_hp',
        'alamat',
        'nomor_identitas',
        'jabatan',
        'keterangan',
        'mosque_id',
    ];

    public function wakafCashes()
    {
        return $this->hasMany(WakafCash::class);
    }

    public function wakafNonCashes()
    {
        return $this->hasMany(WakafNonCash::class);
    }

    public function wakafAssets()
    {
        return $this->hasMany(WakafAsset::class);
    }
}
