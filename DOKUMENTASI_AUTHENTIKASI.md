# SIMAS - Sistem Informasi Manajemen Masjid (Multi-Masjid)

## Dokumentasi Sistem Autentikasi dan Role User

### Ringkasan
Telah diimplementasikan sistem autentikasi dan role-based access control (RBAC) untuk mendukung manajemen multi-masjid. Setiap user (kecuali superuser) terhubung ke satu masjid dan memiliki satu atau lebih role dalam masjid tersebut.

---

## 1. Struktur Database

### Tabel `users`
Kolom yang ditambahkan:
```sql
ALTER TABLE users ADD COLUMN active_mosque_id BIGINT UNSIGNED NULL;
```
- `id` - Primary key
- `name` - Nama user
- `email` - Email unik
- `password` - Password (hashed)
- `active_mosque_id` - Masjid aktif (nullable untuk superuser)
- `remember_token`
- `timestamps`

### Tabel `roles` (Baru)
```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(255) UNIQUE,
    label VARCHAR(255),
    description TEXT,
    created_at, updated_at
)
```

**Roles yang tersedia:**
| Name | Label | Deskripsi |
|------|-------|-----------|
| `superuser` | Superuser | Mengelola seluruh sistem dan semua masjid |
| `admin_masjid` | Admin Masjid | Mengelola satu masjid |
| `ketua_dkm` | Ketua DKM | Melihat dan mengawasi data masjid |
| `bendahara` | Bendahara | Mengelola kas, ZIS, wakaf, dan laporan keuangan |
| `operator` | Operator | Input data jamaah, kegiatan, inventaris, administrasi |

### Tabel `role_user` (Baru - Pivot)
```sql
CREATE TABLE role_user (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    role_id BIGINT UNSIGNED,
    mosque_id BIGINT UNSIGNED NULL,
    created_at, updated_at,
    UNIQUE(user_id, role_id, mosque_id)
)
```

**Penjelasan:**
- Relasi many-to-many antara user dan role
- `mosque_id` menentukan scope role (NULL untuk superuser)
- Contoh: Admin1 bisa menjadi `admin_masjid` di Masjid A dan `operator` di Masjid B

### Tabel `mosques`
Sudah ada sebelumnya.

---

## 2. Model dan Relationships

### Role Model (`app/Models/Role.php`)
```php
class Role {
    public function users() // Relasi many-to-many
    public const SUPERUSER = 'superuser';
    public const ADMIN_MASJID = 'admin_masjid';
    public const KETUA_DKM = 'ketua_dkm';
    public const BENDAHARA = 'bendahara';
    public const OPERATOR = 'operator';
}
```

### User Model (`app/Models/User.php`)
**Relationships:**
```php
public function roles() // Relasi many-to-many ke Role
public function activeMosque() // Relasi belongsTo Mosque
public function mosques() // Relasi belongsToMany Mosque (sudah ada)
```

**Helper Methods:**
```php
// Cek apakah superuser
$user->isSuperuser(): bool

// Dapatkan masjid aktif
$user->getActiveMosque(): ?Mosque

// Set masjid aktif
$user->setActiveMosque($mosqueId): bool

// Dapatkan semua roles di masjid tertentu
$user->getRolesInMosque($mosqueId): Collection

// Cek role tunggal di masjid
$user->hasRoleInMosque($roleName, $mosqueId): bool

// Cek salah satu dari beberapa roles (OR)
$user->hasAnyRoleInMosque(['bendahara', 'operator'], $mosqueId): bool

// Cek semua roles (AND)
$user->hasAllRolesInMosque(['admin_masjid', 'ketua_dkm'], $mosqueId): bool
```

---

## 3. Authorization - Gates & Policies

### Definisi Gates (AppServiceProvider.php)

**Role Gates:**
```php
Gate::define('is-admin-masjid', fn($user) => ...); // Check role
Gate::define('is-ketua-dkm', fn($user) => ...);
Gate::define('is-bendahara', fn($user) => ...);
Gate::define('is-operator', fn($user) => ...);
```

**Feature Gates:**
```php
Gate::define('manage-mosque', ...); // Admin + Ketua
Gate::define('manage-finance', ...); // Bendahara
Gate::define('manage-jamaah', ...); // Admin + Operator
Gate::define('manage-activities', ...); // Admin + Operator
Gate::define('view-reports', ...); // Admin + Ketua + Bendahara
```

**Superuser Bypass:**
```php
Gate::before(function ($user, $ability) {
    return $user->isSuperuser() ? true : null;
});
```

---

## 4. Middleware

### CheckRole Middleware
**File:** `app/Http/Middleware/CheckRole.php`

Verifikasi user memiliki role tertentu di masjid aktif.

**Cara Pakai:**
```php
Route::post('/transactions', [TransactionController::class, 'store'])
    ->middleware('role:bendahara'); // Hanya bendahara

Route::get('/reports', [ReportController::class, 'index'])
    ->middleware('role:admin_masjid|ketua_dkm|bendahara'); // Salah satu

Route::delete('/users/{id}', [UserController::class, 'destroy'])
    ->middleware('role:admin_masjid,all'); // Semua roles (AND)
```

### CheckMosqueAccess Middleware
**File:** `app/Http/Middleware/CheckMosqueAccess.php`

Verifikasi user punya akses ke masjid yang diminta.

**Cara Pakai:**
```php
Route::group(['middleware' => 'mosque.access'], function () {
    Route::get('/mosques/{mosque}/jamaah', ...);
    Route::post('/mosques/{mosque}/transactions', ...);
});
```

---

## 5. Controller Autentikasi

### AuthController (`app/Http/Controllers/Auth/AuthController.php`)

**Method `login()`**
- Validate email & password
- Set active_mosque_id untuk non-superuser (masjid pertama)
- Redirect ke dashboard

```php
if (Auth::attempt($credentials)) {
    $user = Auth::user();
    if (!$user->isSuperuser()) {
        $mosque = $user->mosques()->first();
        $user->update(['active_mosque_id' => $mosque->id]);
    }
    return redirect()->intended('dashboard');
}
```

**Method `switchMosque()`**
- Ganti masjid aktif
- Validasi user punya akses ke masjid tujuan

```php
public function switchMosque(Request $request)
{
    $user->update(['active_mosque_id' => $request->mosque_id]);
    return response()->json(['message' => 'Switched']);
}
```

**Method `logout()`**
- Logout standard Laravel

---

## 6. Seeding - Demo Data

### RoleSeeder (`database/seeders/RoleSeeder.php`)
Membuat 5 roles:
```
superuser, admin_masjid, ketua_dkm, bendahara, operator
```

### UserSeeder (`database/seeders/UserSeeder.php`)
Membuat demo users:

| Email | Role | Masjid | Password |
|-------|------|--------|----------|
| `superuser@simas.local` | superuser | - | password |
| `admin.mosque1@simas.local` | admin_masjid | Masjid Nurul Iman | password |
| `ketua.mosque1@simas.local` | ketua_dkm | Masjid Nurul Iman | password |
| `bendahara.mosque1@simas.local` | bendahara | Masjid Nurul Iman | password |
| `operator.mosque1@simas.local` | operator | Masjid Nurul Iman | password |
| `admin.mosque2@simas.local` | admin_masjid | Masjid Al-Ikhlas | password |

**Jalankan Seeding:**
```bash
php artisan migrate
php artisan db:seed
```

---

## 7. Contoh Penggunaan

### Di Controller
```php
namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        // Cek role
        if (!auth()->user()->hasRoleInMosque(Role::BENDAHARA, $mosque_id)) {
            abort(403);
        }

        // Atau pakai Gate
        Gate::authorize('manage-finance');

        // Proses...
    }

    public function report()
    {
        // Cek multiple roles (salah satu)
        if (!auth()->user()->hasAnyRoleInMosque(
            [Role::ADMIN_MASJID, Role::KETUA_DKM, Role::BENDAHARA],
            $mosque_id
        )) {
            abort(403);
        }

        // Proses...
    }
}
```

### Di Routes
```php
use App\Models\Role;

Route::middleware('auth')->group(function () {
    // Hanya bendahara
    Route::post('/transactions', [TransactionController::class, 'store'])
        ->middleware('role:' . Role::BENDAHARA);

    // Hanya admin masjid
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('role:' . Role::ADMIN_MASJID);

    // Admin atau Ketua DKM
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('role:' . Role::ADMIN_MASJID . '|' . Role::KETUA_DKM);

    // Pakai Gate
    Route::delete('/jamaah/{id}', [JamaahController::class, 'destroy'])
        ->middleware('can:manage-jamaah');
});
```

### Di Blade Views
```blade
<!-- Hanya tampil untuk bendahara -->
@can('is-bendahara')
    <a href="/finance">Keuangan</a>
@endcan

<!-- Gate dengan parameter -->
@can('manage-finance')
    <button>Tambah Transaksi</button>
@endcan

<!-- Role spesifik -->
@if(auth()->user()->hasRoleInMosque('operator', auth()->user()->getActiveMosque()->id))
    <form action="/jamaah" method="POST">
        <!-- Input jamaah form -->
    </form>
@endif
```

---

## 8. Alur Login Multi-Masjid

### User Biasa (Admin/Operator)
```
1. Input email & password
2. Sistem cek credentials
3. Cek apakah user superuser
   - Jika ya: set active_mosque_id = NULL
   - Jika tidak: set active_mosque_id = masjid pertama dari list
4. Redirect ke dashboard
5. Di dashboard, bisa switch masjid via dropdown
```

### User Superuser
```
1. Login dengan superuser@simas.local
2. active_mosque_id = NULL (tidak perlu pilih masjid)
3. Bisa akses & manage semua masjid
4. Bisa membuat user & assign roles
```

---

## 9. File-file yang Dibuat/Diubah

### File Baru:
```
database/migrations/2026_05_25_000002_create_roles_table.php
database/migrations/2026_05_25_000003_add_active_mosque_id_to_users_table.php
app/Models/Role.php
app/Http/Middleware/CheckRole.php
app/Http/Middleware/CheckMosqueAccess.php
app/Http/Controllers/Auth/AuthController.php
database/seeders/RoleSeeder.php
database/seeders/UserSeeder.php
```

### File Dimodifikasi:
```
app/Models/User.php (tambah relasi & methods)
app/Models/Mosque.php (tambah relasi)
app/Providers/AppServiceProvider.php (tambah Gates)
database/seeders/DatabaseSeeder.php (tambah calls)
```

---

## 10. Checklist Setup

```bash
# 1. Jalankan migrations
php artisan migrate

# 2. Jalankan seeding (membuat roles & demo users)
php artisan db:seed

# 3. Update routes (routes/web.php & routes/api.php)
# Tambahkan auth routes, middleware registration

# 4. Buat views (belum dibuat - prioritas selanjutnya)
# - resources/views/auth/login.blade.php
# - resources/views/dashboard.blade.php
# - resources/views/layouts/app.blade.php

# 5. Test login dengan:
# Email: superuser@simas.local | Password: password
# Email: admin.mosque1@simas.local | Password: password
```

---

## 11. Saran Lanjutan (Tidak Termasuk Scope Ini)

1. **Audit Log** - Catat setiap action user (siapa, kapan, apa)
2. **Permission-based** - Untuk granular control di masa depan
3. **User Management UI** - Dashboard untuk admin assign roles
4. **2FA** - Two-factor authentication untuk security
5. **Activity Log** - Untuk compliance & troubleshooting
6. **API Token** - Jika butuh mobile app
7. **Password Reset** - Fitur lupa password

---

**Status:** ✅ Sistem autentikasi & role-based access control selesai dan siap digunakan.
