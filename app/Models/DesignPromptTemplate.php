<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignPromptTemplate extends Model
{
    use HasFactory, SoftDeletes;

    public const MODULE_UMUM = 'umum';
    public const MODULE_KEGIATAN = 'kegiatan';
    public const MODULE_DONASI = 'donasi';
    public const MODULE_PENGUMUMAN = 'pengumuman';
    public const MODULE_BERITA = 'berita';
    public const MODULE_ARTIKEL = 'artikel';
    public const MODULE_INFORMASI = 'informasi';

    protected $fillable = [
        'mosque_id',
        'name',
        'module_type',
        'design_type',
        'canvas_size',
        'platforms',
        'tone',
        'style',
        'color_palette',
        'target_audience',
        'layout_density',
        'elements',
        'required_text_rules',
        'photo_rules',
        'prompt_structure',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'platforms' => 'array',
        'elements' => 'array',
        'required_text_rules' => 'array',
        'photo_rules' => 'array',
        'is_active' => 'boolean',
    ];

    public static function moduleOptions(): array
    {
        return [
            self::MODULE_UMUM => 'Umum',
            self::MODULE_KEGIATAN => 'Kegiatan',
            self::MODULE_DONASI => 'Donasi',
            self::MODULE_PENGUMUMAN => 'Pengumuman',
            self::MODULE_BERITA => 'Berita',
            self::MODULE_ARTIKEL => 'Artikel',
            self::MODULE_INFORMASI => 'Informasi',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function designRequests(): HasMany
    {
        return $this->hasMany(DesignRequest::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForModule(Builder $query, ?string $moduleType): Builder
    {
        return $query->where(function (Builder $query) use ($moduleType) {
            $query->whereNull('module_type')->orWhere('module_type', self::MODULE_UMUM);

            if ($moduleType) {
                $query->orWhere('module_type', $moduleType);
            }
        });
    }

    public function scopeAvailableForMosque(Builder $query, int $mosqueId): Builder
    {
        return $query->where(function (Builder $query) use ($mosqueId) {
            $query->whereNull('mosque_id')->orWhere('mosque_id', $mosqueId);
        });
    }
}
