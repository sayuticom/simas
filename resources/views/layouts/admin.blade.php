<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'SIMAS - Sistem Informasi Manajemen Masjid')</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .simas-premium-sidebar {
            background:
                radial-gradient(circle at top left, rgba(212, 175, 55, 0.18), transparent 34%),
                linear-gradient(180deg, #0b2f2a 0%, #0f2e2a 46%, #064e3b 100%);
            color: #f9fafb;
        }
        .simas-premium-sidebar .sidebar-brand {
            border-color: rgba(255, 255, 255, 0.10);
        }
        .simas-premium-sidebar a,
        .simas-premium-sidebar .sidebar-section {
            color: #d1d5db !important;
        }
        .simas-premium-sidebar a:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #f9fafb !important;
        }
        .simas-premium-sidebar .active-mosque-button {
            background: linear-gradient(135deg, #facc15 0%, #eab308 100%) !important;
            color: #0f172a !important;
            border: 1px solid rgba(250, 204, 21, 0.90);
        }
        .simas-premium-sidebar .active-mosque-button i,
        .simas-premium-sidebar .active-mosque-button span {
            color: #0f172a !important;
        }
        .simas-premium-sidebar .active-mosque-button:hover {
            background: linear-gradient(135deg, #fde047 0%, #facc15 100%) !important;
            color: #0f172a !important;
        }
        .simas-premium-sidebar .bg-indigo-600 {
            background: linear-gradient(135deg, #d4af37 0%, #eab308 100%) !important;
            color: #0f172a !important;
            border: 1px solid rgba(250, 204, 21, 0.45);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
        }
        .simas-premium-sidebar .bg-indigo-600 i,
        .simas-premium-sidebar .bg-indigo-600 span {
            color: #0f172a !important;
        }
        .simas-premium-sidebar .bg-indigo-50 {
            background: rgba(250, 204, 21, 0.16) !important;
            color: #f8e7b0 !important;
            border: 1px solid rgba(250, 204, 21, 0.22);
        }
        .simas-premium-sidebar .border-gray-300 {
            border-color: rgba(255, 255, 255, 0.10) !important;
        }
        .simas-premium-sidebar li.pl-6 {
            display: none;
        }
        .simas-premium-sidebar li.pl-6.sidebar-submenu-open {
            display: list-item;
        }
        .simas-premium-sidebar .sidebar-accordion-parent {
            border-left: 4px solid transparent;
            transition: background-color 150ms ease, color 150ms ease, border-color 150ms ease;
        }
        .simas-premium-sidebar .sidebar-accordion-parent:hover {
            background: rgba(255, 255, 255, 0.08) !important;
            color: #f9fafb !important;
        }
        .simas-premium-sidebar .sidebar-parent-active .sidebar-accordion-parent {
            background: rgba(250, 204, 21, 0.16) !important;
            color: #f8e7b0 !important;
            border-left-color: #facc15;
        }
        .simas-premium-sidebar .sidebar-chevron {
            margin-left: auto;
            font-size: 0.75rem;
            opacity: 0.8;
            transition: transform 150ms ease;
        }
        .simas-premium-sidebar .sidebar-parent-open .sidebar-chevron {
            transform: rotate(180deg);
        }
        .simas-premium-sidebar .sidebar-parent-has-submenu > a,
        .simas-premium-sidebar .sidebar-parent-has-submenu > .sidebar-section {
            cursor: pointer;
        }
        .simas-premium-sidebar li.pl-6 a {
            margin-left: 0.25rem;
            border-left: 2px solid rgba(250, 204, 21, 0.18);
        }
        .simas-admin-content {
            background:
                radial-gradient(circle at top right, rgba(20, 83, 45, 0.08), transparent 30%),
                #f8fafc;
        }
        .simas-admin-content h1,
        .simas-admin-content h2,
        .simas-admin-content h3,
        .simas-admin-content h4 {
            color: #0f172a;
        }
        .simas-admin-content .bg-white {
            border: 1px solid rgba(15, 46, 42, 0.08);
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06);
        }
        .simas-admin-content .rounded-lg {
            border-radius: 1rem !important;
        }
        .simas-admin-content .rounded-xl {
            border-radius: 1.125rem !important;
        }
        .simas-admin-content table {
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }
        .simas-admin-content thead,
        .simas-admin-content .bg-gray-50 thead,
        .simas-admin-content thead.bg-gray-50,
        .simas-admin-content thead.bg-slate-50 {
            background: linear-gradient(180deg, #f8fafc 0%, #eef7f1 100%) !important;
        }
        .simas-admin-content th {
            color: #334155 !important;
            font-weight: 800 !important;
            letter-spacing: 0.06em;
        }
        .simas-admin-content tbody tr {
            transition: background-color 0.16s ease, box-shadow 0.16s ease;
        }
        .simas-admin-content tbody tr:hover {
            background: #f7fbf8 !important;
        }
        .simas-admin-content input:not([type="checkbox"]):not([type="radio"]),
        .simas-admin-content select,
        .simas-admin-content textarea {
            border-color: #cbd5e1 !important;
            background-color: #ffffff;
            color: #0f172a;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }
        .simas-admin-content input:focus,
        .simas-admin-content select:focus,
        .simas-admin-content textarea:focus {
            border-color: #0f766e !important;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.14) !important;
            outline: none;
        }
        .simas-admin-content label {
            color: #334155;
        }
        .simas-admin-content .bg-indigo-600,
        .simas-admin-content button.bg-indigo-600,
        .simas-admin-content a.bg-indigo-600 {
            background: linear-gradient(135deg, #0f4f3f 0%, #166534 100%) !important;
            color: #ffffff !important;
            border: 1px solid rgba(212, 175, 55, 0.28);
            box-shadow: 0 10px 22px rgba(15, 79, 63, 0.18);
        }
        .simas-admin-content .hover\:bg-indigo-700:hover,
        .simas-admin-content button.bg-indigo-600:hover,
        .simas-admin-content a.bg-indigo-600:hover {
            background: linear-gradient(135deg, #0b3f34 0%, #14532d 100%) !important;
            color: #ffffff !important;
        }
        .simas-admin-content .text-indigo-600,
        .simas-admin-content .text-indigo-700 {
            color: #0f766e !important;
        }
        .simas-admin-content .hover\:text-indigo-900:hover,
        .simas-admin-content .hover\:text-indigo-800:hover {
            color: #064e3b !important;
        }
        .simas-admin-content .border-indigo-600,
        .simas-admin-content .border-indigo-500 {
            border-color: #0f766e !important;
        }
        .simas-admin-content .bg-indigo-50,
        .simas-admin-content .bg-indigo-100 {
            background-color: #ecfdf5 !important;
            color: #065f46 !important;
            border-color: #a7f3d0 !important;
        }
        .simas-admin-content .bg-gray-50,
        .simas-admin-content .bg-slate-50 {
            background-color: #f8fafc !important;
        }
        .simas-admin-content .shadow,
        .simas-admin-content .shadow-sm {
            box-shadow: 0 14px 36px rgba(15, 23, 42, 0.06) !important;
        }
        .simas-admin-content a:not(.active-mosque-button) {
            text-underline-offset: 3px;
        }
        .simas-admin-content .bg-red-50,
        .simas-admin-content .bg-green-50,
        .simas-admin-content .bg-yellow-50,
        .simas-admin-content .bg-blue-50 {
            box-shadow: none;
        }
        .simas-admin-content .divide-gray-200 > :not([hidden]) ~ :not([hidden]),
        .simas-admin-content .divide-slate-200 > :not([hidden]) ~ :not([hidden]) {
            border-color: #e2e8f0 !important;
        }
        @media (max-width: 768px) {
            .simas-admin-content {
                padding: 1rem !important;
            }
            .simas-admin-content table {
                font-size: 0.875rem;
            }
            .simas-admin-content th,
            .simas-admin-content td {
                padding-left: 0.75rem !important;
                padding-right: 0.75rem !important;
            }
        }
    </style>
</head>
<body class="overflow-x-hidden bg-slate-100">
    <div class="flex min-h-screen">
        <div id="sidebarOverlay" class="pointer-events-none fixed inset-0 z-40 bg-slate-950/60 opacity-0 transition-opacity duration-300 lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="adminSidebar" class="simas-premium-sidebar fixed inset-y-0 left-0 z-50 flex h-screen w-72 -translate-x-full flex-col overflow-y-auto shadow-2xl transition-transform duration-300 ease-out lg:static lg:z-auto lg:h-auto lg:w-64 lg:translate-x-0 lg:flex-shrink-0">
            <div class="sidebar-brand p-6 border-b">
                <div class="flex items-center justify-between gap-3">
                    <h1 class="text-xl font-bold text-amber-200">
                        <i class="fas fa-mosque mr-2"></i>SIMAS
                    </h1>
                    <button type="button" id="closeSidebar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 text-slate-100 hover:bg-white/10 lg:hidden" aria-label="Tutup menu">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- NAMA MASJID DI BAWAH LOGO -->
                @auth
                <p class="text-xs text-slate-300 mt-2">Sistem Informasi Manajemen Masjid</p>
                <div class="mt-2">
                    @if(auth()->user()->activeMosque)
                        <a href="{{ route('profile') }}" title="Kelola Profil Masjid" class="active-mosque-button inline-flex items-center gap-2 text-sm font-bold px-3 py-2 rounded-lg shadow-md transition">
                            <i class="fas fa-building"></i>
                            <span class="truncate max-w-[10rem]">{{ auth()->user()->activeMosque->name }}</span>
                        </a>
                    @elseif(auth()->user()->isSuperSuperuser())
                        <div class="text-xs font-semibold text-indigo-700 bg-indigo-50 p-2 rounded border border-indigo-200">
                            <i class="fas fa-shield-halved mr-1"></i> Super Superuser Mode
                        </div>
                    @elseif(auth()->user()->isSuperuser())
                        <div class="text-xs font-semibold text-amber-700 bg-amber-50 p-2 rounded border border-amber-200">
                            <i class="fas fa-shield-halved mr-1"></i> Superuser Mode
                        </div>
                    @endif
                </div>
                @endauth
            </div>

            <!-- Menu Sidebar -->
            <nav class="mt-4">
                <ul class="space-y-1 px-3">
                    <!-- Dashboard -->
                    <li>
                        <a href="{{ route('dashboard') }}" data-sidebar-link class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-home mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <!-- Profil Masjid link removed: use mosque name in header as profile button -->

                    <!-- Divider -->
                    <li class="py-1">
                        <div class="border-t border-gray-300"></div>
                    </li>

                    @if(auth()->check() && auth()->user()->activeMosque)
                    <!-- Data Jamaah -->
                    <li>
                        <a href="{{ route('jamaah.index') }}" data-sidebar-link class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('jamaah.index', 'jamaah.create', 'jamaah.show', 'jamaah.edit') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-users mr-3"></i>
                            <span>Data Jamaah</span>
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('jamaah.index') }}" data-sidebar-link class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('jamaah.index', 'jamaah.create', 'jamaah.show', 'jamaah.edit') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Daftar Jamaah
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('jamaah.qr') }}" data-sidebar-link class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('jamaah.qr') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            QR Input Jamaah
                        </a>
                    </li>

                    <!-- Keuangan -->
                    <li>
                        <a href="{{ route('keuangan.index') }}" data-sidebar-link class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('keuangan.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-money-bill mr-3"></i>
                            <span>Keuangan</span>
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('keuangan.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('keuangan.index', 'keuangan.transaksi.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Operasional
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('keuangan.mutasi-akun-kas.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('keuangan.mutasi-akun-kas.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Mutasi Kas
                        </a>
                    </li>

                    <!-- ZIS -->
                    <li>
                        <a href="{{ route('zis.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('zis.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-hand-holding-heart mr-3"></i>
                            <span>ZIS</span>
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('zis.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('zis.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Dashboard ZIS
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('zis.receipts.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('zis.receipts.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Penerimaan ZIS
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('zis.distributions.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('zis.distributions.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Penyaluran ZIS
                        </a>
                    </li>
                    @php $activeMosque = auth()->user()?->getActiveMosque(); @endphp
                    @if(auth()->check() && (auth()->user()->isSuperuser() || ($activeMosque && auth()->user()->hasRoleInMosque(\App\Models\Role::ADMIN_MASJID, $activeMosque->id))))
                    <li class="pl-6">
                        <a href="{{ route('zis.categories.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('zis.categories.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Kategori ZIS
                        </a>
                    </li>
                    @endif

                    <li class="pl-6">
                        <a href="{{ route('zis.reports.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('zis.reports.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Laporan ZIS
                        </a>
                    </li>

                    <!-- Wakaf -->
                    <li>
                        <a href="{{ route('wakaf.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('wakaf.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-scroll mr-3"></i>
                            <span>Wakaf</span>
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.index') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Dashboard Wakaf
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.wakifs.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.wakifs.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Wakif
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.nazhirs.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.nazhirs.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Nazhir
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.programs.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.programs.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Program Wakaf
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.cash.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.cash.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Wakaf Tunai
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.non-cash.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.non-cash.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Wakaf Non-Tunai
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.assets.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.assets.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Aset Wakaf
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.productive-assets.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.productive-assets.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Aset Produktif
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.management-results.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.management-results.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Hasil Kelola
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.asset-maintenances.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.asset-maintenances.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Perawatan Aset
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.documents.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.documents.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Dokumen Wakaf
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('wakaf.report') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('wakaf.report') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Laporan Wakaf
                        </a>
                    </li>

                    <!-- Kegiatan -->
                    <li>
                        <a href="{{ route('kegiatan.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('kegiatan.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-calendar-days mr-3"></i>
                            <span>Kegiatan</span>
                        </a>
                    </li>

                    <!-- Jadwal Petugas -->
                    <li>
                        <a href="{{ route('jadwal-petugas.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('jadwal-petugas.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-clock mr-3"></i>
                            <span>Jadwal Petugas</span>
                        </a>
                    </li>

                    <!-- Inventaris -->
                    <li>
                        <a href="{{ route('inventaris.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('inventaris.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-box mr-3"></i>
                            <span>Inventaris</span>
                        </a>
                    </li>

                    <!-- Dokumen -->
                    <li>
                        <a href="{{ route('dokumen.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('dokumen.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-file-invoice mr-3"></i>
                            <span>Dokumen</span>
                        </a>
                    </li>

                    <!-- Pengumuman -->
                    <li>
                        <a href="{{ route('pengumuman.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('pengumuman.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-bullhorn mr-3"></i>
                            <span>Pengumuman</span>
                        </a>
                    </li>

                    <!-- Konten Website -->
                    <li>
                        <a href="{{ route('website-posts.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('website-posts.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-newspaper mr-3"></i>
                            <span>Konten Website</span>
                        </a>
                    </li>

                    <!-- Program Donasi -->
                    <li>
                        <a href="{{ route('donation-programs.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('donation-programs.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-hand-holding-dollar mr-3"></i>
                            <span>Program Donasi</span>
                        </a>
                    </li>

                    <!-- Prompt Desain -->
                    <li>
                        <a href="{{ route('design-requests.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('design-requests.*', 'design-prompt-templates.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-wand-magic-sparkles mr-3"></i>
                            <span>Prompt Desain</span>
                        </a>
                    </li>

                    <!-- Website Masjid -->
                    <li>
                        <a href="{{ route('website-settings.edit') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('website-settings.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-globe mr-3"></i>
                            <span>Website Masjid</span>
                        </a>
                    </li>
                    <li class="pl-6">
                        <a href="{{ route('website-settings.edit') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('website-settings.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                            Pengaturan Website
                        </a>
                    </li>

                    <!-- Laporan -->
                    <li>
                        <a href="{{ route('laporan.index') }}" class="flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('laporan.*') ? 'bg-indigo-600 text-white' : 'text-gray-700 hover:bg-gray-100' }}">
                            <i class="fas fa-chart-bar mr-3"></i>
                            <span>Laporan</span>
                        </a>
                    </li>
                    @endif

                    <!-- Divider -->
                    <li class="py-1">
                        <div class="border-t border-gray-300"></div>
                    </li>

                    <!-- Pengaturan -->
                    @auth
                        @php
                            $hasSelectableMosques = auth()->user()->selectableMosques()->isNotEmpty();
                        @endphp
                        @if(auth()->user()->activeMosque || auth()->user()->isSuperuser())
                        <li>
                            <div class="sidebar-section flex items-center px-3 py-2 rounded-lg {{ request()->routeIs('profile', 'account.password.*', 'users.*', 'user-invitations.*', 'roles.*') ? 'bg-indigo-600 text-white' : 'text-gray-700' }}">
                                <i class="fas fa-cog mr-3"></i>
                                <span>Pengaturan</span>
                            </div>
                        </li>
                        <li class="pl-6">
                            <a href="{{ route('account.password.edit') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('account.password.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                Ubah Password
                            </a>
                        </li>
                        @if(auth()->user()->activeMosque)
                            <li class="pl-6">
                                <a href="{{ route('profile') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('profile') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Profil Masjid
                                </a>
                            </li>

                            <li class="pl-6">
                                <a href="{{ route('keuangan.akun-kas.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('keuangan.akun-kas.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Akun Kas
                                </a>
                            </li>
                        @endif
                        @if(auth()->user()->isSuperuser() || auth()->user()->isMosqueAdmin())
                            <li class="pl-6">
                                <a href="{{ route('users.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('users.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    User &amp; Hak Akses
                                </a>
                            </li>
                        @endif
                        @if(auth()->user()->isSuperuser())
                            <li class="pl-6">
                                <a href="{{ route('user-invitations.index') }}" class="flex items-center px-3 py-1.5 rounded-lg text-sm {{ request()->routeIs('user-invitations.*') ? 'bg-indigo-50 text-indigo-700 font-semibold' : 'text-gray-600 hover:bg-gray-100' }}">
                                    Undangan User
                                </a>
                            </li>
                        @endif
                        @endif
                    @endauth
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex min-w-0 flex-1 flex-col">
            <!-- Top Bar -->
            <header class="bg-white/95 shadow-sm backdrop-blur border-b border-emerald-100">
                <div class="flex items-center justify-between gap-3 px-4 py-3 lg:px-6 lg:py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <button type="button" id="openSidebar" class="inline-flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-950 text-white shadow-sm hover:bg-emerald-900 lg:hidden" aria-label="Buka menu">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-amber-700">SIMAS</p>
                        <h2 class="truncate text-lg font-black text-slate-950 sm:text-xl lg:text-2xl">@yield('page_title', 'Dashboard')</h2>
                        @auth
                            @if(auth()->user()->activeMosque)
                                <p class="truncate text-xs font-semibold text-slate-500 lg:hidden">{{ auth()->user()->activeMosque->name }}</p>
                            @endif
                        @endauth
                        </div>
                    </div>

                    <!-- BAGIAN KANAN: USER & LOGOUT -->
                    @auth
                        <div class="flex min-w-0 flex-shrink-0 items-center gap-2 sm:gap-4">
                            <div class="max-w-[8rem] truncate text-right sm:max-w-none sm:border-r sm:pr-4">
                                <div class="truncate text-sm font-bold text-gray-800">{{ auth()->user()->name }}</div>
                                <div class="hidden text-xs text-gray-500 capitalize sm:block">
                                    {{ auth()->user()->getRolesInMosque(auth()->user()->active_mosque_id)->first()?->label ?? 'User' }}
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex h-10 w-10 items-center justify-center rounded-xl text-sm text-red-600 transition-colors hover:bg-red-50 hover:text-red-800 sm:h-auto sm:w-auto sm:rounded-none sm:hover:bg-transparent">
                                    <i class="fas fa-sign-out-alt sm:mr-1"></i> <span class="hidden sm:inline">Logout</span>
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Page Content -->
            <div class="simas-admin-content min-w-0 flex-1 overflow-auto p-4 lg:p-6">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="bg-white border-t mt-6">
                <div class="px-6 py-4 text-center text-gray-600 text-sm">
                    <p>&copy; 2024 SIMAS - Sistem Informasi Manajemen Masjid. All rights reserved.</p>
                </div>
            </footer>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const openButton = document.getElementById('openSidebar');
            const closeButton = document.getElementById('closeSidebar');

            if (!sidebar || !overlay) {
                return;
            }

            const openSidebar = () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('pointer-events-none', 'opacity-0');
                overlay.classList.add('opacity-100');
                document.body.classList.add('overflow-hidden');
            };

            const closeSidebar = () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('pointer-events-none', 'opacity-0');
                overlay.classList.remove('opacity-100');
                document.body.classList.remove('overflow-hidden');
            };

            openButton?.addEventListener('click', openSidebar);
            closeButton?.addEventListener('click', closeSidebar);
            overlay.addEventListener('click', closeSidebar);

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    document.body.classList.remove('overflow-hidden');
                    overlay.classList.add('pointer-events-none', 'opacity-0');
                    overlay.classList.remove('opacity-100');
                }
            });

            const navList = sidebar.querySelector('nav ul');
            if (!navList) {
                return;
            }

            const childItems = Array.from(navList.children);
            const groups = [];
            let activeParent = null;

            childItems.forEach((item) => {
                if (item.classList.contains('pl-6')) {
                    if (activeParent) {
                        activeParent.items.push(item);
                    }
                    return;
                }

                if (activeParent && activeParent.items.length > 0) {
                    groups.push(activeParent);
                }

                const hasMenuLink = item.querySelector('a, .sidebar-section');
                const isDivider = item.querySelector('.border-t');
                activeParent = hasMenuLink && !isDivider ? { parent: item, items: [] } : null;
            });

            if (activeParent && activeParent.items.length > 0) {
                groups.push(activeParent);
            }

            const openGroup = (group) => {
                group.items.forEach((item) => item.classList.add('sidebar-submenu-open'));
                group.parent.classList.add('sidebar-parent-open');
            };

            const closeGroup = (group) => {
                if (group.active) {
                    return;
                }

                group.items.forEach((item) => item.classList.remove('sidebar-submenu-open'));
                group.parent.classList.remove('sidebar-parent-open');
            };

            const toggleGroup = (group) => {
                const isOpen = group.items.every((item) => item.classList.contains('sidebar-submenu-open'));

                if (isOpen && !group.active) {
                    closeGroup(group);
                    return;
                }

                openGroup(group);
            };

            const markParentActive = (group) => {
                const parentControl = group.parent.querySelector('a, .sidebar-section');
                if (!parentControl) {
                    return;
                }

                parentControl.classList.add('bg-indigo-600', 'text-white');
                parentControl.classList.remove('text-gray-700');
                group.parent.classList.add('sidebar-parent-active');
            };

            groups.forEach((group) => {
                group.parent.classList.add('sidebar-parent-has-submenu');
                const parentControl = group.parent.querySelector('a, .sidebar-section');
                parentControl?.classList.add('sidebar-accordion-parent');
                parentControl?.setAttribute('aria-expanded', 'false');
                parentControl?.setAttribute('role', 'button');

                if (parentControl && !parentControl.querySelector('.sidebar-chevron')) {
                    const chevron = document.createElement('i');
                    chevron.className = 'fas fa-chevron-down sidebar-chevron';
                    chevron.setAttribute('aria-hidden', 'true');
                    parentControl.appendChild(chevron);
                }

                group.active = Boolean(
                    group.parent.querySelector('.bg-indigo-600') ||
                    group.items.some((item) => item.querySelector('.bg-indigo-50, .bg-indigo-600'))
                );

                if (group.active) {
                    openGroup(group);
                    markParentActive(group);
                    parentControl?.setAttribute('aria-expanded', 'true');
                }

                parentControl?.addEventListener('click', (event) => {
                    event.preventDefault();
                    toggleGroup(group);
                    const isOpen = group.items.every((item) => item.classList.contains('sidebar-submenu-open'));
                    parentControl.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                });
            });

            sidebar.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    if (window.innerWidth < 1024 && !link.closest('.sidebar-parent-has-submenu')) {
                        closeSidebar();
                    }
                });
            });
        });
    </script>
</body>
</html>

