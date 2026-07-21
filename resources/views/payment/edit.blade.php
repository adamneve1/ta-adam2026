@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
        <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Pembayaran
        </a>
        <h3 class="text-gray-800">Edit Pembayaran</h3>
        <p class="text-muted">Perbarui data pembayaran dan snapshot penandatangan kwitansi.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @php
            $showKwitansiFields = $errors->has('kwitansi_penyetor_nama')
                || $errors->has('kwitansi_penyetor_nip')
                || $errors->has('kwitansi_kepala_stasiun_nama')
                || $errors->has('kwitansi_kepala_stasiun_nip');
        @endphp

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control @error('tanggal_pembayaran') is-invalid @enderror" value="{{ old('tanggal_pembayaran', $payment->tanggal_pembayaran->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="ntpn" class="form-label fw-semibold">NTPN <span class="text-danger">*</span></label>
                                <input type="text" name="ntpn" id="ntpn" class="form-control @error('ntpn') is-invalid @enderror" value="{{ old('ntpn', $payment->ntpn) }}" required maxlength="16" minlength="16" pattern="[A-Za-z0-9]{16}">
                                <small class="text-muted">16 karakter alfanumerik (angka dan huruf).</small>
                            </div>
                        </div>

                        <input type="hidden" name="jumlah_pembayaran" value="{{ old('jumlah_pembayaran', (int) $payment->jumlah_pembayaran) }}">
                        <input type="hidden" name="ntb" value="{{ old('ntb', $payment->ntb) }}">

                        <div class="mb-4">
                            <label for="bukti_pembayaran" class="form-label fw-semibold">Upload Bukti Pembayaran Baru <span class="text-muted">(Opsional)</span></label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Kosongkan jika tidak ingin mengganti bukti pembayaran.</small>
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-semibold">Catatan <span class="text-muted">(Opsional)</span></label>
                            <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $payment->catatan) }}</textarea>
                        </div>

                        <div class="border rounded mb-4">
                            <button class="btn btn-light w-100 d-flex align-items-center justify-content-between rounded-0 border-0 px-3 py-2"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#kwitansiFields"
                                    aria-expanded="{{ $showKwitansiFields ? 'true' : 'false' }}"
                                    aria-controls="kwitansiFields">
                                <span class="fw-semibold">Ubah Penandatangan Kwitansi</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div class="collapse {{ $showKwitansiFields ? 'show' : '' }}" id="kwitansiFields">
                                <div class="border-top p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="kwitansi_penyetor_nama" class="form-label fw-semibold">Nama Penyetor Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_penyetor_nama"
                                                   id="kwitansi_penyetor_nama"
                                                   class="form-control @error('kwitansi_penyetor_nama') is-invalid @enderror"
                                                   value="{{ old('kwitansi_penyetor_nama', $payment->kwitansi_penyetor_nama) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="kwitansi_penyetor_nip" class="form-label fw-semibold">NIP Penyetor Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_penyetor_nip"
                                                   id="kwitansi_penyetor_nip"
                                                   class="form-control @error('kwitansi_penyetor_nip') is-invalid @enderror"
                                                   value="{{ old('kwitansi_penyetor_nip', $payment->kwitansi_penyetor_nip) }}"
                                                   inputmode="numeric"
                                                   pattern="\d{18}"
                                                   maxlength="18"
                                                   placeholder="18 digit angka"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="kwitansi_kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_kepala_stasiun_nama"
                                                   id="kwitansi_kepala_stasiun_nama"
                                                   class="form-control @error('kwitansi_kepala_stasiun_nama') is-invalid @enderror"
                                                   value="{{ old('kwitansi_kepala_stasiun_nama', $payment->kwitansi_kepala_stasiun_nama) }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kwitansi_kepala_stasiun_nip" class="form-label fw-semibold">NIP Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_kepala_stasiun_nip"
                                                   id="kwitansi_kepala_stasiun_nip"
                                                   class="form-control @error('kwitansi_kepala_stasiun_nip') is-invalid @enderror"
                                                   value="{{ old('kwitansi_kepala_stasiun_nip', $payment->kwitansi_kepala_stasiun_nip) }}"
                                                   inputmode="numeric"
                                                   pattern="\d{18}"
                                                   maxlength="18"
                                                   placeholder="18 digit angka"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('payment.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 position-sticky" style="top: 92px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi bi-receipt fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-semibold mb-1">Ringkasan Pembayaran</h6>
                                <small class="text-muted">Detail invoice dan pembayaran.</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Nomor Pembayaran</small>
                                <div class="fw-semibold text-dark text-break">{{ $payment->nomor_pembayaran ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Invoice</small>
                                <div class="fw-semibold text-dark text-break">{{ $payment->invoice->nomor_invoice ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Kontrak</small>
                                <div class="fw-semibold text-dark text-break">{{ $payment->invoice->pks->nomor ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Klien</small>
                                <div class="fw-semibold text-dark text-break">{{ $payment->invoice->pks->client->nama ?? '-' }}</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Kode Billing Invoice</small>
                                <code class="d-inline-block fw-semibold text-dark text-break">{{ $payment->invoice->kode_billing ?? $payment->kode_billing ?? '-' }}</code>
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="h-100 bg-light border rounded p-3">
                                    <small class="text-muted d-block mb-1">Nominal Invoice</small>
                                    <div class="fw-bold text-dark">Rp {{ number_format($payment->invoice->nominal ?? $payment->jumlah_pembayaran ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="h-100 bg-light border border-primary-subtle rounded p-3">
                                    <small class="text-muted d-block mb-1">Jumlah Dibayar</small>
                                    <div class="fw-bold text-primary">Rp {{ number_format($payment->jumlah_pembayaran ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.getElementById('ntpn').addEventListener('input', function () {
        this.value = this.value.replace(/[^A-Za-z0-9]/g, '').toUpperCase().slice(0, 16);
    });

    document.getElementById('kwitansi_penyetor_nip').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 18);
    });

    document.getElementById('kwitansi_kepala_stasiun_nip').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 18);
    });
</script>
@endsection
