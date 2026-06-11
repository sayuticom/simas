<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WakafProgram extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'wakaf_programs';

    protected $fillable = [
        'mosque_id',
        'nama',
        'deskripsi',
        'target_dana',
        'tujuan',
        'status',
    ];

    protected $casts = [
        'target_dana' => 'decimal:2',
    ];

    public function wakafCashes()
    {
        return $this->hasMany(WakafCash::class, 'waqf_program_id');
    }
}
