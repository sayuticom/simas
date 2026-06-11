<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafNonCash extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_non_cashes';

    protected $fillable = [
        'mosque_id',
        'wakif_id',
        'nazhir_id',
        'tanggal_terima',
        'jenis_aset',
        'nama_aset',
        'nilai_estimasi',
        'lokasi',
        'jumlah',
        'luas',
        'nomor_sertifikat',
        'status_dokumen',
        'dokumen_ikrar',
        'dokumen_aset',
        'foto',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'nilai_estimasi' => 'decimal:2',
    ];

    public function wakif()
    {
        return $this->belongsTo(Wakif::class);
    }

    public function nazhir()
    {
        return $this->belongsTo(Nazhir::class);
    }

    public function wakafAssets()
    {
        return $this->hasMany(WakafAsset::class, 'wakaf_non_tunai_id');
    }
}
