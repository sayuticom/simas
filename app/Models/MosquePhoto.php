<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MosquePhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'mosque_id',
        'path',
        'caption',
        'sort_order',
        'is_featured',
    ];

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }
}
