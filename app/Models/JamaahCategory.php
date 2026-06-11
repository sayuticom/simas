<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JamaahCategory extends Model
{
    protected $fillable = [
        'name',
        'label',
    ];

    public function jamaahs(): BelongsToMany
    {
        return $this->belongsToMany(Jamaah::class, 'jamaah_category')
            ->withTimestamps();
    }
}
