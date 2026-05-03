<!DOCTYPE html>
<html>
<head>
    <title>PNBP RRI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="bg-dark text-white p-3" style="width: 250px; min-height: 100vh;">
        <h4>PNBP</h4>
        <hr>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/katalog" class="nav-link text-white">Katalog</a>
            </li>
            <li class="nav-item">
                <a href="/pks/create" class="nav-link text-white">Buat PKS</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white">Invoice</a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white">Pembayaran</a>
            </li>
        </ul>
    </div>

    <!-- CONTENT -->
    <div class="p-4 w-100 bg-light">
        @yield('content')
    </div>

</div>

</body>
</html>