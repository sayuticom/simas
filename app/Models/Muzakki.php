<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Muzakki extends Model
{
    use BelongsToMosque, HasFactory;

    protected $fillable = [
        'nama',
        'nama_kepala_keluarga',
        'no_hp',
        'alamat',
        'jumlah_anggota_keluarga',
        'keterangan',
    ];

    public function receipts()
    {
        return $this->hasMany(ZisReceipt::class);
    }
}
