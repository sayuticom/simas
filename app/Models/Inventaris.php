<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'inventaris';

    protected $fillable = [
        'mosque_id',
        'kode_barang',
        'nama_barang',
        'kategori',
        'merk',
        'tipe_model',
        'jumlah',
        'satuan',
        'kondisi',
        'lokasi',
        'tanggal_perolehan',
        'sumber_perolehan',
        'nilai_perolehan',
        'penanggung_jawab',
        'foto',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_perolehan' => 'date',
        'nilai_perolehan' => 'decimal:2',
        'jumlah' => 'integer',
    ];
}
