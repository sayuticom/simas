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

    /**
     * Default kategori hanya untuk OPERASIONAL KELUAR
     * (dipakai di Keuangan Operasional SIMAS)
     */
    public const DEFAULT_OPERASIONAL_KELUAR = [
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

    /*
    |-------------------------------------------------------
    | RELATION
    |-------------------------------------------------------
    */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mosque()
    {
        return $this->belongsTo(Mosque::class);
    }

    /*
    |-------------------------------------------------------
    | SEED DEFAULT PER MOSQUE
    |-------------------------------------------------------
    */
    public static function ensureDefaultsForMosque(int $mosqueId): void
    {
        foreach (self::DEFAULT_OPERASIONAL_KELUAR as $name) {
            self::firstOrCreate(
                [
                    'mosque_id' => $mosqueId,
                    'name' => $name,
                    'type' => self::TYPE_KELUAR,
                ],
                [
                    'description' => 'Kategori pengeluaran operasional',
                    'is_active' => true,
                ]
            );
        }
    }

    /**
     * Ensure default categories exist for all mosques.
     */
    public static function ensureDefaultsForAllMosques(): void
    {
        $mosqueIds = Mosque::query()->pluck('id');

        foreach ($mosqueIds as $mosqueId) {
            self::ensureDefaultsForMosque($mosqueId);
        }
    }
}