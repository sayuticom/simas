<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'active_mosque_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke masjid aktif user saat ini.
     */
    public function activeMosque(): BelongsTo
    {
        return $this->belongsTo(Mosque::class, 'active_mosque_id');
    }

    /**
     * Semua role yang dimiliki user.
     *
     * Pivot role_user:
     * - user_id
     * - role_id
     * - mosque_id
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            Role::class,
            'role_user',
            'user_id',
            'role_id'
        )
            ->withPivot('mosque_id')
            ->withTimestamps();
    }

    /**
     * Semua masjid yang dapat diakses user berdasarkan tabel role_user.
     *
     * Catatan:
     * Karena pivot role_user memakai role_id juga, relasi ini hanya mengambil
     * daftar masjid yang mosque_id-nya tidak null.
     */
    public function mosques(): BelongsToMany
    {
        return $this->belongsToMany(
            Mosque::class,
            'role_user',
            'user_id',
            'mosque_id'
        )
            ->withPivot('role_id')
            ->whereNotNull('role_user.mosque_id')
            ->distinct();
    }

    /**
     * Cek apakah user memiliki akses global sistem.
     *
     * Super superuser dan superuser sama-sama dapat bekerja lintas masjid.
     */
    public function isSuperuser(): bool
    {
        return $this->roles()
            ->whereIn('roles.name', [Role::SUPER_SUPERUSER, Role::SUPERUSER])
            ->wherePivotNull('mosque_id')
            ->exists();
    }

    /**
     * Hanya super superuser yang dapat memberi atau mengubah role superuser.
     */
    public function isSuperSuperuser(): bool
    {
        return $this->roles()
            ->where('roles.name', Role::SUPER_SUPERUSER)
            ->wherePivotNull('mosque_id')
            ->exists();
    }

    /**
     * Cek apakah user memiliki akses ke masjid tertentu.
     */
    public function canAccessMosque($mosqueId): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        if (is_null($mosqueId)) {
            return false;
        }

        return $this->mosques()
            ->where('mosques.id', $mosqueId)
            ->exists();
    }

    /**
     * Cek apakah user merupakan admin pada setidaknya satu masjid.
     */
    public function isMosqueAdmin(): bool
    {
        return $this->roles()
            ->where('roles.name', Role::ADMIN_MASJID)
            ->wherePivotNotNull('mosque_id')
            ->exists();
    }

    /**
     * Daftar masjid yang boleh dipilih sebagai konteks aktif.
     */
    public function selectableMosques(): Collection
    {
        if ($this->isSuperuser()) {
            return Mosque::all();
        }

        if ($this->isMosqueAdmin()) {
            $adminRoleId = Role::where('name', Role::ADMIN_MASJID)->value('id');

            return $this->mosques()
                ->wherePivot('role_id', $adminRoleId)
                ->get();
        }

        return $this->mosques()->get();
    }

    /**
     * Cek apakah user boleh memilih masjid sebagai konteks aktif.
     */
    public function canSelectMosque($mosqueId): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        if ($this->isMosqueAdmin()) {
            return $this->roles()
                ->where('roles.name', Role::ADMIN_MASJID)
                ->wherePivot('mosque_id', $mosqueId)
                ->exists();
        }

        return $this->canAccessMosque($mosqueId);
    }

    /**
     * Menetapkan masjid aktif untuk user.
     *
     * - Superuser boleh active_mosque_id null.
     * - Admin masjid hanya boleh memilih masjid yang dia kelola.
     * - User lainnya wajib memilih masjid yang memang dia miliki aksesnya.
     */
    public function setActiveMosque($mosqueId): bool
    {
        if (is_null($mosqueId)) {
            if ($this->isSuperuser()) {
                return $this->clearActiveMosque();
            }

            return false;
        }

        if (! $this->canSelectMosque($mosqueId)) {
            return false;
        }

        return $this->update([
            'active_mosque_id' => $mosqueId,
        ]);
    }

    /**
     * Mengosongkan konteks masjid aktif saat user perlu memilih kembali.
     */
    public function clearActiveMosque(): bool
    {
        return $this->update([
            'active_mosque_id' => null,
        ]);
    }

    /**
     * Memasangkan role ke user pada masjid tertentu.
     *
     * Untuk superuser:
     * - mosque_id = null
     *
     * Untuk role masjid:
     * - mosque_id wajib diisi
     */
    public function assignRole(string $roleName, $mosqueId = null): void
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        $alreadyExists = $this->roles()
            ->where('roles.id', $role->id)
            ->wherePivot('mosque_id', $mosqueId)
            ->exists();

        if ($alreadyExists) {
            return;
        }

        $this->roles()->attach($role->id, [
            'mosque_id' => $mosqueId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Ambil model masjid aktif.
     */
    public function getActiveMosque(): ?Mosque
    {
        return $this->activeMosque;
    }

    /**
     * Cek apakah user punya role tertentu pada masjid tertentu.
     */
    public function hasRoleInMosque(string $roleName, $mosqueId): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        return $this->roles()
            ->where('roles.name', $roleName)
            ->wherePivot('mosque_id', $mosqueId)
            ->exists();
    }

    /**
     * Cek apakah user punya salah satu role pada masjid tertentu.
     */
    public function hasAnyRoleInMosque(array $roleNames, $mosqueId): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        return $this->roles()
            ->whereIn('roles.name', $roleNames)
            ->wherePivot('mosque_id', $mosqueId)
            ->exists();
    }

    /**
     * Cek apakah user punya semua role pada masjid tertentu.
     */
    public function hasAllRolesInMosque(array $roleNames, $mosqueId): bool
    {
        if ($this->isSuperuser()) {
            return true;
        }

        $count = $this->roles()
            ->whereIn('roles.name', $roleNames)
            ->wherePivot('mosque_id', $mosqueId)
            ->distinct()
            ->count('roles.id');

        return $count === count($roleNames);
    }

    /**
     * Ambil semua role user pada masjid tertentu.
     */
    public function getRolesInMosque($mosqueId): Collection
    {
        if ($this->isSuperSuperuser()) {
            return Role::where('name', Role::SUPER_SUPERUSER)->get();
        }

        if ($this->isSuperuser()) {
            return Role::where('name', Role::SUPERUSER)->get();
        }

        return $this->roles()
            ->wherePivot('mosque_id', $mosqueId)
            ->get();
    }
}
