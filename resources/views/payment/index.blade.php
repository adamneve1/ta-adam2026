@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">List Pembayaran</h3>
            <p class="text-muted mb-0">Riwayat pembayaran invoice PNBP.</p>
        </div>
        @if(auth()->user()->isPenyetor())
            <a href="{{ route('payment.create') }}" class="btn btn-primary align-self-start align-self-md-auto">
                <i class="bi bi-plus-circle me-1"></i> Buat Pembayaran
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
        <form method="GET" action="{{ route('payment.index') }}">
            <div class="row g-2 align-items-stretch">
                <div class="col-12 col-lg">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control form-control-sm"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nomor pembayaran, invoice, kontrak, klien, billing, NTPN..."
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
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                </div>

                @if(request()->filled('keyword') || request()->filled('tanggal'))
                    <div class="col-6 col-md-auto">
                        <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                            Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <style>
        .payment-list-table {
            min-width: 1120px;
        }

        .payment-list-table th {
            font-size: 0.72rem;
            text-transform: uppercase;
            color: #6c757d;
            letter-spacing: 0;
            white-space: nowrap;
            font-weight: 700;
            border-bottom-color: #e9ecef;
        }

        .payment-list-table td {
            vertical-align: top;
            border-bottom-color: #f1f3f5;
        }

        .payment-inline-line {
            max-width: 100%;
            overflow-x: auto;
        }

        .payment-number {
            color: #212529;
            font-size: 0.86rem;
            line-height: 1.25;
            white-space: nowrap;
        }

        .payment-muted-label,
        .payment-meta {
            color: #6c757d;
            font-size: 0.76rem;
            line-height: 1.2;
        }

        .payment-title {
            line-height: 1.25;
            max-width: 320px;
        }

        .payment-code {
            font-size: 0.8rem;
        }

        .payment-action-btn {
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
                <table class="table table-hover align-middle mb-0 payment-list-table">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 20%;">Pembayaran</th>
                            <th class="py-3" style="width: 22%;">Invoice</th>
                            <th class="py-3" style="width: 28%;">PKS / Klien</th>
                            <th class="py-3" style="width: 150px;">Billing</th>
                            <th class="py-3" style="width: 150px;">NTPN</th>
                            <th class="py-3 text-end" style="width: 150px;">Jumlah</th>
                            <th class="px-4 py-3 text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $pay)
                            <tr>
                                <td class="px-4 py-3 pe-4">
                                    <div class="payment-inline-line">
                                        <div class="payment-number fw-semibold d-inline-block">{{ $pay->nomor_pembayaran ?? '-' }}</div>
                                    </div>
                                    <div class="payment-meta mt-2">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $pay->tanggal_pembayaran->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="py-3 pe-4">
                                    <div class="payment-inline-line">
                                        <div class="fw-semibold text-dark text-nowrap d-inline-block">{{ $pay->invoice->nomor_invoice ?? '-' }}</div>
                                    </div>
                                    <div class="payment-muted-label mt-1">Invoice tagihan</div>
                                </td>
                                <td class="py-3 pe-4">
                                    <div class="payment-inline-line">
                                        <div class="fw-semibold text-dark text-nowrap d-inline-block">{{ $pay->invoice->pks->nomor ?? '-' }}</div>
                                    </div>
                                    <div class="payment-title text-gray-900 mt-1">{{ $pay->invoice->pks->judul ?? '-' }}</div>
                                    <div class="payment-meta payment-inline-line mt-2">
                                        <i class="bi bi-building me-1"></i><span class="text-nowrap d-inline-block">{{ $pay->invoice->pks->client->nama ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($pay->kode_billing)
                                        <code class="payment-code d-inline-block px-2 py-1 bg-light rounded text-dark text-nowrap">{{ $pay->kode_billing }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    @if($pay->ntpn)
                                        <code class="payment-code text-dark text-nowrap">{{ $pay->ntpn }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3 text-end">
                                    <div class="fw-bold text-gray-900 text-nowrap">Rp {{ number_format($pay->jumlah_pembayaran, 0, ',', '.') }}</div>
                                    <div class="payment-muted-label mt-1">Dibayar</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-light text-secondary border payment-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi pembayaran">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            @if(auth()->user()->isPenyetor())
                                                <li>
                                                    <a href="{{ route('payment.edit', $pay->id) }}" class="dropdown-item py-2">
                                                        <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Pembayaran
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                            <li>
                                                @if($pay->bukti_pembayaran_path)
                                                    <a href="{{ asset('storage/' . $pay->bukti_pembayaran_path) }}" target="_blank" class="dropdown-item py-2">
                                                        <i class="bi bi-file-earmark-arrow-down text-secondary me-2"></i> Lihat Bukti
                                                    </a>
                                                @else
                                                    <button class="dropdown-item py-2 text-muted" disabled>
                                                        <i class="bi bi-file-earmark-x text-muted me-2"></i> Bukti Tidak Ada
                                                    </button>
                                                @endif
                                            </li>
                                            @if(auth()->user()->isPenyetor())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('payment.kwitansi', $pay->id) }}" target="_blank" class="dropdown-item py-2 text-success">
                                                        <i class="bi bi-printer-fill me-2"></i> Cetak Kwitansi
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
                                    <i class="bi bi-cash-coin fs-1 d-block mb-3 opacity-50"></i>
                                    {{ request()->filled('keyword') || request()->filled('tanggal') ? 'Tidak ada pembayaran yang cocok dengan filter saat ini.' : 'Belum ada data pembayaran.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-md-none">
                @forelse($payments as $pay)
                    <div class="p-3 border-bottom bg-white">
                        <div class="d-flex justify-content-between gap-3">
                            <div style="min-width: 0;">
                                <div class="payment-inline-line">
                                    <div class="payment-number fw-semibold d-inline-block">{{ $pay->nomor_pembayaran ?? '-' }}</div>
                                </div>
                                <div class="payment-meta mt-1">{{ $pay->tanggal_pembayaran->format('d M Y') }}</div>
                                <div class="fw-semibold text-gray-900 mt-3">Rp {{ number_format($pay->jumlah_pembayaran, 0, ',', '.') }}</div>
                            </div>

                            <div class="dropdown flex-shrink-0">
                                <button class="btn btn-light text-secondary border payment-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Aksi pembayaran">
                                    <i class="bi bi-three-dots-vertical fs-5"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                    @if(auth()->user()->isPenyetor())
                                        <li>
                                            <a href="{{ route('payment.edit', $pay->id) }}" class="dropdown-item py-2">
                                                <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Pembayaran
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                    @endif
                                    <li>
                                        @if($pay->bukti_pembayaran_path)
                                            <a href="{{ asset('storage/' . $pay->bukti_pembayaran_path) }}" target="_blank" class="dropdown-item py-2">
                                                <i class="bi bi-file-earmark-arrow-down text-secondary me-2"></i> Lihat Bukti
                                            </a>
                                        @else
                                            <button class="dropdown-item py-2 text-muted" disabled>
                                                <i class="bi bi-file-earmark-x text-muted me-2"></i> Bukti Tidak Ada
                                            </button>
                                        @endif
                                    </li>
                                    @if(auth()->user()->isPenyetor())
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a href="{{ route('payment.kwitansi', $pay->id) }}" target="_blank" class="dropdown-item py-2 text-success">
                                                <i class="bi bi-printer-fill me-2"></i> Cetak Kwitansi
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        <div class="row g-3 small mt-3">
                            <div class="col-12">
                                <div class="payment-muted-label">Invoice</div>
                                <div class="payment-inline-line">
                                    <div class="fw-semibold text-nowrap d-inline-block">{{ $pay->invoice->nomor_invoice ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="payment-muted-label">PKS / Klien</div>
                                <div class="payment-inline-line">
                                    <div class="fw-semibold text-nowrap d-inline-block">{{ $pay->invoice->pks->nomor ?? '-' }}</div>
                                </div>
                                <div class="payment-inline-line mt-1">
                                    <div class="text-muted text-nowrap d-inline-block">{{ $pay->invoice->pks->client->nama ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="payment-muted-label">Billing</div>
                                @if($pay->kode_billing)
                                    <code class="payment-code text-dark text-nowrap">{{ $pay->kode_billing }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <div class="payment-muted-label">NTPN</div>
                                @if($pay->ntpn)
                                    <code class="payment-code text-dark text-nowrap">{{ $pay->ntpn }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-cash-coin fs-1 d-block mb-3 opacity-50"></i>
                        {{ request()->filled('keyword') || request()->filled('tanggal') ? 'Tidak ada pembayaran yang cocok dengan filter saat ini.' : 'Belum ada data pembayaran.' }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if($payments->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $payments->links() }}
        </div>
    @endif
</div>
@endsection
