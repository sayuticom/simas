<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mustahik extends Model
{
    use BelongsToMosque, HasFactory;

    protected $fillable = [
        'nama',
        'nik',
        'no_hp',
        'alamat',
        'kategori_asnaf',
        'kondisi_ekonomi',
        'jumlah_tanggungan',
        'status_verifikasi',
        'catatan_survei',
        'foto',
    ];

    protected $casts = [
        'status_verifikasi' => 'boolean',
    ];

    public function distributions()
    {
        return $this->hasMany(ZisDistribution::class);
    }
}
