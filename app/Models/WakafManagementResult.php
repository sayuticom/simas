<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafManagementResult extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_management_results';

    protected $fillable = [
        'mosque_id',
        'productive_waqf_asset_id',
        'tanggal_penerimaan',
        'jenis_hasil',
        'nominal',
        'periode',
        'nama_pembayar',
        'bukti_file',
        'masuk_ke_kas_masjid',
        'cash_account_id',
        'mosque_cash_transaction_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penerimaan' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function productiveAsset()
    {
        return $this->belongsTo(WakafProductiveAsset::class, 'productive_waqf_asset_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'mosque_cash_transaction_id');
    }

    public function cashTransaction()
    {
        return $this->belongsTo(Transaction::class, 'mosque_cash_transaction_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }
}
