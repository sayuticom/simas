<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MosqueProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'nama_masjid',
        'alamat',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'no_telepon',
        'email',
        'website',
        'nama_ketua_dkm',
        'nama_bendahara',
        'nama_sekretaris',
        'logo',
        'deskripsi_singkat',
    ];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }
}
