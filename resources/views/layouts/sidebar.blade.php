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

<<<<<<< HEAD
            @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('users.index') }}"
                       class="{{ $linkBase }} {{ request()->is('users*') ? $activeClass : $inactiveClass }}">
                        <i class="bi bi-people me-2"></i>
                        Manajemen Akun
                    </a>
                </li>
            @endif

            @if(auth()->user()->isLpu())
                <li class="nav-item">
                    <a href="{{ route('clients.index') }}"
                       class="{{ $linkBase }} {{ request()->is('clients*') ? $activeClass : $inactiveClass }}">
                        <i class="bi bi-person-vcard me-2"></i>
                        Data Client
                    </a>
                </li>
=======
            <li class="nav-item">
                <a class="nav-link rounded mb-1 {{ request()->is('katalog*') ? 'active text-primary bg-white' : 'text-white-50' }}"
                   data-bs-toggle="collapse"
                   href="#{{ $menuId ?? 'sidebarMenu' }}Katalog"
                   role="button">
                    <i class="bi bi-grid me-2"></i>
                    Katalog
                </a>
>>>>>>> 48f4e6dac89782083cf857aa1f340d612807a0f1

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

<<<<<<< HEAD
            @if(auth()->user()->isLpu() || auth()->user()->isKepsta())
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
            @endif

            @if(auth()->user()->isPenyetor() || auth()->user()->isKepsta())
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
            @endif

            @if(auth()->user()->isPenyetor() || auth()->user()->isKepsta())
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
                                <li class="nav-item">
                                    <a href="{{ route('payment.import-simponi') }}"
                                       class="{{ $subLinkBase }} {{ request()->is('payment/import-simponi*') ? $activeClass : $inactiveClass }}">
                                        Import Rekap SIMPONI
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
            @endif

            @if(auth()->user()->isLpu() || auth()->user()->isPenyetor() || auth()->user()->isKepsta())
                <li class="nav-item">
                    <a class="{{ $linkBase }} {{ request()->is('rekapitulasi*') ? $activeClass : $inactiveClass }}"
                       data-bs-toggle="collapse"
                       href="#{{ $sidebarId }}Rekapitulasi"
                       role="button"
                       aria-expanded="{{ request()->is('rekapitulasi*') ? 'true' : 'false' }}"
                       aria-controls="{{ $sidebarId }}Rekapitulasi">
                        <i class="bi bi-clipboard-data me-2"></i>
                        Rekapitulasi
                        <i class="bi bi-chevron-down ms-auto small"></i>
                    </a>

                    <div class="collapse {{ request()->is('rekapitulasi*') ? 'show' : '' }}"
                         id="{{ $sidebarId }}Rekapitulasi"
                         data-bs-parent="#{{ $sidebarId }}">
                        <ul class="nav flex-column ms-3 ps-2 border-start border-light border-opacity-25 mb-2">
                            @if(auth()->user()->isLpu() || auth()->user()->isKepsta())
                                <li class="nav-item">
                                    <a href="{{ route('rekapitulasi.kerja-sama') }}"
                                       class="{{ $subLinkBase }} {{ request()->is('rekapitulasi/kerja-sama') ? $activeClass : $inactiveClass }}">
                                        Kerja Sama PNBP
                                    </a>
                                </li>
                            @endif

                            @if(auth()->user()->isPenyetor() || auth()->user()->isKepsta())
                                <li class="nav-item">
                                    <a href="{{ route('rekapitulasi.penerimaan') }}"
                                       class="{{ $subLinkBase }} {{ request()->is('rekapitulasi/penerimaan') ? $activeClass : $inactiveClass }}">
                                        Penerimaan PNBP
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                </li>
            @endif
=======
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
                               class="nav-link small rounded mb-1 {{ request()->is('Apks') ? 'active text-primary bg-white' : 'text-white-50' }}">
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
>>>>>>> 48f4e6dac89782083cf857aa1f340d612807a0f1
        </ul>
    </nav>
</aside>
