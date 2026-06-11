<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZisProgram extends Model
{
    use BelongsToMosque, HasFactory;

    protected $fillable = [
        'nama',
        'deskripsi',
        'target_dana',
        'status',
    ];

    public function receipts()
    {
        return $this->hasMany(ZisReceipt::class);
    }

    public function distributions()
    {
        return $this->hasMany(ZisDistribution::class);
    }
}
