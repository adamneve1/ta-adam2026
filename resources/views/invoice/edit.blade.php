@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
        <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail
        </a>
        <h3 class="text-gray-800">Edit Invoice Tagihan</h3>
        <p class="text-muted">Perbarui data invoice tagihan yang belum dibayar.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong class="d-block mb-2">Harap perbaiki kesalahan berikut:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')
        @php
            $showPenandatanganFields = $errors->has('penyetor_nama')
                || $errors->has('penyetor_nip')
                || $errors->has('kepala_stasiun_nama')
                || $errors->has('kepala_stasiun_nip');
        @endphp

        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label for="pks_select" class="form-label fw-semibold">Pilih Kontrak PKS <span class="text-danger">*</span></label>
                            <select name="pks_id" id="pks_select" class="form-select @error('pks_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Kontrak --</option>
                                @foreach($pksList as $pks)
                                    @php
                                        $isSelected = old('pks_id', $invoice->pks_id) == $pks->id;
                                        $tanggalJatuhTempoInvoice = $pks->tanggal_jatuh_tempo_invoice ?? '';
                                    @endphp
                                    <option value="{{ $pks->id }}"
                                            data-total="{{ (int) $pks->total }}"
                                            data-tanggal-jatuh-tempo="{{ $tanggalJatuhTempoInvoice }}"
                                            data-judul="{{ $pks->judul }}"
                                            data-client="{{ $pks->client->nama ?? '-' }}"
                                            {{ $isSelected ? 'selected' : '' }}>
                                        {{ $pks->nomor }} - {{ $pks->judul }} (Total kontrak: Rp {{ number_format($pks->total, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nomor_invoice" class="form-label fw-semibold">Nomor Invoice <span class="text-danger">*</span></label>
                                <input type="text"
                                       name="nomor_invoice"
                                       id="nomor_invoice"
                                       class="form-control @error('nomor_invoice') is-invalid @enderror"
                                       value="{{ old('nomor_invoice', $invoice->nomor_invoice) }}"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_invoice" class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                                <input type="date"
                                       name="tanggal_invoice"
                                       id="tanggal_invoice"
                                       class="form-control @error('tanggal_invoice') is-invalid @enderror"
                                       value="{{ old('tanggal_invoice', $invoice->tanggal_invoice->format('Y-m-d')) }}"
                                       required>
                            </div>
                        </div>

                        <input type="hidden" name="nominal" id="nominal_input" value="{{ old('nominal', (int) $invoice->nominal) }}">
                        <input type="hidden" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $invoice->tanggal_jatuh_tempo->format('Y-m-d')) }}">

                        <div class="mb-4">
                            <label for="kode_billing" class="form-label fw-semibold">Kode Billing SIMPONI <span class="text-muted">(Opsional)</span></label>
                            <input type="text"
                                   name="kode_billing"
                                   id="kode_billing"
                                   class="form-control @error('kode_billing') is-invalid @enderror"
                                   value="{{ old('kode_billing', $invoice->kode_billing) }}"
                                   placeholder="Masukkan 15 digit kode billing Simponi jika sudah ada">
                        </div>

                        <div class="border rounded mb-4">
                            <button class="btn btn-light w-100 d-flex align-items-center justify-content-between rounded-0 border-0 px-3 py-2"
                                    type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#penandatanganFields"
                                    aria-expanded="{{ $showPenandatanganFields ? 'true' : 'false' }}"
                                    aria-controls="penandatanganFields">
                                <span class="fw-semibold">Ubah Data Penandatangan</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div class="collapse {{ $showPenandatanganFields ? 'show' : '' }}" id="penandatanganFields">
                                <div class="border-top p-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="penyetor_nama" class="form-label fw-semibold">Nama Penyetor <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="penyetor_nama"
                                                   id="penyetor_nama"
                                                   class="form-control @error('penyetor_nama') is-invalid @enderror"
                                                   value="{{ old('penyetor_nama', $invoice->penyetor_nama) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="penyetor_nip" class="form-label fw-semibold">NIP Penyetor <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="penyetor_nip"
                                                   id="penyetor_nip"
                                                   class="form-control @error('penyetor_nip') is-invalid @enderror"
                                                   value="{{ old('penyetor_nip', $invoice->penyetor_nip) }}"
                                                   inputmode="numeric"
                                                   maxlength="18"
                                                   placeholder="18 digit angka">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kepala_stasiun_nama"
                                                   id="kepala_stasiun_nama"
                                                   class="form-control @error('kepala_stasiun_nama') is-invalid @enderror"
                                                   value="{{ old('kepala_stasiun_nama', $invoice->kepala_stasiun_nama ?: $kepalaStasiunDefault?->name) }}">
                                            <div class="form-text">Default dari akun role Kepala Stasiun, boleh diubah manual.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kepala_stasiun_nip" class="form-label fw-semibold">NIP Kepala Stasiun <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kepala_stasiun_nip"
                                                   id="kepala_stasiun_nip"
                                                   class="form-control @error('kepala_stasiun_nip') is-invalid @enderror"
                                                   value="{{ old('kepala_stasiun_nip', $invoice->kepala_stasiun_nip ?: $kepalaStasiunDefault?->nip) }}"
                                                   inputmode="numeric"
                                                   maxlength="18"
                                                   placeholder="18 digit angka">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3">
                            <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-light border">Batal</a>
                            <button type="submit" class="btn btn-primary px-4" id="submit_button">
                                <i class="bi bi-save me-1"></i> Perbarui Invoice
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card shadow-sm border-0 position-sticky" style="top: 92px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3 border-bottom pb-3 mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded bg-primary bg-opacity-10 text-primary flex-shrink-0" style="width: 42px; height: 42px;">
                                <i class="bi bi-file-earmark-text fs-5"></i>
                            </span>
                            <div>
                                <h6 class="fw-semibold mb-1">Ringkasan Invoice</h6>
                                <small class="text-muted">Data PKS terpilih.</small>
                            </div>
                        </div>

                        <div id="pks_info_card" style="display: none;">
                            <div class="row g-3">
                                <div class="col-12">
                                    <span class="d-block text-muted small">Judul PKS</span>
                                    <strong class="text-dark text-break" id="info_judul">-</strong>
                                </div>
                                <div class="col-12">
                                    <span class="d-block text-muted small">Nama Klien / Mitra</span>
                                    <strong class="text-dark text-break" id="info_client">-</strong>
                                </div>
                            </div>

                            <div class="row g-2 mt-3">
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="h-100 bg-light border rounded p-3">
                                        <span class="d-block text-muted small">Nilai Kontrak</span>
                                        <strong id="info_total_kontrak">Rp 0</strong>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-12 col-xl-6">
                                    <div class="h-100 bg-light border border-primary-subtle rounded p-3">
                                        <span class="d-block text-muted small">Nominal Invoice</span>
                                        <strong class="text-primary" id="info_invoice_ini">Rp 0</strong>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="bg-light border rounded p-3">
                                        <span class="d-block text-muted small">Tanggal Jatuh Tempo</span>
                                        <strong id="info_tanggal_jatuh_tempo">-</strong>
                                        <small class="d-block text-muted mt-1" id="tanggal_jatuh_tempo_hint">Otomatis 20 hari setelah tanggal selesai penyiaran terakhir.</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center text-muted py-4" id="pks_empty_state">
                            <i class="bi bi-file-earmark-text fs-3 d-block mb-2"></i>
                            <span class="small">Pilih kontrak untuk melihat ringkasan invoice.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pksSelect = document.getElementById('pks_select');
        const nominalInput = document.getElementById('nominal_input');
        const jatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');
        const jatuhTempoHint = document.getElementById('tanggal_jatuh_tempo_hint');
        const infoCard = document.getElementById('pks_info_card');
        const emptyState = document.getElementById('pks_empty_state');
        const infoJudul = document.getElementById('info_judul');
        const infoClient = document.getElementById('info_client');
        const infoTotalKontrak = document.getElementById('info_total_kontrak');
        const infoInvoiceIni = document.getElementById('info_invoice_ini');
        const infoTanggalJatuhTempo = document.getElementById('info_tanggal_jatuh_tempo');
        const submitButton = document.getElementById('submit_button');

        function rupiah(value) {
            return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
        }

        function selectedPksData() {
            const option = pksSelect.options[pksSelect.selectedIndex];

            if (!option || option.value === '') {
                return null;
            }

            return {
                total: Number(option.getAttribute('data-total') || 0),
                tanggalJatuhTempo: option.getAttribute('data-tanggal-jatuh-tempo') || '',
                judul: option.getAttribute('data-judul') || '-',
                client: option.getAttribute('data-client') || '-',
            };
        }

        function updateJatuhTempo(data) {
            jatuhTempoInput.value = data ? data.tanggalJatuhTempo : '';
            jatuhTempoHint.textContent = data && data.tanggalJatuhTempo
                ? 'Otomatis 20 hari setelah tanggal selesai penyiaran terakhir: ' + data.tanggalJatuhTempo + '.'
                : 'Otomatis 20 hari setelah tanggal selesai penyiaran terakhir.';
        }

        function updateContractSummary() {
            const data = selectedPksData();

            if (!data) {
                infoCard.style.display = 'none';
                emptyState.style.display = 'block';
                updateJatuhTempo(null);
                submitButton.disabled = false;
                return;
            }

            const nominalInvoiceIni = Number(nominalInput.value || 0);

            infoJudul.textContent = data.judul;
            infoClient.textContent = data.client;
            infoTotalKontrak.textContent = rupiah(data.total);
            infoInvoiceIni.textContent = rupiah(nominalInvoiceIni);
            infoTanggalJatuhTempo.textContent = data.tanggalJatuhTempo
                ? new Date(data.tanggalJatuhTempo).toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'long', year: 'numeric'
                })
                : '-';
            updateJatuhTempo(data);
            infoCard.style.display = 'block';
            emptyState.style.display = 'none';
        }

        function updateInfo() {
            const data = selectedPksData();

            if (data) {
                nominalInput.value = data.total;
                updateContractSummary();
            } else {
                infoCard.style.display = 'none';
                emptyState.style.display = 'block';
            }
        }

        pksSelect.addEventListener('change', function() {
            const data = selectedPksData();
            nominalInput.value = data ? data.total : '';
            updateJatuhTempo(data);
            updateContractSummary();
        });

        if (pksSelect.value !== '') {
            updateInfo();
        }
    });
</script>
@endsection
