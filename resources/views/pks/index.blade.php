@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Daftar PKS</h3>
            <p class="text-muted mb-0">Manajemen Perjanjian Kerja Sama (PKS).</p>
        </div>
        @if(auth()->user()->isLpu())
            <a href="{{ route('pks.create') }}" class="btn btn-primary align-self-start align-self-md-auto">
                <i class="bi bi-plus-circle me-1"></i> Buat PKS
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <form method="GET" action="{{ route('pks.index') }}">
            <div class="row g-2 align-items-stretch">
                <div class="col-12 col-lg">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control form-control-sm"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nomor PKS, referensi, judul, atau klien..."
                    >
                </div>

                <div class="col-6 col-md-auto">
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control form-control-sm"
                        value="{{ request('tanggal') }}"
                    >
                </div>

                <div class="col-6 col-md-auto">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-auto">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>

                @if(request()->filled('keyword') || request()->filled('tanggal') || request()->filled('status'))
                    <div class="col-6 col-md-auto">
                        <a href="{{ route('pks.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <style>
        .pks-list-table {
            min-width: 1080px;
        }

        .pks-list-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0;
            white-space: nowrap;
            font-weight: 700;
            border-bottom-color: #e9ecef;
        }

        .pks-list-table td {
            vertical-align: top;
            border-bottom-color: #f1f3f5;
        }

        .pks-contract-title {
            line-height: 1.25;
            max-width: 420px;
        }

        .pks-inline-line {
            max-width: 100%;
            overflow-x: auto;
        }

        .pks-meta {
            font-size: 0.78rem;
        }

        .pks-number {
            color: #212529;
            font-size: 0.82rem;
            line-height: 1.2;
            white-space: nowrap;
        }

        .pks-muted-label {
            color: #6c757d;
            font-size: 0.74rem;
            line-height: 1.2;
        }

        .pks-action-btn {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
    </style>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover align-middle mb-0 pks-list-table">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 38%;">Kontrak</th>
                            <th class="py-3" style="width: 22%;">Mitra</th>
                            <th class="py-3" style="width: 130px;">Tanggal</th>
                            <th class="py-3 text-end" style="width: 160px;">Nilai</th>
                            <th class="py-3" style="width: 230px;">Tagihan</th>
                            <th class="px-4 py-3 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pks as $p)
                            @php
                                $invoice = $p->invoices->first();
                                $statusLabel = $invoice ? $invoice->statusLabel() : 'Belum Invoice';
                                $statusClass = $invoice ? $invoice->statusBadgeClass() : 'bg-secondary text-white';
                                $canDelete = !$invoice;
                            @endphp
                            <tr>
                                <td class="px-4 py-3 pe-4">
                                    <div class="pks-inline-line">
                                        <div class="pks-number fw-semibold d-inline-block">{{ $p->nomor }}</div>
                                    </div>
                                    <div class="pks-contract-title fw-semibold text-gray-900 mt-1">
                                        {{ $p->judul }}
                                    </div>
                                    <div class="pks-meta pks-inline-line text-muted mt-2">
                                        <i class="bi bi-hash me-1"></i><span class="text-nowrap d-inline-block">Ref: {{ $p->nomor_referensi ?: '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 pe-4">
                                    <div class="pks-inline-line">
                                        <div class="fw-semibold text-dark text-nowrap d-inline-block">{{ $p->client->nama ?? '-' }}</div>
                                    </div>
                                    <div class="pks-muted-label mt-1">Mitra kerja sama</div>
                                </td>
                                <td class="py-3 text-nowrap">
                                    <div class="fw-semibold text-dark">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                                    <div class="pks-muted-label mt-1">Tanggal PKS</div>
                                </td>
                                <td class="py-3 text-end">
                                    <div class="fw-bold text-gray-900 text-nowrap">Rp {{ number_format($p->total, 0, ',', '.') }}</div>
                                    <div class="pks-muted-label mt-1">Nilai kontrak</div>
                                </td>
                                <td class="py-3">
                                    @if($invoice)
                                        <div class="pks-inline-line">
                                            @if(auth()->user()->isPenyetor() || auth()->user()->isKepsta())
                                                <a href="{{ route('invoice.show', $invoice->id) }}" class="fw-semibold text-decoration-none text-nowrap d-inline-block">
                                                    {{ $invoice->nomor_invoice }}
                                                </a>
                                            @else
                                                <span class="fw-semibold text-nowrap d-inline-block">{{ $invoice->nomor_invoice }}</span>
                                            @endif
                                        </div>
                                        <div class="pks-meta text-muted mt-1">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-muted">Belum ada invoice</div>
                                    @endif
                                    <div class="d-flex flex-wrap gap-1 mt-2">
                                        <span class="badge {{ $statusClass }} text-nowrap">{{ $statusLabel }}</span>
                                        @if($invoice && $invoice->isOverdue())
                                            <span class="badge bg-danger text-white text-nowrap">Lewat Tempo {{ $invoice->overdueDays() }} hari</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <!-- Tombol Pemicu Dropdown (Titik 3 Vertikal) -->
                                        <button class="btn btn-light text-secondary border pks-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        
                                        <!-- Menu Dropdown -->
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            @if(auth()->user()->isLpu())
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('pks.edit', $p->id) }}">
                                                        <i class="bi bi-pencil-fill text-warning me-2"></i> Edit PKS
                                                    </a>
                                                </li>
                                            @endif
                                            @if(auth()->user()->isLpu())
                                                <li>
                                                    <a class="dropdown-item py-2 text-success" href="{{ route('pks.cetak', $p->id) }}" target="_blank">
                                                        <i class="bi bi-printer-fill me-2"></i> Cetak PKS
                                                    </a>
                                                </li>
                                            @endif
                                            @if(auth()->user()->isLpu())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    @if($canDelete)
                                                        <form action="{{ route('pks.destroy', $p->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus PKS ini?')">
                                                                <i class="bi bi-trash-fill me-2"></i> Hapus PKS
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button type="button" class="dropdown-item py-2 text-muted" disabled title="PKS sudah memiliki invoice">
                                                            <i class="bi bi-lock-fill me-2"></i> Tidak Bisa Dihapus
                                                        </button>
                                                    @endif
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-text fs-1 d-block mb-3 opacity-50"></i>
                                    {{ request()->filled('keyword') || request()->filled('tanggal') || request()->filled('status') ? 'Tidak ada PKS yang cocok dengan filter saat ini.' : 'Belum ada PKS yang dibuat.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-md-none">
                @forelse($pks as $p)
                    @php
                        $invoice = $p->invoices->first();
                        $statusLabel = $invoice ? $invoice->statusLabel() : 'Belum Invoice';
                        $statusClass = $invoice ? $invoice->statusBadgeClass() : 'bg-secondary text-white';
                        $canDelete = !$invoice;
                    @endphp
                    <div class="p-3 border-bottom bg-white">
                        <div class="d-flex justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="pks-inline-line">
                                    <div class="pks-number fw-semibold d-inline-block">{{ $p->nomor }}</div>
                                </div>
                                <div class="fw-semibold text-gray-900 mt-1">{{ $p->judul }}</div>
                                <small class="pks-inline-line text-muted d-block mt-2">
                                    <i class="bi bi-building me-1"></i><span class="text-nowrap d-inline-block">{{ $p->client->nama ?? '-' }}</span>
                                </small>
                            </div>

                            <div class="dropdown flex-shrink-0">
                                <button class="btn btn-light text-secondary border pks-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    @if(auth()->user()->isLpu())
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('pks.edit', $p->id) }}">
                                                <i class="bi bi-pencil-fill text-warning me-2"></i> Edit PKS
                                            </a>
                                        </li>
                                    @endif
                                    @if(auth()->user()->isLpu())
                                        <li>
                                            <a class="dropdown-item py-2 text-success" href="{{ route('pks.cetak', $p->id) }}" target="_blank">
                                                <i class="bi bi-printer-fill me-2"></i> Cetak PKS
                                            </a>
                                        </li>
                                    @endif
                                    @if(auth()->user()->isLpu())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            @if($canDelete)
                                                <form action="{{ route('pks.destroy', $p->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus PKS ini?')">
                                                        <i class="bi bi-trash-fill me-2"></i> Hapus PKS
                                                    </button>
                                                </form>
                                            @else
                                                <button type="button" class="dropdown-item py-2 text-muted" disabled title="PKS sudah memiliki invoice">
                                                    <i class="bi bi-lock-fill me-2"></i> Tidak Bisa Dihapus
                                                </button>
                                            @endif
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            @if($invoice && $invoice->isOverdue())
                                <span class="badge bg-danger text-white">Lewat Tempo {{ $invoice->overdueDays() }} hari</span>
                            @endif
                        </div>

                        <div class="row g-3 small mt-3">
                            <div class="col-6">
                                <div class="pks-muted-label">Tanggal</div>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="pks-muted-label">Nilai</div>
                                <div class="fw-semibold text-gray-900">Rp {{ number_format($p->total, 0, ',', '.') }}</div>
                            </div>
                            <div class="col-12">
                                <div class="pks-muted-label">Invoice</div>
                                @if($invoice)
                                    <div class="pks-inline-line">
                                        @if(auth()->user()->isPenyetor() || auth()->user()->isKepsta())
                                            <a href="{{ route('invoice.show', $invoice->id) }}" class="fw-semibold text-decoration-none text-nowrap d-inline-block">
                                                {{ $invoice->nomor_invoice }}
                                            </a>
                                        @else
                                            <span class="fw-semibold text-nowrap d-inline-block">{{ $invoice->nomor_invoice }}</span>
                                        @endif
                                    </div>
                                    <div class="text-muted mt-1">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</div>
                                @else
                                    <span class="text-muted">Belum dibuat</span>
                                @endif
                            </div>
                            <div class="col-12 text-muted pks-inline-line">
                                <i class="bi bi-hash me-1"></i><span class="text-nowrap d-inline-block">Ref: {{ $p->nomor_referensi ?: '-' }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-file-earmark-text fs-1 d-block mb-3 opacity-50"></i>
                        {{ request()->filled('keyword') || request()->filled('tanggal') || request()->filled('status') ? 'Tidak ada PKS yang cocok dengan filter saat ini.' : 'Belum ada PKS yang dibuat.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($pks->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $pks->links() }}
        </div>
    @endif
</div>
@endsection
