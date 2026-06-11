<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use BelongsToMosque, HasFactory;

    public const SOURCE_ZIS_DISTRIBUTION = 'zis_distribution';

    public const SOURCE_WAKAF_CASH = 'wakaf_cash';

    public const SOURCE_WAKAF_MANAGEMENT_RESULT = 'wakaf_management_result';

    public const SOURCE_WAKAF_ASSET_MAINTENANCE = 'wakaf_asset_maintenance';

    protected $fillable = [
        'transaction_date',
        'type',
        'transaction_category_id',
        'cash_account_id',
        'amount',
        'description',
        'payment_method',
        'proof_file',
        'created_by',
        'source_type',
        'source_id',
        'mosque_id',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function sourceDistribution()
    {
        return $this->belongsTo(ZisDistribution::class, 'source_id');
    }

    public function isFromZisDistribution(): bool
    {
        return $this->source_type === self::SOURCE_ZIS_DISTRIBUTION && filled($this->source_id);
    }

    public function isFromWakafCash(): bool
    {
        return $this->source_type === self::SOURCE_WAKAF_CASH && filled($this->source_id);
    }

    public function isFromWakafManagementResult(): bool
    {
        return $this->source_type === self::SOURCE_WAKAF_MANAGEMENT_RESULT && filled($this->source_id);
    }

    public function isFromWakafAssetMaintenance(): bool
    {
        return $this->source_type === self::SOURCE_WAKAF_ASSET_MAINTENANCE && filled($this->source_id);
    }

    public function isFromWakaf(): bool
    {
        return $this->isFromWakafCash()
            || $this->isFromWakafManagementResult()
            || $this->isFromWakafAssetMaintenance();
    }

    public function sourceLabel(): string
    {
        return match ($this->source_type) {
            self::SOURCE_ZIS_DISTRIBUTION => 'Transfer dari ZIS',
            self::SOURCE_WAKAF_CASH => 'Wakaf Tunai',
            self::SOURCE_WAKAF_MANAGEMENT_RESULT => 'Hasil Kelola Wakaf',
            self::SOURCE_WAKAF_ASSET_MAINTENANCE => 'Perawatan Aset Wakaf',
            default => 'Manual',
        };
    }
}
