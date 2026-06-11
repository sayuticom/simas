<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Mosque extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'qr_token',
    ];

    public function ensureQrToken(): string
    {
        if ($this->qr_token) {
            return $this->qr_token;
        }

        do {
            $token = Str::random(32);
        } while (self::where('qr_token', $token)->exists());

        $this->forceFill(['qr_token' => $token])->save();

        return $token;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withPivot(['role_id', 'mosque_id']);
    }

    public function photos()
    {
        return $this->hasMany(MosquePhoto::class);
    }

    public function profile()
    {
        return $this->hasOne(MosqueProfile::class);
    }

    public function websiteSetting(): HasOne
    {
        return $this->hasOne(WebsiteSetting::class);
    }
}
