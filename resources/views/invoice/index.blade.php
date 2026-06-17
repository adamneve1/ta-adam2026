@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Daftar Invoice (Tagihan)</h3>
            <p class="text-muted mb-0">Manajemen penagihan invoice PNBP dari PKS yang disepakati.</p>
        </div>
        @if(auth()->user()->isPenyetor())
            <a href="{{ route('invoice.create') }}" class="btn btn-primary align-self-start align-self-md-auto">
                <i class="bi bi-plus-circle me-1"></i> Buat Invoice Tagihan
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="mb-4">
        <form method="GET" action="{{ route('invoice.index') }}">
            <div class="row g-2 align-items-stretch">
                <div class="col-12 col-lg">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control form-control-sm"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nomor invoice, PKS, judul, atau klien..."
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
                        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <style>
        .invoice-list-table {
            min-width: 1040px;
        }

        .invoice-list-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0;
            white-space: nowrap;
            font-weight: 700;
            border-bottom-color: #e9ecef;
        }

        .invoice-list-table td {
            vertical-align: top;
            border-bottom-color: #f1f3f5;
        }

        .invoice-number {
            color: #212529;
            font-size: 0.86rem;
            line-height: 1.25;
            white-space: nowrap;
        }

        .invoice-inline-line,
        .invoice-number-line {
            max-width: 100%;
            overflow-x: auto;
        }

        .invoice-muted-label,
        .invoice-meta {
            color: #6c757d;
            font-size: 0.76rem;
            line-height: 1.2;
        }

        .invoice-title {
            line-height: 1.25;
            max-width: 320px;
        }

        .invoice-billing-code {
            font-size: 0.8rem;
        }

        .invoice-action-btn {
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
                <table class="table table-hover align-middle mb-0 invoice-list-table">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 22%;">Invoice</th>
                            <th class="py-3" style="width: 30%;">PKS / Klien</th>
                            <th class="py-3 text-end" style="width: 150px;">Nominal</th>
                            <th class="py-3" style="width: 130px;">Tempo</th>
                            <th class="py-3" style="width: 170px;">Billing</th>
                            <th class="py-3 text-center" style="width: 180px;">Status</th>
                            <th class="px-4 py-3 text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $inv)
                            <tr>
                                <td class="px-4 py-3 pe-4">
                                    <div class="invoice-number-line">
                                        <a href="{{ route('invoice.show', $inv->id) }}" class="invoice-number fw-semibold text-decoration-none d-inline-block">
                                            {{ $inv->nomor_invoice }}
                                        </a>
                                    </div>
                                    <div class="invoice-meta mt-2">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $inv->tanggal_invoice->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-3 pe-4">
                                    <div class="invoice-inline-line">
                                        <div class="fw-semibold text-dark text-nowrap d-inline-block">{{ $inv->pks->nomor }}</div>
                                    </div>
                                    <div class="invoice-title text-gray-900 mt-1">{{ $inv->pks->judul }}</div>
                                    <div class="invoice-meta invoice-inline-line mt-2">
                                        <i class="bi bi-building me-1"></i><span class="text-nowrap d-inline-block">{{ $inv->pks->client->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-end">
                                    <div class="fw-bold text-gray-900 text-nowrap">Rp {{ number_format($inv->nominal, 0, ',', '.') }}</div>
                                    <div class="invoice-muted-label mt-1">Tagihan</div>
                                </td>
                                <td class="py-3 text-nowrap">
                                    <div class="fw-semibold text-dark">{{ $inv->tanggal_jatuh_tempo->format('d M Y') }}</div>
                                    <div class="invoice-muted-label mt-1">Jatuh tempo</div>
                                </td>
                                <td class="py-3">
                                    @if($inv->kode_billing)
                                        <code class="invoice-billing-code d-inline-block px-2 py-1 bg-light rounded text-dark text-nowrap">{{ $inv->kode_billing }}</code>
                                    @else
                                        <span class="text-muted small">Belum dibuat</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <div class="d-flex flex-wrap gap-1 justify-content-center">
                                        <span class="badge {{ $inv->statusBadgeClass() }}">{{ $inv->statusLabel() }}</span>
                                        @if($inv->isOverdue())
                                            <span class="badge bg-danger text-white">Lewat Tempo {{ $inv->overdueDays() }} hari</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-light text-secondary border invoice-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi invoice">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('invoice.show', $inv->id) }}">
                                                    <i class="bi bi-eye-fill text-primary me-2"></i> Detail Tagihan
                                                </a>
                                            </li>

                                            @if(auth()->user()->isPenyetor() && $inv->isUnpaid())
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('invoice.edit', $inv->id) }}">
                                                        <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Invoice
                                                    </a>
                                                </li>
                                            @endif

                                            @if(auth()->user()->isPenyetor())
                                                <li><hr class="dropdown-divider"></li>

                                                <li>
                                                    <a class="dropdown-item py-2 text-success" href="{{ route('invoice.cetak', $inv->id) }}" target="_blank">
                                                        <i class="bi bi-printer-fill me-2"></i> Cetak PDF
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-3 opacity-50"></i>
                                    {{ request()->filled('keyword') || request()->filled('tanggal') || request()->filled('status') ? 'Tidak ada invoice yang cocok dengan filter saat ini.' : 'Belum ada invoice yang dibuat.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-md-none">
                @forelse($invoices as $inv)
                    <div class="p-3 border-bottom bg-white">
                        <div class="d-flex justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="invoice-number-line">
                                    <a href="{{ route('invoice.show', $inv->id) }}" class="invoice-number fw-semibold text-decoration-none d-inline-block">
                                        {{ $inv->nomor_invoice }}
                                    </a>
                                </div>
                                <div class="invoice-meta mt-1">{{ $inv->tanggal_invoice->format('d M Y') }}</div>
                                <div class="fw-semibold text-gray-900 mt-3">Rp {{ number_format($inv->nominal, 0, ',', '.') }}</div>
                            </div>

                            <div class="dropdown flex-shrink-0">
                                <button class="btn btn-light text-secondary border invoice-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi invoice">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('invoice.show', $inv->id) }}">
                                            <i class="bi bi-eye-fill text-primary me-2"></i> Detail Tagihan
                                        </a>
                                    </li>

                                    @if(auth()->user()->isPenyetor() && $inv->isUnpaid())
                                        <li>
                                            <a class="dropdown-item py-2" href="{{ route('invoice.edit', $inv->id) }}">
                                                <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Invoice
                                            </a>
                                        </li>
                                    @endif

                                    @if(auth()->user()->isPenyetor())
                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <a class="dropdown-item py-2 text-success" href="{{ route('invoice.cetak', $inv->id) }}" target="_blank">
                                                <i class="bi bi-printer-fill me-2"></i> Cetak PDF
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                            <span class="badge {{ $inv->statusBadgeClass() }}">{{ $inv->statusLabel() }}</span>
                            @if($inv->isOverdue())
                                <span class="badge bg-danger text-white">Lewat Tempo {{ $inv->overdueDays() }} hari</span>
                            @endif
                        </div>

                        <div class="row g-3 small mt-3">
                            <div class="col-12">
                                <div class="invoice-muted-label">PKS / Klien</div>
                                <div class="invoice-inline-line">
                                    <div class="fw-semibold text-nowrap d-inline-block">{{ $inv->pks->nomor }}</div>
                                </div>
                                <div class="invoice-inline-line mt-1">
                                    <div class="text-muted text-nowrap d-inline-block">{{ $inv->pks->client->nama ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="invoice-muted-label">Jatuh Tempo</div>
                                <div class="fw-semibold">{{ $inv->tanggal_jatuh_tempo->format('d M Y') }}</div>
                            </div>
                            <div class="col-6 text-end">
                                <div class="invoice-muted-label">Billing</div>
                                @if($inv->kode_billing)
                                    <code class="invoice-billing-code text-dark text-nowrap">{{ $inv->kode_billing }}</code>
                                @else
                                    <span class="text-muted">Belum dibuat</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt fs-1 d-block mb-3 opacity-50"></i>
                        {{ request()->filled('keyword') || request()->filled('tanggal') || request()->filled('status') ? 'Tidak ada invoice yang cocok dengan filter saat ini.' : 'Belum ada invoice yang dibuat.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($invoices->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $invoices->links() }}
        </div>
    @endif
</div>
@endsection
