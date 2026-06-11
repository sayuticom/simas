<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafProductiveAsset extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_productive_assets';

    protected $fillable = [
        'mosque_id',
        'waqf_asset_id',
        'jenis_pengelolaan',
        'nama_penyewa_atau_mitra',
        'tanggal_mulai_kontrak',
        'tanggal_selesai_kontrak',
        'target_pendapatan',
        'periode_pendapatan',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai_kontrak' => 'date',
        'tanggal_selesai_kontrak' => 'date',
        'target_pendapatan' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }

    public function wakafAsset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }

    public function managementResults()
    {
        return $this->hasMany(WakafManagementResult::class, 'productive_waqf_asset_id');
    }
}
