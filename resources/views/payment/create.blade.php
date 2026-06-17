@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Invoice
        </a>
        <h3 class="text-gray-800">Input Pembayaran Invoice</h3>
        <p class="text-muted">Pilih invoice aktif, data kontrak dan klien akan terisi otomatis.</p>
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

    <form action="{{ route('payment.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                        <div class="mb-4">
                            <label for="invoice_id" class="form-label fw-semibold">Pilih Invoice <span class="text-danger">*</span></label>
                            <select name="invoice_id" id="invoice_id" class="form-select @error('invoice_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Invoice --</option>
                                @foreach($invoices as $invoice)
                                    @php
                                        $sisaTagihan = (float) $invoice->nominal;
                                    @endphp
                                    <option value="{{ $invoice->id }}"
                                        data-nomor-kontrak="{{ $invoice->pks->nomor ?? '-' }}"
                                        data-judul-kontrak="{{ $invoice->pks->judul ?? '-' }}"
                                        data-client="{{ $invoice->pks->client->nama ?? '-' }}"
                                        data-narahubung="{{ $invoice->pks->client->no_narahubung ?? '-' }}"
                                        data-kode-billing="{{ $invoice->kode_billing ?? '' }}"
                                        data-jumlah-tagihan="{{ (float) $invoice->nominal }}"
                                        data-sisa-tagihan="{{ $sisaTagihan }}"
                                        data-penyetor-nama="{{ $invoice->penyetor_nama ?? '' }}"
                                        data-penyetor-nip="{{ $invoice->penyetor_nip ?? '' }}"
                                        data-kepala-stasiun-nama="{{ $invoice->kepala_stasiun_nama ?? '' }}"
                                        data-kepala-stasiun-nip="{{ $invoice->kepala_stasiun_nip ?? '' }}"
                                        {{ (old('invoice_id', $selectedInvoiceId ?? null) == $invoice->id) ? 'selected' : '' }}>
                                        {{ $invoice->nomor_invoice }} - {{ $invoice->pks->client->nama ?? '-' }} (Sisa: Rp {{ number_format($sisaTagihan, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control @error('tanggal_pembayaran') is-invalid @enderror" value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="ntpn" class="form-label fw-semibold">NTPN <span class="text-danger">*</span></label>
                                <input type="text" name="ntpn" id="ntpn" class="form-control @error('ntpn') is-invalid @enderror" value="{{ old('ntpn') }}" required>
                            </div>
                        </div>

                        <input type="hidden" name="jumlah_pembayaran" id="jumlah_pembayaran" value="{{ old('jumlah_pembayaran') }}">
                        <input type="hidden" name="ntb" value="{{ old('ntb') }}">

                        <div class="mb-4">
                            <label for="bukti_pembayaran" class="form-label fw-semibold">Upload Bukti Pembayaran <span class="text-muted">(Opsional)</span></label>
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Format: PDF/JPG/PNG, maksimal 5MB.</small>
                        </div>

                        <div class="mb-4">
                            <label for="catatan" class="form-label fw-semibold">Catatan <span class="text-muted">(Opsional)</span></label>
                            <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
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
                                    <div class="alert alert-info py-2 small">
                                        Default mengikuti penandatangan invoice terpilih, tetapi bisa diubah jika ada PLH atau pergantian pejabat.
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="kwitansi_penyetor_nama" class="form-label fw-semibold">Nama Penyetor Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_penyetor_nama"
                                                   id="kwitansi_penyetor_nama"
                                                   class="form-control @error('kwitansi_penyetor_nama') is-invalid @enderror"
                                                   value="{{ old('kwitansi_penyetor_nama') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="kwitansi_penyetor_nip" class="form-label fw-semibold">NIP Penyetor Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_penyetor_nip"
                                                   id="kwitansi_penyetor_nip"
                                                   class="form-control @error('kwitansi_penyetor_nip') is-invalid @enderror"
                                                   value="{{ old('kwitansi_penyetor_nip') }}"
                                                   inputmode="numeric"
                                                   maxlength="18"
                                                   placeholder="18 digit angka">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="kwitansi_kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_kepala_stasiun_nama"
                                                   id="kwitansi_kepala_stasiun_nama"
                                                   class="form-control @error('kwitansi_kepala_stasiun_nama') is-invalid @enderror"
                                                   value="{{ old('kwitansi_kepala_stasiun_nama') }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kwitansi_kepala_stasiun_nip" class="form-label fw-semibold">NIP Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kwitansi_kepala_stasiun_nip"
                                                   id="kwitansi_kepala_stasiun_nip"
                                                   class="form-control @error('kwitansi_kepala_stasiun_nip') is-invalid @enderror"
                                                   value="{{ old('kwitansi_kepala_stasiun_nip') }}"
                                                   inputmode="numeric"
                                                   maxlength="18"
                                                   placeholder="18 digit angka">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('invoice.index') }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Pembayaran</button>
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
                                <h6 class="fw-semibold mb-1">Ringkasan Invoice</h6>
                                <small class="text-muted">Detail invoice terpilih.</small>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Nomor Kontrak</small>
                                <div class="fw-semibold text-dark text-break" id="info_nomor_kontrak">-</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Nama Klien</small>
                                <div class="fw-semibold text-dark text-break" id="info_client">-</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Judul Kontrak</small>
                                <div class="fw-semibold text-dark text-break" id="info_judul_kontrak">-</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">No Narahubung</small>
                                <div class="fw-semibold text-dark text-break" id="info_narahubung">-</div>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block mb-1">Kode Billing Invoice</small>
                                <code class="d-inline-block fw-semibold text-dark text-break" id="info_kode_billing">-</code>
                            </div>
                        </div>

                        <div class="row g-2 mt-3">
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="h-100 bg-light border rounded p-3">
                                    <small class="text-muted d-block mb-1">Nominal Invoice</small>
                                    <div class="fw-bold text-dark" id="info_jumlah_tagihan">-</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-12 col-xl-6">
                                <div class="h-100 bg-light border border-primary-subtle rounded p-3">
                                    <small class="text-muted d-block mb-1">Nominal Wajib Dibayar</small>
                                    <div class="fw-bold text-primary" id="info_sisa_tagihan">-</div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('invoice_id');
        const jumlahPembayaranInput = document.getElementById('jumlah_pembayaran');
        const kwitansiPenyetorNama = document.getElementById('kwitansi_penyetor_nama');
        const kwitansiPenyetorNip = document.getElementById('kwitansi_penyetor_nip');
        const kwitansiKepalaStasiunNama = document.getElementById('kwitansi_kepala_stasiun_nama');
        const kwitansiKepalaStasiunNip = document.getElementById('kwitansi_kepala_stasiun_nip');

        const infoNomorKontrak = document.getElementById('info_nomor_kontrak');
        const infoJudulKontrak = document.getElementById('info_judul_kontrak');
        const infoClient = document.getElementById('info_client');
        const infoNarahubung = document.getElementById('info_narahubung');
        const infoKodeBilling = document.getElementById('info_kode_billing');
        const infoJumlahTagihan = document.getElementById('info_jumlah_tagihan');
        const infoSisaTagihan = document.getElementById('info_sisa_tagihan');

        function rupiah(angka) {
            return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
        }

        function updateInfo() {
            const option = select.options[select.selectedIndex];
            if (!option || option.value === '') {
                infoNomorKontrak.textContent = '-';
                infoJudulKontrak.textContent = '-';
                infoClient.textContent = '-';
                infoNarahubung.textContent = '-';
                infoKodeBilling.textContent = '-';
                infoJumlahTagihan.textContent = '-';
                infoSisaTagihan.textContent = '-';
                jumlahPembayaranInput.value = '';
                return;
            }

            const nomorKontrak = option.getAttribute('data-nomor-kontrak');
            const judulKontrak = option.getAttribute('data-judul-kontrak');
            const client = option.getAttribute('data-client');
            const narahubung = option.getAttribute('data-narahubung');
            const kodeBilling = option.getAttribute('data-kode-billing');
            const jumlahTagihan = option.getAttribute('data-jumlah-tagihan');
            const sisaTagihan = option.getAttribute('data-sisa-tagihan');
            const penyetorNama = option.getAttribute('data-penyetor-nama');
            const penyetorNip = option.getAttribute('data-penyetor-nip');
            const kepalaStasiunNama = option.getAttribute('data-kepala-stasiun-nama');
            const kepalaStasiunNip = option.getAttribute('data-kepala-stasiun-nip');

            infoNomorKontrak.textContent = nomorKontrak;
            infoJudulKontrak.textContent = judulKontrak;
            infoClient.textContent = client;
            infoNarahubung.textContent = narahubung;
            infoKodeBilling.textContent = kodeBilling || '-';
            infoJumlahTagihan.textContent = rupiah(jumlahTagihan);
            infoSisaTagihan.textContent = rupiah(sisaTagihan);
            jumlahPembayaranInput.value = sisaTagihan || '';

            if (!kwitansiPenyetorNama.value) {
                kwitansiPenyetorNama.value = penyetorNama || '';
            }
            if (!kwitansiPenyetorNip.value) {
                kwitansiPenyetorNip.value = penyetorNip || '';
            }
            if (!kwitansiKepalaStasiunNama.value) {
                kwitansiKepalaStasiunNama.value = kepalaStasiunNama || '';
            }
            if (!kwitansiKepalaStasiunNip.value) {
                kwitansiKepalaStasiunNip.value = kepalaStasiunNip || '';
            }
        }

        select.addEventListener('change', function () {
            kwitansiPenyetorNama.value = '';
            kwitansiPenyetorNip.value = '';
            kwitansiKepalaStasiunNama.value = '';
            kwitansiKepalaStasiunNip.value = '';
            updateInfo();
        });
        if (select.value) {
            updateInfo();
        }
    });
</script>
@endsection
