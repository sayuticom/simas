<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wakif extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakifs';

    protected $fillable = [
        'nama',
        'jenis_wakif',
        'no_hp',
        'alamat',
        'nomor_identitas',
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
}
