<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZisDistribution extends Model
{
    use BelongsToMosque, HasFactory;

    protected $fillable = [
        'mosque_id',
        'zis_receipt_id',
        'zis_category_id',
        'cash_account_id',
        'distribution_date',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'recipient_type',
        'distribution_target',
        'amount',
        'description',
        'proof_file',
        'created_by',
        'mustahik_id',
        'zis_program_id',
        'tanggal',
        'sumber_dana',
        'kategori_asnaf',
        'jenis_bantuan',
        'nominal',
        'nama_barang',
        'jumlah_barang',
        'disalurkan_oleh',
        'bukti_serah_terima',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'distribution_date' => 'date',
        'amount' => 'decimal:2',
        'nominal' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(ZisCategory::class, 'zis_category_id');
    }

    public function receipt()
    {
        return $this->belongsTo(ZisReceipt::class, 'zis_receipt_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function operationalTransaction()
    {
        return $this->hasOne(Transaction::class, 'source_id')
            ->where('source_type', 'zis_distribution');
    }

    public function mustahik()
    {
        return $this->belongsTo(Mustahik::class);
    }

    public function program()
    {
        return $this->belongsTo(ZisProgram::class, 'zis_program_id');
    }
}
