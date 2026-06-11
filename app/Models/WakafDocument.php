<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafDocument extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_documents';

    protected $fillable = [
        'mosque_id',
        'waqf_asset_id',
        'jenis_dokumen',
        'nomor_dokumen',
        'file_dokumen',
        'tanggal_terbit',
        'tanggal_berakhir',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
        'tanggal_berakhir' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }

    public function wakafAsset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }
}
