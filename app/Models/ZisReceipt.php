<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ZisReceipt extends Model
{
    use BelongsToMosque, HasFactory;

    public const RECAP_STATUS_BELUM_DIREKAP = 'belum_direkap';

    public const RECAP_STATUS_SUDAH_DIREKAP = 'sudah_direkap';

    protected $fillable = [
        'mosque_id',
        'zis_category_id',
        'cash_account_id',
        'receipt_date',
        'donor_name',
        'donor_phone',
        'amount',
        'payment_method',
        'description',
        'proof_file',
        'public_receipt_token',
        'created_by',
        'jenis_penerimaan',
        'muzakki_id',
        'zis_program_id',
        'tanggal',
        'metode_pembayaran',
        'jenis_zakat',
        'jenis_fitrah',
        'jumlah_jiwa',
        'jumlah_beras',
        'nominal_uang',
        'bukti_file',
        'diterima_oleh',
        'keterangan',
        'receipt_status',
        'receipt_issued_at',
        'receipt_issued_by',
        'recap_status',
        'recapped_at',
        'recapped_by',
        'recap_note',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'receipt_date' => 'date',
        'receipt_issued_at' => 'datetime',
        'recapped_at' => 'datetime',
        'amount' => 'decimal:2',
        'nominal_uang' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $receipt) {
            if (! $receipt->public_receipt_token) {
                $receipt->public_receipt_token = self::generatePublicReceiptToken();
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(ZisCategory::class, 'zis_category_id');
    }

    public function distributions()
    {
        return $this->hasMany(ZisDistribution::class, 'zis_receipt_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function muzakki()
    {
        return $this->belongsTo(Muzakki::class);
    }

    public function program()
    {
        return $this->belongsTo(ZisProgram::class, 'zis_program_id');
    }

    public function recappedBy()
    {
        return $this->belongsTo(User::class, 'recapped_by');
    }

    public function distributionStatus(float|int|null $distributedAmount = null): string
    {
        $amount = (float) ($this->amount ?? $this->nominal_uang ?? 0);
        $distributed = (float) ($distributedAmount ?? $this->distributions()->sum('amount'));
        $remaining = max($amount - $distributed, 0);

        if ($distributed <= 0) {
            return 'Belum Disalurkan';
        }

        return $remaining > 0 ? 'Sebagian' : 'Sudah Disalurkan';
    }

    public static function generatePublicReceiptToken(): string
    {
        do {
            $token = Str::random(40);
        } while (self::withoutGlobalScope('mosque')->where('public_receipt_token', $token)->exists());

        return $token;
    }

    public function ensurePublicReceiptToken(): string
    {
        if (! $this->public_receipt_token) {
            $this->forceFill(['public_receipt_token' => self::generatePublicReceiptToken()])->save();
        }

        return $this->public_receipt_token;
    }

    public function isReceiptIssued(): bool
    {
        return ($this->receipt_status ?? 'belum_diterbitkan') === 'sudah_diterbitkan';
    }

    public function isRecapped(): bool
    {
        return ($this->recap_status ?? self::RECAP_STATUS_BELUM_DIREKAP) === self::RECAP_STATUS_SUDAH_DIREKAP;
    }

    public function isLocked(): bool
    {
        return $this->isReceiptIssued() || $this->isRecapped();
    }

    public function canBeEdited(): bool
    {
        return ! $this->isLocked();
    }

    public function canBeDeleted(): bool
    {
        return ! $this->isLocked();
    }

    public function recapStatusLabel(): string
    {
        return $this->isRecapped() ? 'Sudah Direkap' : 'Belum Direkap';
    }

    public function receiptNumber(): string
    {
        return sprintf('ZIS-%s-%06d', $this->receipt_date?->format('Y') ?? $this->tanggal?->format('Y') ?? now()->format('Y'), $this->id);
    }
}
