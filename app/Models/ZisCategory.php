<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZisCategory extends Model
{
    use HasFactory;

    public const TYPE_ZAKAT = 'zakat';

    public const TYPE_INFAK = 'infak';

    public const TYPE_SEDEKAH = 'sedekah';

    public const TYPE_WAKAF = 'wakaf';

    public const TYPE_BANTUAN = 'bantuan';

    public const TYPE_DONASI = 'donasi';

    public const TYPE_PENDAPATAN_LAYANAN = 'pendapatan_layanan';

    public const TYPE_LAINNYA = 'lainnya';

    public const USAGE_BEBAS_OPERASIONAL = 'bebas_operasional';

    public const USAGE_KHUSUS_PROGRAM = 'khusus_program';

    public const USAGE_KHUSUS_MUSTAHIK = 'khusus_mustahik';

    public const USAGE_WAKAF = 'wakaf';

    public const USAGE_TERIKAT_PERJANJIAN = 'terikat_perjanjian';

    public const TYPE_OPTIONS = [
        self::TYPE_ZAKAT => 'Zakat',
        self::TYPE_INFAK => 'Infak',
        self::TYPE_SEDEKAH => 'Sedekah',
        self::TYPE_WAKAF => 'Wakaf',
        self::TYPE_BANTUAN => 'Bantuan',
        self::TYPE_DONASI => 'Donasi',
        self::TYPE_PENDAPATAN_LAYANAN => 'Pendapatan Layanan',
        self::TYPE_LAINNYA => 'Lainnya',
    ];

    public const USAGE_OPTIONS = [
        self::USAGE_BEBAS_OPERASIONAL => 'Bebas Operasional',
        self::USAGE_KHUSUS_PROGRAM => 'Khusus Program',
        self::USAGE_KHUSUS_MUSTAHIK => 'Khusus Mustahik',
        self::USAGE_WAKAF => 'Wakaf',
        self::USAGE_TERIKAT_PERJANJIAN => 'Terikat Perjanjian',
    ];

    public const DEFAULT_CATEGORIES = [
        ['name' => 'Zakat Fitrah', 'type' => self::TYPE_ZAKAT, 'usage_type' => self::USAGE_KHUSUS_MUSTAHIK, 'allow_operational_transfer' => false],
        ['name' => 'Zakat Maal', 'type' => self::TYPE_ZAKAT, 'usage_type' => self::USAGE_KHUSUS_MUSTAHIK, 'allow_operational_transfer' => false],
        ['name' => 'Zakat Profesi', 'type' => self::TYPE_ZAKAT, 'usage_type' => self::USAGE_KHUSUS_MUSTAHIK, 'allow_operational_transfer' => false],
        ['name' => 'Zakat Pertanian', 'type' => self::TYPE_ZAKAT, 'usage_type' => self::USAGE_KHUSUS_MUSTAHIK, 'allow_operational_transfer' => false],
        ['name' => 'Infak Jumat', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
        ['name' => 'Infak Subuh', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
        ['name' => 'Infak Kotak Amal', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
        ['name' => 'Infak Operasional Masjid', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
        ['name' => 'Infak Kajian', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_KHUSUS_PROGRAM, 'allow_operational_transfer' => false],
        ['name' => 'Infak Pembangunan', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_KHUSUS_PROGRAM, 'allow_operational_transfer' => false],
        ['name' => 'Infak Via QRIS', 'type' => self::TYPE_INFAK, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
        ['name' => 'Sedekah Umum', 'type' => self::TYPE_SEDEKAH, 'usage_type' => self::USAGE_BEBAS_OPERASIONAL, 'allow_operational_transfer' => true],
    ];

    protected $fillable = [
        'mosque_id',
        'name',
        'type',
        'usage_type',
        'allow_operational_transfer',
        'is_active',
    ];

    protected $casts = [
        'allow_operational_transfer' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function receipts()
    {
        return $this->hasMany(ZisReceipt::class, 'zis_category_id');
    }

    public function distributions()
    {
        return $this->hasMany(ZisDistribution::class, 'zis_category_id');
    }

    public static function ensureDefaultsForMosque(int $mosqueId): void
    {
        foreach (self::DEFAULT_CATEGORIES as $category) {
            self::updateOrCreate(
                [
                    'mosque_id' => $mosqueId,
                    'name' => $category['name'],
                ],
                [
                    'type' => $category['type'],
                    'usage_type' => $category['usage_type'],
                    'allow_operational_transfer' => $category['allow_operational_transfer'],
                    'is_active' => true,
                ]
            );
        }
    }

    public function isZakat(): bool
    {
        return $this->type === self::TYPE_ZAKAT;
    }
}
