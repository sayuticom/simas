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
        // Default keluar categories applied per mosque
        self::TYPE_KELUAR => [
            'Dakwah & Kajian',
            'Honor Petugas',
            'Kebersihan',
            'Listrik & Air',
            'ATK & Administrasi',
            'Konsumsi',
            'Perawatan Masjid',
            'Transportasi',
            'Perlengkapan Ibadah',
            'Keamanan',
            'Biaya Admin Bank',
            'Lainnya',
        ],
    ];

    /**
     * Ensure default categories exist for all mosques.
     */
    public static function ensureDefaultsForAllMosques(): void
    {
        // Avoid pulling large models if not necessary; use Mosque model to get ids
        $mosqueModel = app()->makeIf(\App\Models\Mosque::class);
        if (! $mosqueModel) {
            return;
        }

        $mosqueIds = \App\Models\Mosque::query()->pluck('id')->all();

        foreach ($mosqueIds as $mosqueId) {
            self::ensureDefaultsForMosque($mosqueId);
        }
    }

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
