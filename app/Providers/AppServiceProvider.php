<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\DonationProgram;
use App\Models\Kegiatan;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'kegiatan' => Kegiatan::class,
            'donasi' => DonationProgram::class,
        ]);

        $this->registerGates();
    }

    /**
     * Register authorization gates
     */
    private function registerGates(): void
    {
        // Superuser dapat melakukan apapun
        Gate::before(function ($user, $ability) {
            return $user->isSuperuser() ? true : null;
        });

        // Gate untuk setiap role di mosque yang aktif
        Gate::define('is-admin-masjid', function ($user) {
            $mosque = $user->getActiveMosque();

            return $mosque ? $user->hasRoleInMosque(Role::ADMIN_MASJID, $mosque->id) : false;
        });

        Gate::define('is-ketua-dkm', function ($user) {
            $mosque = $user->getActiveMosque();

            return $mosque ? $user->hasRoleInMosque(Role::KETUA_DKM, $mosque->id) : false;
        });

        Gate::define('is-bendahara', function ($user) {
            $mosque = $user->getActiveMosque();

            return $mosque ? $user->hasRoleInMosque(Role::BENDAHARA, $mosque->id) : false;
        });

        Gate::define('is-operator', function ($user) {
            $mosque = $user->getActiveMosque();

            return $mosque ? $user->hasRoleInMosque(Role::OPERATOR, $mosque->id) : false;
        });

        // Gates untuk fitur-fitur
        Gate::define('manage-mosque', function ($user) {
            $mosque = $user->getActiveMosque();
            if (! $mosque) {
                return false;
            }

            return $user->hasAnyRoleInMosque([
                Role::ADMIN_MASJID,
                Role::KETUA_DKM,
            ], $mosque->id);
        });

        Gate::define('manage-finance', function ($user) {
            $mosque = $user->getActiveMosque();
            if (! $mosque) {
                return false;
            }

            return $user->hasRoleInMosque(Role::BENDAHARA, $mosque->id);
        });

        Gate::define('manage-jamaah', function ($user) {
            $mosque = $user->getActiveMosque();
            if (! $mosque) {
                return false;
            }

            return $user->hasAnyRoleInMosque([
                Role::ADMIN_MASJID,
                Role::OPERATOR,
            ], $mosque->id);
        });

        Gate::define('manage-activities', function ($user) {
            $mosque = $user->getActiveMosque();
            if (! $mosque) {
                return false;
            }

            return $user->hasAnyRoleInMosque([
                Role::ADMIN_MASJID,
                Role::OPERATOR,
            ], $mosque->id);
        });

        Gate::define('view-reports', function ($user) {
            $mosque = $user->getActiveMosque();
            if (! $mosque) {
                return false;
            }

            return $user->hasAnyRoleInMosque([
                Role::ADMIN_MASJID,
                Role::KETUA_DKM,
                Role::BENDAHARA,
            ], $mosque->id);
        });
    }
}
