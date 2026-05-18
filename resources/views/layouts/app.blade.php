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
        <header class="bg-white border-bottom px-3 px-md-4 py-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                    <i class="bi bi-list"></i>
                </button>

                <button class="btn btn-outline-dark d-none d-md-inline-flex" type="button" data-bs-toggle="collapse" data-bs-target="#desktopSidebarWrapper">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h5 class="mb-0">Sistem PNBP RRI</h5>
                    <small class="text-muted">Pengelolaan katalog, kontrak, dan pembayaran</small>
                </div>
            </div>

            <div class="d-none d-sm-flex align-items-center gap-2 text-muted small">
                <i class="bi bi-calendar3"></i>
                <span>{{ now()->translatedFormat('d F Y') }}</span>
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
