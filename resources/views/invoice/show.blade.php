@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar
            </a>
            @if($invoice->status !== 'paid')
                <a href="{{ route('invoice.edit', $invoice->id) }}" class="btn btn-outline-primary btn-sm mb-2 ms-1">
                    <i class="bi bi-pencil-square"></i> Edit Invoice
                </a>
            @endif
              <a href="{{ route('invoice.cetak', $invoice->id) }}" target="_blank" class="btn btn-success btn-sm mb-2 ms-1">
                <i class="bi bi-printer"></i> Cetak Invoice
            </a>
            <h3 class="text-gray-800 mb-0">Detail Invoice: {{ $invoice->nomor_invoice }}</h3>
        </div>
        <div>
            @if($invoice->status === 'paid')
                <span class="badge bg-success fs-6 px-3 py-2">Lunas (Paid)</span>
            @else
                <span class="badge bg-warning text-dark fs-6 px-3 py-2">Belum Bayar (Unpaid)</span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Rincian Tagihan & Kontrak -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                    <h5 class="m-0 text-primary fw-semibold">
                        <i class="bi bi-receipt me-2"></i>Informasi Tagihan Invoice
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <span class="text-muted small d-block">Nomor Invoice:</span>
                            <span class="fs-5 fw-bold text-gray-900">{{ $invoice->nomor_invoice }}</span>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Nominal Tagihan:</span>
                            <span class="fs-4 fw-extrabold text-primary">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="row border-top pt-3 mb-3">
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted small d-block">Tanggal Invoice:</span>
                            <strong class="text-gray-800">{{ $invoice->tanggal_invoice->format('d F Y') }}</strong>
                        </div>
                        <div class="col-sm-6 mb-3">
                            <span class="text-muted small d-block">Tanggal Jatuh Tempo:</span>
                            <strong class="text-gray-800">{{ $invoice->tanggal_jatuh_tempo->format('d F Y') }}</strong>
                        </div>
                    </div>

                    <!-- Informasi Kontrak PKS Terkait -->
                    <div class="p-3 bg-light rounded border mb-2">
                        <h6 class="text-dark fw-bold mb-2">Kontrak PKS Referensi:</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <span class="text-muted small d-block">Nomor Kontrak PKS:</span>
                                <strong>{{ $invoice->pks->nomor }}</strong>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted small d-block">Judul Perjanjian:</span>
                                <strong>{{ $invoice->pks->judul }}</strong>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Tanggal Kontrak:</span>
                                <span>{{ \Carbon\Carbon::parse($invoice->pks->tanggal)->format('d M Y') }}</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small d-block">Total Nilai PKS:</span>
                                <strong>Rp {{ number_format($invoice->pks->total, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Klien / Mitra -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 text-dark fw-semibold">
                        <i class="bi bi-person-circle me-2"></i>Informasi Mitra / Klien
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Nama Perusahaan/Klien:</span>
                            <strong class="fs-6">{{ $invoice->pks->client->nama ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Penanggung Jawab:</span>
                            <strong>{{ $invoice->pks->client->nama_penanggung_jawab ?? '-' }}</strong>
                            <small class="text-muted d-block">{{ $invoice->pks->client->jabatan ?? '-' }}</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Narahubung (Kontak):</span>
                            <span>{{ $invoice->pks->client->nama_narahubung ?? '-' }} ({{ $invoice->pks->client->no_narahubung ?? '-' }})</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <span class="text-muted small d-block">Email:</span>
                            <span>{{ $invoice->pks->client->email ?? '-' }}</span>
                        </div>
                        <div class="col-12">
                            <span class="text-muted small d-block">Alamat Lengkap:</span>
                            <p class="mb-0 text-gray-800">{{ $invoice->pks->client->alamat ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SIMPONI Billing & Panel Pembayaran (Sisi Kanan) -->
        <div class="col-lg-4">
            <!-- Kode Billing SIMPONI -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 text-dark fw-semibold">
                        <i class="bi bi-file-earmark-code me-2"></i>Billing SIMPONI
                    </h5>
                </div>
                <div class="card-body">
                    @if($invoice->kode_billing)
                        <div class="text-center py-3 bg-light rounded border border-dashed mb-3">
                            <span class="text-muted small d-block mb-1">KODE BILLING AKTIF</span>
                            <span class="fs-4 fw-bold font-monospace tracking-wide text-dark">{{ $invoice->kode_billing }}</span>
                        </div>
                        
                        @if($invoice->status === 'unpaid')
                            <!-- Tombol Ubah Kode Billing -->
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2" data-bs-toggle="collapse" data-bs-target="#editBillingForm">
                                <i class="bi bi-pencil-square me-1"></i> Edit Kode Billing
                            </button>
                        @endif
                    @else
                        <div class="alert alert-info py-2 small mb-3">
                            <i class="bi bi-info-circle me-1"></i> Kode Billing Simponi belum di-generate atau dimasukkan ke dalam tagihan ini.
                        </div>
                    @endif

                    <!-- Form Input / Edit Kode Billing -->
                    <div class="collapse {{ !$invoice->kode_billing ? 'show' : '' }}" id="editBillingForm">
                        <form action="{{ route('invoice.updateBilling', $invoice->id) }}" method="POST" class="border p-3 rounded bg-light">
                            @csrf
                            @method('PATCH')
                            <div class="mb-3">
                                <label for="kode_billing_input" class="form-label small fw-semibold">Input Kode Billing SIMPONI</label>
                                <input type="text" 
                                       name="kode_billing" 
                                       id="kode_billing_input" 
                                       class="form-control form-control-sm font-monospace" 
                                       value="{{ $invoice->kode_billing }}" 
                                       placeholder="15 Digit Kode Billing" 
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bi bi-save me-1"></i> Simpan Kode Billing
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Alur Pembayaran NTPN -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="m-0 text-dark fw-semibold">
                        <i class="bi bi-credit-card me-2"></i>Status Pembayaran PNBP
                    </h5>
                </div>
                <div class="card-body">
                    @if($invoice->status === 'paid')
                        <!-- Tampilan Jika Sudah Lunas -->
                        <div class="text-center py-3 bg-success-subtle text-success border border-success rounded mb-3">
                            <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                            <span class="fw-bold d-block">TAGIHAN SUDAH LUNAS</span>
                            <small class="text-muted d-block mt-1">Pembayaran telah terverifikasi NTPN</small>
                        </div>
                        
                        <!-- Bukti NTPN dan Kwitansi Placeholder -->
                        <div class="p-3 bg-light rounded border mb-2">
                            <span class="text-muted small d-block">Bukti Pembayaran (NTPN):</span>
                            <strong class="fs-6 text-dark font-monospace" id="ntpn_display">Telah Dilunasi</strong>
                            
                            <hr class="my-2">
                            
                            <a href="#" class="btn btn-success btn-sm w-100">
                                <i class="bi bi-printer me-1"></i> Cetak Kwitansi Resmi
                            </a>
                        </div>
                    @else
                        <!-- Tampilan Jika Belum Lunas -->
                        <div class="text-center py-3 bg-warning-subtle text-warning-emphasis border border-warning rounded mb-3">
                            <i class="bi bi-hourglass-split fs-1 d-block mb-2 animate-pulse"></i>
                            <span class="fw-bold d-block">MENUNGGU PEMBAYARAN</span>
                            <small class="text-muted d-block mt-1">Minta mitra membayar via SIMPONI</small>
                        </div>

                        @if($invoice->kode_billing)
                            <div class="p-3 bg-light rounded border border-primary-subtle text-center">
                                <span class="d-block small text-muted mb-2">Apakah klien sudah menyetorkan pembayaran dan memberikan NTPN?</span>
                                
                                <!-- Placeholder tombol ke form pencatatan payment yang akan dikerjakan di tahap berikutnya -->
                                <button class="btn btn-primary btn-sm w-100 disabled" title="Modul Pembayaran Akan Segera Hadir">
                                    <i class="bi bi-plus-circle me-1"></i> Entri NTPN Pembayaran
                                </button>
                                <small class="text-muted mt-2 d-block xs-text">Modul Entri Pembayaran sedang dipersiapkan.</small>
                            </div>
                        @else
                            <div class="alert alert-secondary py-2 small mb-0 text-center">
                                <i class="bi bi-exclamation-triangle me-1"></i> Harap input Kode Billing SIMPONI terlebih dahulu untuk mengaktifkan pembayaran.
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
