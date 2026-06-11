<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CashAccountController;
use App\Http\Controllers\CashAccountTransferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DesignPromptTemplateController;
use App\Http\Controllers\DesignRequestController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\DonationProgramController;
use App\Http\Controllers\InventarisController;
use App\Http\Controllers\JamaahController;
use App\Http\Controllers\JamaahQrController;
use App\Http\Controllers\JadwalPetugasController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MosqueProfileController;
use App\Http\Controllers\MustahikController;
use App\Http\Controllers\MuzakkiController;
use App\Http\Controllers\NazhirController;
use App\Http\Controllers\PlaceholderController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionCategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserInvitationController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\PublicWebsiteController;
use App\Http\Controllers\WebsiteSettingController;
use App\Http\Controllers\WebsitePostController;
use App\Http\Controllers\WakafAssetController;
use App\Http\Controllers\WakafAssetMaintenanceController;
use App\Http\Controllers\WakafCashController;
use App\Http\Controllers\WakafController;
use App\Http\Controllers\WakafDocumentController;
use App\Http\Controllers\WakafManagementResultController;
use App\Http\Controllers\WakafNonCashController;
use App\Http\Controllers\WakafProductiveAssetController;
use App\Http\Controllers\WakafProgramController;
use App\Http\Controllers\WakifController;
use App\Http\Controllers\ZisCategoryController;
use App\Http\Controllers\ZisController;
use App\Http\Controllers\ZisDistributionController;
use App\Http\Controllers\ZisProgramController;
use App\Http\Controllers\ZisReceiptController;
use App\Http\Controllers\ZisReportController;
use Illuminate\Support\Facades\Route;

Route::domain('{subdomain}.' . config('simas.base_domain'))->group(function () {
    Route::get('/', [PublicWebsiteController::class, 'home'])->name('public-website.home');
    Route::get('/profil', [PublicWebsiteController::class, 'profile'])->name('public-website.profile');
    Route::get('/kegiatan', [PublicWebsiteController::class, 'events'])->name('public-website.events');
    Route::get('/donasi', [PublicWebsiteController::class, 'donasi'])->name('public-website.donasi');
    Route::get('/donasi/{slug}', [PublicWebsiteController::class, 'donasiShow'])->name('public-website.donasi.show');
    Route::get('/pengumuman', [PublicWebsiteController::class, 'pengumuman'])->name('public-website.pengumuman');
    Route::get('/pengumuman/{slug}', [PublicWebsiteController::class, 'pengumumanShow'])->name('public-website.pengumuman.show');
    Route::get('/berita', [PublicWebsiteController::class, 'berita'])->name('public-website.berita');
    Route::get('/berita/{slug}', [PublicWebsiteController::class, 'beritaShow'])->name('public-website.berita.show');
    Route::get('/artikel', [PublicWebsiteController::class, 'artikel'])->name('public-website.artikel');
    Route::get('/artikel/{slug}', [PublicWebsiteController::class, 'artikelShow'])->name('public-website.artikel.show');
    Route::get('/informasi', [PublicWebsiteController::class, 'informasi'])->name('public-website.informasi');
    Route::get('/informasi/{slug}', [PublicWebsiteController::class, 'informasiShow'])->name('public-website.informasi.show');
    Route::get('/kontak', [PublicWebsiteController::class, 'contact'])->name('public-website.contact');
});

Route::get('/', fn () => redirect()->route('login'));
Route::get('/tanda-terima-zis/{token}', [ZisReceiptController::class, 'publicReceipt'])->name('zis.penerimaan.receipt.public');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register/invitation/{token}', [UserInvitationController::class, 'showRegisterForm'])->name('invitations.register');
Route::post('/register/invitation/{token}', [UserInvitationController::class, 'submitRegisterForm'])->name('invitations.submit');
Route::get('/jamaah/daftar/{token}', [JamaahQrController::class, 'showPublicForm'])->name('jamaah.public.create');
Route::post('/jamaah/daftar/{token}', [JamaahQrController::class, 'storePublicForm'])->name('jamaah.public.store');

Route::middleware('auth')->group(function () {
    Route::get('/select-mosque', [AuthController::class, 'showMosqueSelect'])->name('mosque.select');
    Route::post('/switch-mosque', [AuthController::class, 'switchMosque'])->name('mosque.switch');
    Route::post('/all-mosques', [AuthController::class, 'showAllMosques'])->name('mosque.all');
    Route::get('/akun/password', [ProfileController::class, 'editPassword'])->name('account.password.edit');
    Route::put('/akun/password', [ProfileController::class, 'updatePassword'])->name('account.password.update');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::get('/user-invitations', [UserInvitationController::class, 'index'])->name('user-invitations.index');
    Route::get('/user-invitations/create', [UserInvitationController::class, 'create'])->name('user-invitations.create');
    Route::post('/user-invitations', [UserInvitationController::class, 'store'])->name('user-invitations.store');
    Route::post('/user-invitations/{invitation}/approve', [UserInvitationController::class, 'approve'])->name('user-invitations.approve');
    Route::post('/user-invitations/{invitation}/cancel', [UserInvitationController::class, 'cancel'])->name('user-invitations.cancel');
    // Create new mosque
    Route::get('/mosque/create', [\App\Http\Controllers\MosqueController::class, 'create'])->name('mosque.create');
    Route::post('/mosque', [\App\Http\Controllers\MosqueController::class, 'store'])->name('mosque.store');
    // Mosque photos (profile gallery)
    Route::post('/profil-masjid/photo', [\App\Http\Controllers\MosquePhotoController::class, 'store'])->name('profile.photo.store');
    Route::delete('/profil-masjid/photo/{photo}', [\App\Http\Controllers\MosquePhotoController::class, 'destroy'])->name('profile.photo.destroy');
    Route::post('/profil-masjid/photo/{photo}/feature', [\App\Http\Controllers\MosquePhotoController::class, 'feature'])->name('profile.photo.feature');
});

// Admin Routes (requires authentication and active mosque)
// Dashboard should be accessible without forcing an active mosque so users can select from it
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/jamaah/qr', [JamaahQrController::class, 'showQr'])->name('jamaah.qr');
});

Route::middleware(['web', 'auth', \App\Http\Middleware\EnsureActiveMosque::class])->group(function () {
    Route::get('/profil-masjid', [MosqueProfileController::class, 'index'])->name('profile');
    Route::put('/profil-masjid', [MosqueProfileController::class, 'update'])->name('profile.update');
    Route::get('/website-masjid/pengaturan', [WebsiteSettingController::class, 'edit'])->name('website-settings.edit');
    Route::put('/website-masjid/pengaturan', [WebsiteSettingController::class, 'update'])->name('website-settings.update');
    Route::resource('jamaah', JamaahController::class)->except(['show']);
    Route::get('/jamaah/{jamaah}', [JamaahController::class, 'show'])->name('jamaah.show');
    Route::resource('kegiatan', KegiatanController::class);
    Route::resource('design-prompt-templates', DesignPromptTemplateController::class)->parameters([
        'design-prompt-templates' => 'designPromptTemplate',
    ]);
    Route::resource('design-requests', DesignRequestController::class)->parameters([
        'design-requests' => 'designRequest',
    ]);
    Route::resource('donation-programs', DonationProgramController::class)->parameters([
        'donation-programs' => 'donationProgram',
    ]);
    Route::resource('website-posts', WebsitePostController::class)->parameters([
        'website-posts' => 'websitePost',
    ]);
    Route::resource('jadwal-petugas', JadwalPetugasController::class)->parameters([
        'jadwal-petugas' => 'jadwalPetugas',
    ]);
    Route::resource('pengumuman', PengumumanController::class);
    Route::resource('inventaris', InventarisController::class)->parameters([
        'inventaris' => 'inventaris',
    ]);
    Route::resource('dokumen', DokumenController::class)->parameters([
        'dokumen' => 'dokumen',
    ]);
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    Route::get('/keuangan', [TransactionController::class, 'index'])->name('keuangan.index');
    Route::get('/keuangan/kategori', [TransactionCategoryController::class, 'index'])->name('keuangan.kategori.index');
    Route::get('/keuangan/kategori/create', [TransactionCategoryController::class, 'create'])->name('keuangan.kategori.create');
    Route::post('/keuangan/kategori', [TransactionCategoryController::class, 'store'])->name('keuangan.kategori.store');
    Route::get('/keuangan/kategori/{category}/edit', [TransactionCategoryController::class, 'edit'])->name('keuangan.kategori.edit');
    Route::put('/keuangan/kategori/{category}', [TransactionCategoryController::class, 'update'])->name('keuangan.kategori.update');
    Route::delete('/keuangan/kategori/{category}', [TransactionCategoryController::class, 'destroy'])->name('keuangan.kategori.destroy');
    Route::get('/keuangan/akun-kas', [CashAccountController::class, 'index'])->name('keuangan.akun-kas.index');
    Route::get('/keuangan/akun-kas/create', [CashAccountController::class, 'create'])->name('keuangan.akun-kas.create');
    Route::post('/keuangan/akun-kas', [CashAccountController::class, 'store'])->name('keuangan.akun-kas.store');
    Route::get('/keuangan/akun-kas/{cashAccount}/edit', [CashAccountController::class, 'edit'])->name('keuangan.akun-kas.edit');
    Route::put('/keuangan/akun-kas/{cashAccount}', [CashAccountController::class, 'update'])->name('keuangan.akun-kas.update');
    Route::delete('/keuangan/akun-kas/{cashAccount}', [CashAccountController::class, 'destroy'])->name('keuangan.akun-kas.destroy');
    Route::get('/keuangan/mutasi-akun-kas', [CashAccountTransferController::class, 'index'])->name('keuangan.mutasi-akun-kas.index');
    Route::get('/keuangan/mutasi-akun-kas/create', [CashAccountTransferController::class, 'create'])->name('keuangan.mutasi-akun-kas.create');
    Route::post('/keuangan/mutasi-akun-kas', [CashAccountTransferController::class, 'store'])->name('keuangan.mutasi-akun-kas.store');
    Route::get('/keuangan/mutasi-akun-kas/{cashAccountTransfer}', [CashAccountTransferController::class, 'show'])->name('keuangan.mutasi-akun-kas.show');
    Route::get('/keuangan/transaksi/create', [TransactionController::class, 'create'])->name('keuangan.transaksi.create');
    Route::post('/keuangan/transaksi', [TransactionController::class, 'store'])->name('keuangan.transaksi.store');
    Route::get('/keuangan/transaksi/{transaction}', [TransactionController::class, 'show'])->name('keuangan.transaksi.show');
    Route::get('/keuangan/transaksi/{transaction}/edit', [TransactionController::class, 'edit'])->name('keuangan.transaksi.edit');
    Route::put('/keuangan/transaksi/{transaction}', [TransactionController::class, 'update'])->name('keuangan.transaksi.update');
    Route::delete('/keuangan/transaksi/{transaction}', [TransactionController::class, 'destroy'])->name('keuangan.transaksi.destroy');

    Route::prefix('zis')->name('zis.')->group(function () {
        Route::get('/', [ZisController::class, 'index'])->name('index');
        Route::get('/laporan', [ZisReportController::class, 'index'])->name('reports.index');
        Route::resource('kategori', ZisCategoryController::class)
            ->except(['show'])
            ->parameters(['kategori' => 'category'])
            ->names('categories');
        Route::get('/penerimaan/{receipt}/kwitansi', [ZisReceiptController::class, 'kwitansi'])->name('receipts.kwitansi');
        Route::resource('penerimaan', ZisReceiptController::class)
            ->parameters(['penerimaan' => 'receipt'])
            ->names('receipts');
        Route::resource('penyaluran', ZisDistributionController::class)
            ->parameters(['penyaluran' => 'distribution'])
            ->names('distributions');
        Route::resource('muzakkis', MuzakkiController::class);
        Route::resource('mustahiks', MustahikController::class);
        Route::resource('programs', ZisProgramController::class);
    });

    Route::prefix('wakaf')->name('wakaf.')->group(function () {
        Route::get('/', [WakafController::class, 'index'])->name('index');
        Route::get('/report', [WakafController::class, 'report'])->name('report');
        Route::resource('wakifs', WakifController::class);
        Route::resource('nazhirs', NazhirController::class);
        Route::resource('programs', WakafProgramController::class);
        Route::get('/cash/{wakafCash}/receipt', [WakafCashController::class, 'receipt'])->name('cash.receipt');
        Route::resource('cash', WakafCashController::class)->parameters(['cash' => 'wakafCash']);
        Route::resource('non-cash', WakafNonCashController::class)->parameters(['non-cash' => 'wakafNonCash']);
        Route::resource('assets', WakafAssetController::class);
        Route::resource('productive-assets', WakafProductiveAssetController::class);
        Route::resource('management-results', WakafManagementResultController::class);
        Route::resource('asset-maintenances', WakafAssetMaintenanceController::class);
        Route::resource('documents', WakafDocumentController::class);
    });

    Route::get('/modul/{slug}', [PlaceholderController::class, 'show'])->name('placeholder');
});
