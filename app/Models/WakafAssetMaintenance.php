<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafAssetMaintenance extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_asset_maintenances';

    protected $fillable = [
        'mosque_id',
        'waqf_asset_id',
        'tanggal_pengeluaran',
        'jenis_biaya',
        'nominal',
        'dibayar_dari',
        'cash_account_id',
        'mosque_cash_transaction_id',
        'bukti_file',
        'penanggung_jawab',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pengeluaran' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }

    public function wakafAsset()
    {
        return $this->belongsTo(WakafAsset::class, 'waqf_asset_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function cashTransaction()
    {
        return $this->belongsTo(Transaction::class, 'mosque_cash_transaction_id');
    }
}
