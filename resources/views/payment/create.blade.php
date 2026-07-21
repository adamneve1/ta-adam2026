@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Invoice
        </a>
        <h3 class="text-gray-800">Input Pembayaran Invoice</h3>
        <p class="text-muted mb-0">Pilih invoice aktif, lalu lengkapi data pembayaran di bawah.</p>
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

        {{-- ====== STEP 1 : Pilih Invoice ====== --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">1</span>
                    <span class="fw-semibold">Pilih Invoice</span>
                </div>
                <div style="max-width: 240px;">
                    <input type="text" id="pickerSearch" class="form-control form-control-sm" placeholder="Cari invoice atau klien...">
                </div>
            </div>
            <div class="card-body p-0">
                @if($invoices->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada invoice yang tersedia untuk pembayaran.
                    </div>
                @else
                    <div class="picker-table-scroll">
                        <table class="table align-middle mb-0 picker-table">
                            <thead>
                                <tr>
                                    <th style="width:46px;"></th>
                                    <th>Nomor Invoice</th>
                                    <th>Nama Klien</th>
                                    <th>Nomor Kontrak</th>
                                    <th class="text-end">Nominal Invoice</th>
                                    <th class="text-end">Sisa Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $invoice)
                                    @php
                                        $sisaTagihan = (float) $invoice->nominal;
                                        $isSelected = old('invoice_id', $selectedInvoiceId ?? null) == $invoice->id;
                                    @endphp
                                    <tr class="picker-row {{ $isSelected ? 'active' : '' }}"
                                        data-search="{{ strtolower($invoice->nomor_invoice . ' ' . ($invoice->pks->client->nama ?? '') . ' ' . ($invoice->pks->nomor ?? '')) }}">
                                        <td class="text-center ps-3">
                                            <input class="form-check-input picker-radio"
                                                   type="radio"
                                                   name="invoice_id"
                                                   id="invoice_radio_{{ $invoice->id }}"
                                                   value="{{ $invoice->id }}"
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
                                                   {{ $isSelected ? 'checked' : '' }}
                                                   required>
                                        </td>
                                        <td><span class="fw-semibold">{{ $invoice->nomor_invoice }}</span></td>
                                        <td>{{ $invoice->pks->client->nama ?? '-' }}</td>
                                        <td><span class="text-nowrap">{{ $invoice->pks->nomor ?? '-' }}</span></td>
                                        <td class="text-end text-nowrap">Rp {{ number_format((float) $invoice->nominal, 0, ',', '.') }}</td>
                                        <td class="text-end text-nowrap pe-3">
                                            <span class="fw-semibold text-primary">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @error('invoice_id')
                    <div class="px-3 py-2 border-top bg-danger bg-opacity-10">
                        <small class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    </div>
                @enderror
            </div>
        </div>

        {{-- ====== STEP 2 : Detail Pembayaran + Ringkasan ====== --}}
        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">2</span>
                            <span class="fw-semibold">Detail Pembayaran</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control @error('tanggal_pembayaran') is-invalid @enderror" value="{{ old('tanggal_pembayaran', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="ntpn" class="form-label fw-semibold">NTPN <span class="text-danger">*</span></label>
                                <input type="text" name="ntpn" id="ntpn" class="form-control @error('ntpn') is-invalid @enderror" value="{{ old('ntpn') }}" required maxlength="16" minlength="16" pattern="[A-Za-z0-9]{16}">
                                <small class="text-muted">16 karakter alfanumerik (angka dan huruf).</small>
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

<style>
    /* ---- Picker table (shared) ---- */
    .picker-table-scroll {
        max-height: 310px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .picker-table {
        min-width: 700px;
    }

    .picker-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8f9fa;
        font-size: .72rem;
        text-transform: uppercase;
        color: #6c757d;
        font-weight: 700;
        letter-spacing: .02em;
        white-space: nowrap;
        border-bottom: 2px solid #e9ecef;
        padding: .65rem .75rem;
    }

    .picker-table tbody td {
        font-size: .85rem;
        padding: .6rem .75rem;
        border-bottom: 1px solid #f1f3f5;
        vertical-align: middle;
    }

    .picker-table tbody tr {
        transition: background-color .12s ease, border-color .12s ease;
        border-left: 3px solid transparent;
        cursor: pointer;
    }

    .picker-table tbody tr:hover {
        background-color: #f5f8ff;
    }

    .picker-table tbody tr.active {
        background-color: #e8f0fe;
        border-left-color: var(--bs-primary, #0d6efd);
    }

    .picker-table tbody tr.active td {
        border-bottom-color: #d0def5;
    }

    .picker-table .form-check-input {
        width: 1.1em;
        height: 1.1em;
        margin: 0;
        cursor: pointer;
    }

    .picker-table .form-check-input:checked {
        background-color: var(--bs-primary, #0d6efd);
        border-color: var(--bs-primary, #0d6efd);
    }

    /* Scrollbar styling */
    .picker-table-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .picker-table-scroll::-webkit-scrollbar-track {
        background: transparent;
    }
    .picker-table-scroll::-webkit-scrollbar-thumb {
        background: #ced4da;
        border-radius: 3px;
    }
    .picker-table-scroll::-webkit-scrollbar-thumb:hover {
        background: #adb5bd;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios = document.querySelectorAll('.picker-radio');
        const rows = document.querySelectorAll('.picker-row');
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

        function clearRowHighlights() {
            rows.forEach(function (row) {
                row.classList.remove('active');
            });
        }

        function updateInfo(radio) {
            if (!radio) {
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

            infoNomorKontrak.textContent = radio.getAttribute('data-nomor-kontrak');
            infoJudulKontrak.textContent = radio.getAttribute('data-judul-kontrak');
            infoClient.textContent = radio.getAttribute('data-client');
            infoNarahubung.textContent = radio.getAttribute('data-narahubung');
            infoKodeBilling.textContent = radio.getAttribute('data-kode-billing') || '-';
            infoJumlahTagihan.textContent = rupiah(radio.getAttribute('data-jumlah-tagihan'));
            infoSisaTagihan.textContent = rupiah(radio.getAttribute('data-sisa-tagihan'));
            jumlahPembayaranInput.value = radio.getAttribute('data-sisa-tagihan') || '';

            const penyetorNama = radio.getAttribute('data-penyetor-nama');
            const penyetorNip = radio.getAttribute('data-penyetor-nip');
            const kepalaStasiunNama = radio.getAttribute('data-kepala-stasiun-nama');
            const kepalaStasiunNip = radio.getAttribute('data-kepala-stasiun-nip');

            if (!kwitansiPenyetorNama.value) kwitansiPenyetorNama.value = penyetorNama || '';
            if (!kwitansiPenyetorNip.value) kwitansiPenyetorNip.value = penyetorNip || '';
            if (!kwitansiKepalaStasiunNama.value) kwitansiKepalaStasiunNama.value = kepalaStasiunNama || '';
            if (!kwitansiKepalaStasiunNip.value) kwitansiKepalaStasiunNip.value = kepalaStasiunNip || '';

            clearRowHighlights();
            radio.closest('tr').classList.add('active');
        }

        // Klik baris = pilih radio
        rows.forEach(function (row) {
            row.addEventListener('click', function (e) {
                if (e.target.tagName === 'INPUT') return;
                var radio = row.querySelector('.picker-radio');
                radio.checked = true;
                radio.dispatchEvent(new Event('change'));
            });
        });

        radios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                kwitansiPenyetorNama.value = '';
                kwitansiPenyetorNip.value = '';
                kwitansiKepalaStasiunNama.value = '';
                kwitansiKepalaStasiunNip.value = '';
                updateInfo(this);
            });
        });

        // Auto-fill jika radio sudah terpilih (old input / query param)
        var checkedRadio = document.querySelector('.picker-radio:checked');
        if (checkedRadio) {
            updateInfo(checkedRadio);
        }

        // Live search
        var searchInput = document.getElementById('pickerSearch');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                var keyword = this.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    var text = row.getAttribute('data-search') || '';
                    row.style.display = text.includes(keyword) ? '' : 'none';
                });
            });
        }
    });
</script>

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
