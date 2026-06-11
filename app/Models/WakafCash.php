<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafCash extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_cashes';

    protected $fillable = [
        'mosque_id',
        'wakif_id',
        'nazhir_id',
        'waqf_program_id',
        'tanggal_terima',
        'nominal',
        'tujuan_investasi',
        'metode_pembayaran',
        'cash_account_id',
        'bukti_file',
        'dokumen_ikrar',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function wakif()
    {
        return $this->belongsTo(Wakif::class);
    }

    public function nazhir()
    {
        return $this->belongsTo(Nazhir::class);
    }

    public function program()
    {
        return $this->belongsTo(WakafProgram::class, 'waqf_program_id');
    }

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }
}
