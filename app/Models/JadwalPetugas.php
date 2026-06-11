<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalPetugas extends Model
{
    use BelongsToMosque, HasFactory;

    protected $table = 'jadwal_petugas';

    protected $fillable = [
        'mosque_id',
        'kegiatan_id',
        'user_id',
        'nama_petugas',
        'jenis_tugas',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNamaPetugasLabelAttribute(): string
    {
        return $this->user?->name ?: ($this->nama_petugas ?: '-');
    }
}
