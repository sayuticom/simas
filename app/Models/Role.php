<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'label', 'description'];

    public const SUPER_SUPERUSER = 'super_superuser';

    public const SUPERUSER = 'superuser';

    public const ADMIN_MASJID = 'admin_masjid';

    public const KETUA_DKM = 'ketua_dkm';

    public const BENDAHARA = 'bendahara';

    public const SEKRETARIS = 'sekretaris';

    public const OPERATOR = 'operator';

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('mosque_id')
            ->withTimestamps();
    }
}
