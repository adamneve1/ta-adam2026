@extends('layouts.app')

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Rekapitulasi Penerimaan</h3>
            <p class="text-muted mb-0">Rekap pembayaran invoice PNBP berdasarkan periode tertentu.</p>
        </div>

        <button type="button" class="btn btn-success align-self-start" data-bs-toggle="modal" data-bs-target="#exportPdfModal">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </button>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Periksa kembali input penandatangan.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('rekapitulasi.penerimaan') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4 col-xl-3">
                        <label for="tanggal_mulai" class="form-label small fw-semibold">Tanggal Mulai</label>
                        <input
                            type="date"
                            id="tanggal_mulai"
                            name="tanggal_mulai"
                            class="form-control form-control-sm"
                            value="{{ $filters['tanggal_mulai'] }}"
                        >
                    </div>

                    <div class="col-12 col-md-4 col-xl-3">
                        <label for="tanggal_selesai" class="form-label small fw-semibold">Tanggal Selesai</label>
                        <input
                            type="date"
                            id="tanggal_selesai"
                            name="tanggal_selesai"
                            class="form-control form-control-sm"
                            value="{{ $filters['tanggal_selesai'] }}"
                        >
                    </div>

                    <div class="col-6 col-md-auto">
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            Filter
                        </button>
                    </div>

                    <div class="col-6 col-md-auto">
                        <a href="{{ route('rekapitulasi.penerimaan') }}" class="btn btn-outline-secondary btn-sm w-100">
                            Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Periode</div>
                    <div class="fw-bold text-dark">{{ $filters['tanggal_mulai_label'] }}</div>
                    <div class="small text-muted">sampai {{ $filters['tanggal_selesai_label'] }}</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Jumlah Transaksi</div>
                    <div class="fs-4 fw-bold text-dark">{{ $payments->count() }}</div>
                    <div class="small text-muted">pembayaran tercatat</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small fw-semibold text-uppercase">Total Penerimaan</div>
                    <div class="fs-4 fw-bold text-success">{{ $rupiah($totalPenerimaan) }}</div>
                    <div class="small text-muted">berdasarkan tanggal pembayaran</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Nomor Pembayaran</th>
                            <th class="py-3">Invoice</th>
                            <th class="py-3">Klien</th>
                            <th class="py-3">Kode Billing</th>
                            <th class="py-3">NTPN</th>
                            <th class="py-3 text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $payment)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3">{{ $payment->tanggal_pembayaran->locale('id')->translatedFormat('d M Y') }}</td>
                                <td class="py-3 fw-semibold">{{ $payment->nomor_pembayaran ?? '-' }}</td>
                                <td class="py-3">
                                    <span class="fw-semibold">{{ $payment->invoice->nomor_invoice ?? '-' }}</span>
                                    <small class="text-muted d-block">{{ $payment->invoice->pks->nomor ?? '-' }}</small>
                                </td>
                                <td class="py-3">{{ $payment->invoice->pks->client->nama ?? '-' }}</td>
                                <td class="py-3"><code>{{ $payment->kode_billing ?? '-' }}</code></td>
                                <td class="py-3">{{ $payment->ntpn ?? '-' }}</td>
                                <td class="py-3 text-end fw-bold">{{ $rupiah($payment->jumlah_pembayaran) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    Belum ada penerimaan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="7" class="text-end px-4 py-3">Total Penerimaan</th>
                            <th class="text-end py-3">{{ $rupiah($totalPenerimaan) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="exportPdfModal" tabindex="-1" aria-labelledby="exportPdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('rekapitulasi.penerimaan.export') }}" target="_blank">
                @csrf
                <input type="hidden" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] }}">
                <input type="hidden" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] }}">

                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="exportPdfModalLabel">Data Penandatangan PDF</h5>
                        <small class="text-muted">Periode {{ $filters['tanggal_mulai_label'] }} sampai {{ $filters['tanggal_selesai_label'] }}</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        @for($i = 0; $i < 3; $i++)
                            <div class="col-12 col-lg-4">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <h6 class="fw-bold mb-3">Penandatangan {{ $i + 1 }}</h6>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold" for="signer_{{ $i }}_posisi">Posisi / Jabatan</label>
                                        <input
                                            type="text"
                                            id="signer_{{ $i }}_posisi"
                                            name="signers[{{ $i }}][posisi]"
                                            class="form-control form-control-sm"
                                            value="{{ old('signers.' . $i . '.posisi') }}"
                                            placeholder="Contoh: Mengetahui / Menyetujui"
                                            required
                                        >
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold" for="signer_{{ $i }}_nama">Nama</label>
                                        <input
                                            type="text"
                                            id="signer_{{ $i }}_nama"
                                            name="signers[{{ $i }}][nama]"
                                            class="form-control form-control-sm"
                                            value="{{ old('signers.' . $i . '.nama') }}"
                                            placeholder="Nama lengkap"
                                            required
                                        >
                                    </div>

                                    <div class="mb-0">
                                        <label class="form-label small fw-semibold" for="signer_{{ $i }}_nip">NIP</label>
                                        <input
                                            type="text"
                                            id="signer_{{ $i }}_nip"
                                            name="signers[{{ $i }}][nip]"
                                            class="form-control form-control-sm"
                                            value="{{ old('signers.' . $i . '.nip') }}"
                                            placeholder="NIP"
                                            inputmode="numeric"
                                            required
                                        >
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-printer me-1"></i> Cetak PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
