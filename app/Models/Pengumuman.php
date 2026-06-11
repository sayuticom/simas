<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'pengumumans';

    protected $fillable = [
        'mosque_id',
        'kegiatan_id',
        'judul',
        'slug',
        'excerpt',
        'featured_image',
        'isi',
        'tanggal_mulai',
        'tanggal_selesai',
        'target_audiens',
        'status',
        'published_at',
        'tampil_di_dashboard',
        'dibuat_oleh',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'published_at' => 'datetime',
        'tampil_di_dashboard' => 'boolean',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }
}
