@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Daftar Invoice (Tagihan)</h3>
            <p class="text-muted mb-0">Manajemen penagihan invoice PNBP dari PKS yang disepakati.</p>
        </div>
        <a href="{{ route('invoice.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Buat Invoice Tagihan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 50px;">No</th>
                            <th class="py-3">Nomor Invoice</th>
                            <th class="py-3">Kontrak PKS</th>
                            <th class="py-3">Klien</th>
                            <th class="py-3">Nominal Tagihan</th>
                            <th class="py-3">Tanggal Invoice</th>
                            <th class="py-3">Jatuh Tempo</th>
                            <th class="py-3">Kode Billing</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $index => $inv)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3 fw-semibold">{{ $inv->nomor_invoice }}</td>
                                <td class="py-3 text-truncate" style="max-width: 200px;">
                                    <span class="d-block text-gray-800">{{ $inv->pks->judul }}</span>
                                    <small class="text-muted">No. {{ $inv->pks->nomor }}</small>
                                </td>
                                <td class="py-3">{{ $inv->pks->client->nama ?? '-' }}</td>
                                <td class="py-3 fw-bold text-gray-900">Rp {{ number_format($inv->nominal, 0, ',', '.') }}</td>
                                <td class="py-3">{{ $inv->tanggal_invoice->format('d M Y') }}</td>
                                <td class="py-3">{{ $inv->tanggal_jatuh_tempo->format('d M Y') }}</td>
                                <td class="py-3">
                                    @if($inv->kode_billing)
                                        <code class="px-2 py-1 bg-light rounded text-dark">{{ $inv->kode_billing }}</code>
                                    @else
                                        <span class="text-muted small">Belum di-input</span>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    @if($inv->status === 'paid')
                                        <span class="badge bg-success text-white">Sudah Bayar</span>
                                    @else
                                          <span class="badge bg-warning text-dark">Belum Bayar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <!-- Tombol Pemicu Dropdown (Titik 3 Vertikal) -->
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        
                                        <!-- Menu Dropdown -->
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('invoice.show', $inv->id) }}">
                                                    <i class="bi bi-eye-fill text-primary me-2"></i> Detail Tagihan
                                                </a>
                                            </li>
                                            
                                            <!-- Edit (Hanya jika belum lunas) -->
                                            @if($inv->status !== 'paid')
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('invoice.edit', $inv->id) }}">
                                                        <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Invoice
                                                    </a>
                                                </li>
                                            @else
                                                <li>
                                                    <button class="dropdown-item py-2 text-muted" disabled title="Lunas - Tidak Bisa Diedit">
                                                        <i class="bi bi-lock-fill text-muted me-2"></i> <del>Edit (Lunas)</del>
                                                    </button>
                                                </li>
                                            @endif
                                            
                                            <li><hr class="dropdown-divider"></li>
                                            
                                            <!-- Cetak PDF (Digabung kembali tanpa scroll) -->
                                            <li>
                                                <a class="dropdown-item py-2 text-success" href="{{ route('invoice.cetak', $inv->id) }}" target="_blank">
                                                    <i class="bi bi-printer-fill me-2"></i> Cetak PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-receipt fs-1 d-block mb-3 opacity-50"></i>
                                    Belum ada invoice yang dibuat.
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
