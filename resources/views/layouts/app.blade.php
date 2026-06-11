<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAS - {{ $title ?? 'Dashboard' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; width: 260px; background: #212529; color: white; position: fixed; }
        .main-content { margin-left: 260px; padding: 20px; }
        .brand-wrapper { padding: 1.5rem; border-bottom: 1px solid #343a40; }
        .nav-link { color: #adb5bd; padding: 0.8rem 1.5rem; }
        .nav-link:hover, .nav-link.active { color: white; background: #343a40; }
        .navbar { background: white; border-bottom: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="brand-wrapper text-center">
                <h3 class="fw-bold mb-0">SIMAS</h3>
                
                <!-- NAMA MASJID DI BAWAH LOGO -->
                @auth
                    <div class="mt-2">
                        @if(auth()->user()->activeMosque)
                            <span class="badge bg-success small text-wrap w-100">
                                <i class="bi bi-building"></i> {{ auth()->user()->activeMosque->name }}
                            </span>
                        @elseif(auth()->user()->isSuperSuperuser())
                            <span class="badge bg-primary small">
                                <i class="bi bi-shield-lock"></i> Super Superuser Mode
                            </span>
                        @elseif(auth()->user()->isSuperuser())
                            <span class="badge bg-warning text-dark small">
                                <i class="bi bi-shield-lock"></i> Superuser Mode
                            </span>
                        @endif
                    </div>
                @endauth
            </div>
            
            <nav class="nav flex-column mt-3">
                <a class="nav-link active" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link" href="#"><i class="bi bi-people me-2"></i> Data Jamaah</a>
                <a class="nav-link" href="#"><i class="bi bi-cash-stack me-2"></i> Keuangan</a>
                <!-- Tambahkan menu lainnya di sini -->
            </nav>
        </div>

        <!-- Bagian Utama -->
        <div class="main-content flex-grow-1">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg sticky-top mb-4 px-3">
                <div class="container-fluid">
                    <div class="ms-auto d-flex align-items-center">
                        @auth
                            <!-- NAMA USER DI SEBELAH KANAN -->
                            <div class="me-3 text-end">
                                <span class="d-block fw-bold text-dark">{{ auth()->user()->name }}</span>
                                <small class="text-muted text-capitalize">
                                    {{ auth()->user()->getRolesInMosque(auth()->user()->active_mosque_id)->first()?->label ?? 'User' }}
                                </small>
                            </div>

                            <!-- TOMBOL LOGOUT -->
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            </nav>

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
