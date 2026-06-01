<!-- SIDEBAR -->
<aside class="bg-primary text-white p-3 h-100" style="width: 260px; min-height: 100vh;">
    <div class="mb-4">
        <img src="{{ asset('images/RRI_Logo.png') }}" alt="RRI" class="bg-white rounded p-2 mb-3" style="width: 180px; height: 58px; object-fit: contain;">

        <div>
            <h4 class="mb-1">SISTEM PENGELOLAAAN PNBP RRI BATAM</h4>
            <small class="text-white-50">Katalog, kontrak, dan pembayaran</small>
        </div>
    </div>

    <nav>
        <ul class="nav flex-column" id="{{ $menuId ?? 'sidebarMenu' }}">
            <li class="nav-item">
                <a href="#" class="nav-link rounded mb-1 text-white-50">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link rounded mb-1 {{ request()->is('katalog*') ? 'active text-primary bg-white' : 'text-white-50' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $menuId ?? 'sidebarMenu' }}Katalog"
                   role="button">
                    <i class="bi bi-grid me-2"></i>
                    Katalog
                </a>

                <div class="collapse {{ request()->is('katalog*') ? 'show' : '' }}"
                     id="{{ $menuId ?? 'sidebarMenu' }}Katalog"
                     data-bs-parent="#{{ $menuId ?? 'sidebarMenu' }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        <li class="nav-item">
                            <a href="/katalog/create"
                               class="nav-link small rounded mb-1 {{ request()->is('katalog/create') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                Buat Katalog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/katalog"
                               class="nav-link small rounded mb-1 {{ request()->is('katalog') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                List Katalog
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link rounded mb-1 {{ request()->is('pks*') ? 'active text-primary bg-white' : 'text-white-50' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $menuId ?? 'sidebarMenu' }}Kontrak"
                   role="button">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Kontrak
                </a>

                <div class="collapse {{ request()->is('pks*') ? 'show' : '' }}"
                     id="{{ $menuId ?? 'sidebarMenu' }}Kontrak"
                     data-bs-parent="#{{ $menuId ?? 'sidebarMenu' }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        <li class="nav-item">
                            <a href="/pks/create"
                               class="nav-link small rounded mb-1 {{ request()->is('pks/create') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                Buat Kontrak
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/pks"
                               class="nav-link small rounded mb-1 {{ request()->is('pks') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                List Kontrak
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
               <a class="nav-link rounded mb-1 {{ request()->is('invoice*') ? 'active text-primary bg-white' : 'text-white-50' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $menuId ?? 'sidebarMenu' }}Invoice"
                   role="button">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Invoice
                </a>

                <div class="collapse {{ request()->is('invoice*') ? 'show' : '' }}"
                     id="{{ $menuId ?? 'sidebarMenu' }}Invoice"
                     data-bs-parent="#{{ $menuId ?? 'sidebarMenu' }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        <li class="nav-item">
                            <a href="/invoice/create"
                               class="nav-link small rounded mb-1 {{ request()->is('invoice/create') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                Buat Invoice
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/invoice"
                               class="nav-link small rounded mb-1 {{ request()->is('invoice') ? 'active text-primary bg-white' : 'text-white-50' }}">
                                List Invoice
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a href="#" class="nav-link rounded mb-1 text-white-50">
                    <i class="bi bi-credit-card me-2"></i>
                    Pembayaran
                </a>
            </li>
        </ul>
    </nav>
</aside>
