<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jamaah extends Model
{
    use BelongsToMosque, HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_INACTIVE = 'inactive';

    public const CATEGORY_OPTIONS = [
        'jamaah_tetap' => 'Jamaah Tetap',
        'jamaah_aktif' => 'Jamaah Aktif',
        'pengurus' => 'Pengurus',
        'donatur' => 'Donatur',
        'muzakki' => 'Muzakki',
        'mustahik' => 'Mustahik',
        'remaja_masjid' => 'Remaja Masjid',
        'jamaah_tamu' => 'Jamaah Tamu',
    ];

    public const PEKERJAAN_LAINNYA = 'Lainnya';

    public const PEKERJAAN_OPTIONS = [
        'Pelajar/Mahasiswa',
        'Guru/Dosen',
        "Ustadz/Da'i",
        'Karyawan Swasta',
        'PNS/ASN',
        'TNI/Polri',
        'Wiraswasta',
        'Pedagang',
        'Petani/Nelayan',
        'Buruh',
        'Ojol / Driver Online',
        'Ibu Rumah Tangga',
        'Pensiunan',
        'Belum Bekerja',
        self::PEKERJAAN_LAINNYA,
    ];

    public const STATUS_OPTIONS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_VERIFIED => 'Verified',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    protected $fillable = [
        'nama',
        'jenis_kelamin',
        'alamat',
        'no_hp',
        'kategori',
        'tanggal_lahir',
        'umur',
        'pekerjaan',
        'keahlian',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'umur' => 'integer',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(JamaahCategory::class, 'jamaah_category')
            ->withTimestamps();
    }

    public function getUmurTampilanAttribute(): ?int
    {
        return $this->tanggal_lahir?->age ?? $this->umur;
    }
}
