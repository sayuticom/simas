<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafAsset extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_assets';

    protected $fillable = [
        'mosque_id',
        'sumber_wakaf',
        'wakaf_tunai_id',
        'wakaf_non_tunai_id',
        'nazhir_id',
        'jenis_aset',
        'nama_aset',
        'lokasi',
        'nilai_estimasi',
        'kondisi',
        'status_hukum',
        'status_pemanfaatan',
        'produktif',
        'keterangan',
    ];

    protected $casts = [
        'nilai_estimasi' => 'decimal:2',
        'produktif' => 'boolean',
    ];

    public function wakafTunai()
    {
        return $this->belongsTo(WakafCash::class, 'wakaf_tunai_id');
    }

    public function wakafCash()
    {
        return $this->belongsTo(WakafCash::class, 'wakaf_tunai_id');
    }

    public function wakafNonTunai()
    {
        return $this->belongsTo(WakafNonCash::class, 'wakaf_non_tunai_id');
    }

    public function wakafNonCash()
    {
        return $this->belongsTo(WakafNonCash::class, 'wakaf_non_tunai_id');
    }

    public function nazhir()
    {
        return $this->belongsTo(Nazhir::class);
    }

    public function productiveAssets()
    {
        return $this->hasMany(WakafProductiveAsset::class, 'waqf_asset_id');
    }

    public function maintenances()
    {
        return $this->hasMany(WakafAssetMaintenance::class, 'waqf_asset_id');
    }

    public function documents()
    {
        return $this->hasMany(WakafDocument::class, 'waqf_asset_id');
    }
}
