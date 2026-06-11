<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZisReceipt extends Model
{
    use BelongsToMosque, HasFactory;

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
    ];

    protected $casts = [
        'tanggal' => 'date',
        'receipt_date' => 'date',
        'amount' => 'decimal:2',
        'nominal_uang' => 'decimal:2',
    ];

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
}
