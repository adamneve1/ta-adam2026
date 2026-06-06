@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">List Pembayaran</h3>
            <p class="text-muted mb-0">Riwayat pembayaran invoice PNBP.</p>
        </div>
        @if(auth()->user()->isPenyetor())
            <a href="{{ route('payment.create') }}" class="btn btn-primary">
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
            <div class="row g-2">
                <div class="col">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control form-control-sm"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nomor pembayaran, invoice, kontrak, klien, billing, NTPN..."
                    >
                </div>

                <div class="col-auto">
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control form-control-sm"
                        value="{{ request('tanggal') }}"
                    >
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        Filter
                    </button>
                </div>

                @if(request()->filled('keyword') || request()->filled('tanggal'))
                    <div class="col-auto">
                        <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Nomor Pembayaran</th>
                            <th class="py-3">Tanggal Bayar</th>
                            <th class="py-3">Invoice</th>
                            <th class="py-3">Kontrak</th>
                            <th class="py-3">Klien</th>
                            <th class="py-3">Kode Billing</th>
                            <th class="py-3">NTPN</th>
                            <th class="py-3">Jumlah Bayar</th>
                            <th class="px-4 py-3 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3 fw-semibold">{{ $pay->nomor_pembayaran ?? '-' }}</td>
                                <td class="py-3">{{ $pay->tanggal_pembayaran->format('d M Y') }}</td>
                                <td class="py-3 fw-semibold">{{ $pay->invoice->nomor_invoice ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="d-block">{{ $pay->invoice->pks->judul ?? '-' }}</span>
                                    <small class="text-muted">{{ $pay->invoice->pks->nomor ?? '-' }}</small>
                                </td>
                                <td class="py-3">{{ $pay->invoice->pks->client->nama ?? '-' }}</td>
                                <td class="py-3">
                                    @if($pay->kode_billing)
                                        <code>{{ $pay->kode_billing }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="py-3">{{ $pay->ntpn ?? '-' }}</td>
                                <td class="py-3 fw-bold">Rp {{ number_format($pay->jumlah_pembayaran, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="{{ route('payment.kwitansi', $pay->id) }}" target="_blank" class="dropdown-item py-2 text-success">
                                                    <i class="bi bi-printer-fill me-2"></i> Cetak Kwitansi
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    {{ request()->filled('keyword') || request()->filled('tanggal') ? 'Tidak ada pembayaran yang cocok dengan filter saat ini.' : 'Belum ada data pembayaran.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
