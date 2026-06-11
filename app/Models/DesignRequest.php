<?php

namespace App\Models;

use App\Models\Traits\BelongsToMosque;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignRequest extends Model
{
    use BelongsToMosque, HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PROMPT_READY = 'prompt_ready';
    public const STATUS_GENERATED = 'generated';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'mosque_id',
        'source_type',
        'source_id',
        'design_prompt_template_id',
        'title',
        'prompt_text',
        'negative_prompt',
        'generated_image_path',
        'reference_image_path',
        'selected_options',
        'source_snapshot',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'selected_options' => 'array',
        'source_snapshot' => 'array',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PROMPT_READY => 'Prompt Siap',
            self::STATUS_GENERATED => 'Generated',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_ARCHIVED => 'Archived',
        ];
    }

    public function mosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(DesignPromptTemplate::class, 'design_prompt_template_id');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
