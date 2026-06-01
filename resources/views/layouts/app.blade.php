<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PNBP RRI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-rri.png') }}">

    <style>
        body {
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    letter-spacing: 0;
    -webkit-font-smoothing: antialiased;
    text-rendering: optimizeLegibility;
}

h1, h2, h3, h4, h5, h6 {
    font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-weight: 600;
}

    </style>

</head>
<body class="bg-light">
<div class="d-flex min-vh-100">
    <div class="d-none d-md-block flex-shrink-0 position-sticky top-0 align-self-start" style="height: 100vh;">
        <div id="desktopSidebarWrapper" class="collapse collapse-horizontal show">
            @include('layouts.sidebar', ['menuId' => 'desktopSidebar'])
        </div>
    </div>


    <div class="flex-grow-1 min-vh-100 d-flex flex-column">
        <header class="bg-white border-bottom px-3 px-md-4 py-2 d-flex align-items-center justify-content-between sticky-top shadow-sm" style="z-index: 1020;">
            <!-- Sisi Kiri: Toggle Sidebar & Identitas Aplikasi (Bootstrap 5 Murni) -->
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-md-none rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <button class="btn btn-light d-none d-md-inline-flex rounded-circle" type="button" data-bs-toggle="collapse" data-bs-target="#desktopSidebarWrapper" style="width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <div class="vr text-muted opacity-25 d-none d-sm-block" style="height: 28px;"></div>

                <div>
                    <h5 class="mb-0 fw-bold text-dark">Sistem PNBP RRI</h5>
                    <small class="text-muted d-none d-sm-inline-block">Pengelolaan katalog PNBP, kontrak kerja sama (PKS), & tagihan invoice</small>
                </div>
            </div>

            <!-- Sisi Kanan: Informasi Tanggal & Dropdown Admin (Bootstrap 5 Murni) -->
            <div class="d-flex align-items-center gap-3">
                <!-- Kapsul Kalender -->
                <div class="d-none d-md-flex align-items-center gap-2 px-3 py-2 bg-light border border-light-subtle rounded-pill small text-secondary">
                    <i class="bi bi-calendar3 text-primary"></i>
                    <span class="fw-medium">{{ now()->translatedFormat('d F Y') }}</span>
                </div>

                <!-- Separator Vertikal -->
                <div class="vr text-muted opacity-25 d-none d-md-block" style="height: 24px;"></div>

                <!-- Dropdown Profil Administrator -->
                @auth
                    <div class="dropdown">
                        <button class="btn btn-link text-decoration-none dropdown-toggle p-0 d-flex align-items-center gap-2 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <!-- Avatar Inisial Bulat (Warna Solid Primary Bootstrap) -->
                            <div class="d-flex align-items-center justify-content-center bg-primary text-white rounded-circle fw-bold shadow-sm" style="width: 36px; height: 36px; font-size: 14px;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="text-start d-none d-sm-block me-1">
                                <span class="d-block text-dark fw-bold small" style="line-height: 1.2;">{{ Auth::user()->name }}</span>
                                <small class="text-muted d-block" style="font-size: 10px; line-height: 1;">Administrator</small>
                            </div>
                        </button>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow border border-light-subtle rounded-3 py-2 mt-2" style="min-width: 220px;">
                            <li class="px-3 py-2 border-bottom mb-2 bg-light">
                                <span class="text-muted d-block" style="font-size: 10px;">Logged in as:</span>
                                <strong class="text-dark d-block text-truncate small">{{ Auth::user()->email }}</strong>
                            </li>
                            <li>
                                <a class="dropdown-item py-2 d-flex align-items-center gap-2 small text-secondary-emphasis" href="{{ route('profile.edit') }}">
                                    <i class="bi bi-person-gear text-primary fs-5"></i> Pengaturan Profil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider my-1"></li>
                            <li>
                                <!-- Form Keluar Terintegrasi Laravel Breeze -->
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-header">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 d-flex align-items-center gap-2 small text-danger" onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                        <i class="bi bi-box-arrow-right fs-5 text-danger"></i> Keluar Aplikasi
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endauth
            </div>
        </header>

        <main class="p-3 p-md-4 bg-light flex-grow-1">
            @yield('content')
        </main>

        <footer class="bg-white border-top px-3 px-md-4 py-3 text-muted small">
            &copy; {{ date('Y') }} PNBP RRI
        </footer>
    </div>
</div>

<div class="offcanvas offcanvas-start bg-dark text-white p-0" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-body p-0">
        @include('layouts.sidebar', ['menuId' => 'mobileSidebarMenu'])
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
