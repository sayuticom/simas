<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use BelongsToMosque, HasFactory;

    public const TYPE_TUNAI = 'tunai';

    public const TYPE_BANK = 'bank';

    public const TYPE_QRIS = 'qris';

    public const TYPE_EWALLET = 'ewallet';

    public const TYPE_LAINNYA = 'lainnya';

    public const ACCOUNT_TYPE_CASH = 'cash';

    public const ACCOUNT_TYPE_BANK = 'bank';

    public const ACCOUNT_TYPE_QRIS = 'qris';

    public const ACCOUNT_TYPE_EWALLET = 'ewallet';

    public const ACCOUNT_TYPE_OTHER = 'other';

    public const TYPE_OPTIONS = [
        self::TYPE_TUNAI => 'Tunai',
        self::TYPE_BANK => 'Bank',
        self::TYPE_QRIS => 'QRIS',
        self::TYPE_EWALLET => 'E-Wallet',
        self::TYPE_LAINNYA => 'Lainnya',
    ];

    public const ACCOUNT_TYPE_OPTIONS = [
        self::ACCOUNT_TYPE_CASH => 'Tunai',
        self::ACCOUNT_TYPE_BANK => 'Bank/Transfer',
        self::ACCOUNT_TYPE_QRIS => 'QRIS',
        self::ACCOUNT_TYPE_EWALLET => 'E-Wallet',
        self::ACCOUNT_TYPE_OTHER => 'Lainnya',
    ];

    public const DEFAULT_ACCOUNTS = [
        ['name' => 'Kas Tunai', 'type' => self::TYPE_TUNAI],
        ['name' => 'Rekening Bank', 'type' => self::TYPE_BANK],
    ];

    protected $fillable = [
        'mosque_id',
        'name',
        'type',
        'account_type',
        'bank_name',
        'account_number',
        'account_holder',
        'is_active',
        'can_receive_zis',
        'can_distribute_zis',
        'can_operational',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'can_receive_zis' => 'boolean',
        'can_distribute_zis' => 'boolean',
        'can_operational' => 'boolean',
    ];

    public function receipts()
    {
        return $this->hasMany(ZisReceipt::class);
    }

    public function distributions()
    {
        return $this->hasMany(ZisDistribution::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(CashAccountTransfer::class, 'from_cash_account_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(CashAccountTransfer::class, 'to_cash_account_id');
    }

    public function zisBalance(): float
    {
        $receipts = (float) $this->receipts()->sum('amount');
        $distributions = (float) $this->distributions()->sum('amount');

        return $receipts - $distributions;
    }

    public function operationalBalance(): float
    {
        $incoming = (float) $this->transactions()
            ->where('type', TransactionCategory::TYPE_MASUK)
            ->sum('amount');
        $outgoing = (float) $this->transactions()
            ->where('type', TransactionCategory::TYPE_KELUAR)
            ->sum('amount');

        return $incoming - $outgoing;
    }

    public function transferBalance(): float
    {
        $incoming = (float) $this->incomingTransfers()->sum('amount');
        $outgoing = (float) $this->outgoingTransfers()->sum('amount');

        return $incoming - $outgoing;
    }

    public function availableBalance(): float
    {
        return $this->zisBalance() + $this->operationalBalance() + $this->transferBalance();
    }

    public function accountTypeLabel(): string
    {
        return self::ACCOUNT_TYPE_OPTIONS[$this->account_type] ?? self::TYPE_OPTIONS[$this->type] ?? ucfirst((string) $this->type);
    }

    public function paymentMethodValue(): string
    {
        return match ($this->account_type) {
            self::ACCOUNT_TYPE_CASH => 'tunai',
            self::ACCOUNT_TYPE_BANK => 'transfer',
            self::ACCOUNT_TYPE_QRIS => 'qris',
            self::ACCOUNT_TYPE_EWALLET => 'ewallet',
            default => 'lainnya',
        };
    }

    public static function accountTypeForType(string $type): string
    {
        return match ($type) {
            self::TYPE_TUNAI => self::ACCOUNT_TYPE_CASH,
            self::TYPE_BANK => self::ACCOUNT_TYPE_BANK,
            self::TYPE_QRIS => self::ACCOUNT_TYPE_QRIS,
            self::TYPE_EWALLET => self::ACCOUNT_TYPE_EWALLET,
            default => self::ACCOUNT_TYPE_OTHER,
        };
    }

    public static function defaultUsageFlagsForAccountType(string $accountType): array
    {
        $allowsOutgoing = in_array($accountType, [self::ACCOUNT_TYPE_CASH, self::ACCOUNT_TYPE_BANK], true);

        return [
            'can_receive_zis' => true,
            'can_distribute_zis' => $allowsOutgoing,
            'can_operational' => $allowsOutgoing,
        ];
    }

    public static function ensureDefaultsForMosque(int $mosqueId): void
    {
        foreach (self::DEFAULT_ACCOUNTS as $account) {
            $cashAccount = self::firstOrNew([
                'mosque_id' => $mosqueId,
                'name' => $account['name'],
            ]);

            if (! $cashAccount->exists) {
                $cashAccount->fill([
                    'type' => $account['type'],
                    'account_type' => self::accountTypeForType($account['type']),
                    'is_active' => true,
                    ...self::defaultUsageFlagsForAccountType(self::accountTypeForType($account['type'])),
                ])->save();

                continue;
            }

            if ($cashAccount->type !== $account['type'] || blank($cashAccount->account_type)) {
                $cashAccount->update([
                    'type' => $account['type'],
                    'account_type' => $cashAccount->account_type ?: self::accountTypeForType($account['type']),
                ]);
            }
        }
    }

    public static function defaultTunaiForMosque(int $mosqueId): self
    {
        self::ensureDefaultsForMosque($mosqueId);

        return self::where('mosque_id', $mosqueId)
            ->where('type', self::TYPE_TUNAI)
            ->orderBy('id')
            ->firstOrFail();
    }
}
