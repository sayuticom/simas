<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'dokumens';

    protected $fillable = [
        'mosque_id',
        'judul',
        'jenis_dokumen',
        'nomor_dokumen',
        'tanggal_dokumen',
        'tanggal_berakhir',
        'file_dokumen',
        'sumber',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_dokumen' => 'date',
        'tanggal_berakhir' => 'date',
    ];
}
