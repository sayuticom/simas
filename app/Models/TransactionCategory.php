<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    use HasFactory;

    public const TYPE_MASUK = 'masuk';

    public const TYPE_KELUAR = 'keluar';

    public const TYPE_OPTIONS = [
        self::TYPE_MASUK => 'Masuk',
        self::TYPE_KELUAR => 'Keluar',
    ];

    public const DEFAULT_CATEGORIES = [
        self::TYPE_MASUK => [
            "Infak Jum'at",
            'Kotak Amal',
            'Donasi Jamaah',
            'Wakaf Tunai',
            'Zakat',
            'Sedekah',
            'Sewa Aula',
            'Bantuan Pemerintah',
        ],
        self::TYPE_KELUAR => [
            'Operasional Masjid',
            'Listrik & Air',
            'Kebersihan',
            'Konsumsi',
            'Honor Petugas',
            'Perbaikan Bangunan',
            'Dakwah & Kajian',
            'Sosial Jamaah',
        ],
    ];

    protected $fillable = [
        'mosque_id',
        'name',
        'type',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }

    public static function ensureDefaultsForMosque(int $mosqueId): void
    {
        foreach (self::DEFAULT_CATEGORIES as $type => $names) {
            foreach ($names as $name) {
                self::firstOrCreate(
                    [
                        'mosque_id' => $mosqueId,
                        'name' => $name,
                    ],
                    [
                        'type' => $type,
                        'is_active' => $type === self::TYPE_KELUAR,
                    ]
                );
            }
        }
    }
}
