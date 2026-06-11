<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DonationProgram extends Model
{
    use BelongsToMosque, HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_ARCHIVED = 'archived';

    public const PAYMENT_MANUAL = 'manual';
    public const PAYMENT_DYNAMIC_QRIS = 'dynamic_qris';
    public const PAYMENT_BOTH = 'both';

    protected $fillable = [
        'mosque_id',
        'title',
        'slug',
        'description',
        'category',
        'target_amount',
        'collected_amount',
        'start_date',
        'end_date',
        'featured_image',
        'qris_image',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'whatsapp_number',
        'status',
        'is_featured',
        'show_on_public',
        'payment_mode',
        'cash_account_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'collected_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_featured' => 'boolean',
        'show_on_public' => 'boolean',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PUBLISHED => 'Published',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public static function paymentModes(): array
    {
        return [
            self::PAYMENT_MANUAL => 'Manual',
            self::PAYMENT_DYNAMIC_QRIS => 'QRIS Dinamis',
            self::PAYMENT_BOTH => 'Keduanya',
        ];
    }

    public function cashAccount(): BelongsTo
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeForMosque(Builder $query, int $mosqueId): Builder
    {
        return $query->where('mosque_id', $mosqueId);
    }

    public function scopeVisiblePublic(Builder $query): Builder
    {
        return $query
            ->where('show_on_public', true)
            ->where(function ($dateQuery) {
                $dateQuery->whereNull('start_date')
                    ->orWhere('start_date', '<=', now()->toDateString());
            })
            ->where(function ($dateQuery) {
                $dateQuery->whereNull('end_date')
                    ->orWhere('end_date', '>=', now()->toDateString());
            });
    }

    public function progressPercentage(): int
    {
        $target = (float) ($this->target_amount ?? 0);

        if ($target <= 0) {
            return 0;
        }

        return min((int) round(((float) $this->collected_amount / $target) * 100), 100);
    }
}
