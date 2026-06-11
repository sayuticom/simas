<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait BelongsToMosque
{
    public static function bootBelongsToMosque()
    {
        static::addGlobalScope('mosque', function (Builder $builder) {
            $mosqueId = session('active_mosque_id');
            if ($mosqueId) {
                $builder->where($builder->getQuery()->from.'.mosque_id', $mosqueId);
            }
        });
        static::creating(function ($model) {
            $mosqueId = session('active_mosque_id');
            if ($mosqueId && empty($model->mosque_id)) {
                $model->mosque_id = $mosqueId;
            }
        });
    }

    public function mosque()
    {
        return $this->belongsTo(\App\Models\Mosque::class);
    }
}
