<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteSetting extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_NONAKTIF = 'nonaktif';

    public const RESERVED_SUBDOMAINS = [
        'admin',
        'app',
        'api',
        'www',
        'mail',
        'dashboard',
        'login',
        'register',
        'superadmin',
        'assets',
        'storage',
    ];

    protected $fillable = [
        'mosque_id',
        'subdomain',
        'nama_website',
        'slogan',
        'deskripsi_singkat',
        'alamat_publik',
        'no_whatsapp_publik',
        'email_publik',
        'instagram_url',
        'tiktok_url',
        'facebook_url',
        'youtube_url',
        'logo',
        'banner',
        'status_website',
        'show_public_pengumuman',
        'show_public_informasi',
        'show_public_donasi',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'show_public_pengumuman' => 'boolean',
        'show_public_informasi' => 'boolean',
        'show_public_donasi' => 'boolean',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_AKTIF,
            self::STATUS_NONAKTIF,
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->nama_website ?: ($this->mosque?->name ?? 'Website Masjid');
    }

    public function publicUrl(?string $scheme = 'https'): string
    {
        $baseDomain = config('simas.base_domain', 'simas.test');

        return "{$scheme}://{$this->subdomain}.{$baseDomain}";
    }
}
