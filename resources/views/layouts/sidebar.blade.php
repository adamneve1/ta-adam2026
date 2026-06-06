@php
    $sidebarId = $menuId ?? 'sidebarMenu';
    $linkBase = 'nav-link rounded mb-1 d-flex align-items-center';
    $subLinkBase = 'nav-link small rounded mb-1 ps-3';
    $activeClass = 'active text-primary bg-white fw-semibold';
    $inactiveClass = 'text-white-50';
@endphp

<!-- SIDEBAR -->
<aside class="bg-primary text-white p-3 h-100 shadow-sm" style="width: 260px; min-height: 100vh;">
    <div class="mb-4">
        <img src="{{ asset('images/RRI_Logo.png') }}"
             alt="RRI"
             class="bg-white rounded p-2 mb-3"
             style="width: 180px; height: 58px; object-fit: contain;">

        <div>
            <h5 class="mb-1 fw-semibold lh-sm">Sistem Pengelolaan PNBP RRI Batam</h5>
            <small class="text-white-50">Katalog, kontrak, invoice, dan pembayaran</small>
        </div>
    </div>

    <nav aria-label="Menu utama">
        <ul class="nav flex-column gap-1" id="{{ $sidebarId }}">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}"
                   class="{{ $linkBase }} {{ request()->is('dashboard') ? $activeClass : $inactiveClass }}">
                    <i class="bi bi-speedometer2 me-2"></i>
                    Dashboard
                </a>
            </li>

            @if(auth()->user()->isLpu())
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="{{ $linkBase }} {{ request()->is('users*') ? $activeClass : $inactiveClass }}">
                        <i class="bi bi-people me-2"></i>
                        Manajemen Akun
                    </a>
                </li>

                <li class="nav-item">
                    <a class="{{ $linkBase }} {{ request()->is('katalog*') ? $activeClass : $inactiveClass }}"
                       data-bs-toggle="collapse"
                       href="#{{ $sidebarId }}Katalog"
                       role="button"
                       aria-expanded="{{ request()->is('katalog*') ? 'true' : 'false' }}"
                       aria-controls="{{ $sidebarId }}Katalog">
                        <i class="bi bi-grid me-2"></i>
                        Katalog
                        <i class="bi bi-chevron-down ms-auto small"></i>
                    </a>

                    <div class="collapse {{ request()->is('katalog*') ? 'show' : '' }}"
                         id="{{ $sidebarId }}Katalog"
                         data-bs-parent="#{{ $sidebarId }}">
                        <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                            <li class="nav-item">
                                <a href="/katalog/create"
                                   class="{{ $subLinkBase }} {{ request()->is('katalog/create') ? $activeClass : $inactiveClass }}">
                                    Buat Katalog
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="/katalog"
                                   class="{{ $subLinkBase }} {{ request()->is('katalog') ? $activeClass : $inactiveClass }}">
                                    List Katalog
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item">
                <a class="{{ $linkBase }} {{ request()->is('pks*') ? $activeClass : $inactiveClass }}"
                   data-bs-toggle="collapse"
                   href="#{{ $sidebarId }}Kontrak"
                   role="button"
                   aria-expanded="{{ request()->is('pks*') ? 'true' : 'false' }}"
                   aria-controls="{{ $sidebarId }}Kontrak">
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Kontrak
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ request()->is('pks*') ? 'show' : '' }}"
                     id="{{ $sidebarId }}Kontrak"
                     data-bs-parent="#{{ $sidebarId }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        @if(auth()->user()->isLpu())
                            <li class="nav-item">
                                <a href="/pks/create"
                                   class="{{ $subLinkBase }} {{ request()->is('pks/create') ? $activeClass : $inactiveClass }}">
                                    Buat Kontrak
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="/pks"
                               class="{{ $subLinkBase }} {{ request()->is('pks') ? $activeClass : $inactiveClass }}">
                                List Kontrak
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="{{ $linkBase }} {{ request()->is('invoice*') ? $activeClass : $inactiveClass }}"
                   data-bs-toggle="collapse"
                   href="#{{ $sidebarId }}Invoice"
                   role="button"
                   aria-expanded="{{ request()->is('invoice*') ? 'true' : 'false' }}"
                   aria-controls="{{ $sidebarId }}Invoice">
                    <i class="bi bi-receipt me-2"></i>
                    Invoice
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ request()->is('invoice*') ? 'show' : '' }}"
                     id="{{ $sidebarId }}Invoice"
                     data-bs-parent="#{{ $sidebarId }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        @if(auth()->user()->isPenyetor())
                            <li class="nav-item">
                                <a href="/invoice/create"
                                   class="{{ $subLinkBase }} {{ request()->is('invoice/create') ? $activeClass : $inactiveClass }}">
                                    Buat Invoice
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="/invoice"
                               class="{{ $subLinkBase }} {{ request()->is('invoice') ? $activeClass : $inactiveClass }}">
                                List Invoice
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <li class="nav-item">
                <a class="{{ $linkBase }} {{ request()->is('payment*') ? $activeClass : $inactiveClass }}"
                   data-bs-toggle="collapse"
                   href="#{{ $sidebarId }}Pembayaran"
                   role="button"
                   aria-expanded="{{ request()->is('payment*') ? 'true' : 'false' }}"
                   aria-controls="{{ $sidebarId }}Pembayaran">
                    <i class="bi bi-credit-card me-2"></i>
                    Pembayaran
                    <i class="bi bi-chevron-down ms-auto small"></i>
                </a>

                <div class="collapse {{ request()->is('payment*') ? 'show' : '' }}"
                     id="{{ $sidebarId }}Pembayaran"
                     data-bs-parent="#{{ $sidebarId }}">
                    <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                        @if(auth()->user()->isPenyetor())
                            <li class="nav-item">
                                <a href="{{ route('payment.create') }}"
                                   class="{{ $subLinkBase }} {{ request()->is('payment/create') ? $activeClass : $inactiveClass }}">
                                    Buat Pembayaran
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('payment.index') }}"
                               class="{{ $subLinkBase }} {{ request()->is('payment') ? $activeClass : $inactiveClass }}">
                                List Pembayaran
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>
</aside>
