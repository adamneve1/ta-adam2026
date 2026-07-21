@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto" style="max-width: 1200px;">
    <div class="mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h3 class="text-gray-800">Buat Invoice Tagihan</h3>
        <p class="text-muted mb-0">Buat invoice/tagihan baru berdasarkan Kontrak PKS yang terdaftar di RRI Batam.</p>
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

    <form action="{{ route('invoice.store') }}" method="POST">
        @csrf
        @php
            $penyetorDefault = auth()->user();
            $showPenandatanganFields = $errors->has('penyetor_nama')
                || $errors->has('penyetor_nip')
                || $errors->has('kepala_stasiun_nama')
                || $errors->has('kepala_stasiun_nip');
        @endphp

        {{-- ====== STEP 1 : Pilih Kontrak PKS ====== --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">1</span>
                    <span class="fw-semibold">Pilih Kontrak PKS <span class="text-danger">*</span></span>
                </div>
                <div style="max-width: 240px;">
                    <input type="text" id="pickerSearch" class="form-control form-control-sm" placeholder="Cari kontrak atau klien...">
                </div>
            </div>
            <div class="card-body p-0">
                @if($pksList->isEmpty())
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                        Tidak ada kontrak PKS yang tersedia untuk pembuatan invoice.
                    </div>
                @else
                    <div class="picker-table-scroll">
                        <table class="table align-middle mb-0 picker-table">
                            <thead>
                                <tr>
                                    <th style="width:46px;"></th>
                                    <th>Nomor Kontrak</th>
                                    <th>Judul PKS</th>
                                    <th>Nama Klien</th>
                                    <th class="text-end">Total Kontrak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pksList as $pks)
                                    @php
                                        $tanggalJatuhTempoInvoice = $pks->tanggal_jatuh_tempo_invoice ?? '';
                                        $isSelected = old('pks_id') == $pks->id || $selectedPksId == $pks->id;
                                    @endphp
                                    <tr class="picker-row {{ $isSelected ? 'active' : '' }}"
                                        data-search="{{ strtolower($pks->nomor . ' ' . $pks->judul . ' ' . ($pks->client->nama ?? '')) }}">
                                        <td class="text-center ps-3">
                                            <input class="form-check-input picker-radio"
                                                   type="radio"
                                                   name="pks_id"
                                                   id="pks_radio_{{ $pks->id }}"
                                                   value="{{ $pks->id }}"
                                                   data-total="{{ (int) $pks->total }}"
                                                   data-tanggal-jatuh-tempo="{{ $tanggalJatuhTempoInvoice }}"
                                                   data-judul="{{ $pks->judul }}"
                                                   data-client="{{ $pks->client->nama ?? '-' }}"
                                                   {{ $isSelected ? 'checked' : '' }}
                                                   required>
                                        </td>
                                        <td><span class="fw-semibold text-nowrap">{{ $pks->nomor }}</span></td>
                                        <td><span class="text-break" style="font-size:.85rem;">{{ $pks->judul }}</span></td>
                                        <td style="font-size:.85rem;">{{ $pks->client->nama ?? '-' }}</td>
                                        <td class="text-end text-nowrap pe-3">
                                            <span class="fw-semibold text-primary">Rp {{ number_format($pks->total, 0, ',', '.') }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                @error('pks_id')
                    <div class="px-3 py-2 border-top bg-danger bg-opacity-10">
                        <small class="text-danger"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    </div>
                @enderror
            </div>
            <div class="card-footer bg-white border-top py-2 px-3">
                <small class="text-muted">Invoice ini akan ditagihkan kepada klien dari PKS terpilih.</small>
            </div>
        </div>

        {{-- ====== STEP 2 : Detail Invoice + Ringkasan ====== --}}
        <div class="row g-4 align-items-start">
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-bottom py-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width:24px;height:24px;font-size:.75rem;">2</span>
                            <span class="fw-semibold">Detail Invoice</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="nomor_invoice" class="form-label fw-semibold">Nomor Invoice <span class="text-danger">*</span></label>
                                <input type="text" 
                                       name="nomor_invoice" 
                                       id="nomor_invoice" 
                                       class="form-control @error('nomor_invoice') is-invalid @enderror" 
                                       value="{{ old('nomor_invoice', $defaultNomorInvoice) }}" 
                                       readonly 
                                       required>
                                <div class="form-text">Dibuat otomatis secara berurutan oleh sistem (Readonly).</div>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_invoice" class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                                <input type="date" 
                                       name="tanggal_invoice" 
                                       id="tanggal_invoice" 
                                       class="form-control @error('tanggal_invoice') is-invalid @enderror" 
                                       value="{{ old('tanggal_invoice', date('Y-m-d')) }}" 
                                       required>
                            </div>
                        </div>

                        <input type="hidden" name="nominal" id="nominal_input" value="{{ old('nominal') }}">
                        <input type="hidden" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo') }}">

                        <div class="mb-4">
                            <label for="kode_billing" class="form-label fw-semibold">Kode Billing SIMPONI</label>
                            <input type="text" 
                                   name="kode_billing" 
                                   id="kode_billing" 
                                   class="form-control @error('kode_billing') is-invalid @enderror" 
                                   value="{{ old('kode_billing') }}" 
                                   placeholder="Masukkan 15 digit kode billing SIMPONI"
                                   inputmode="numeric"
                                   pattern="[0-9]{15}"
                                   maxlength="15"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            @error('kode_billing')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-muted">Masukkan 15 digit angka kode billing SIMPONI.</div>
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
                                                   value="{{ old('penyetor_nama', $penyetorDefault?->name) }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="penyetor_nip" class="form-label fw-semibold">NIP Penyetor <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="penyetor_nip"
                                                   id="penyetor_nip"
                                                   class="form-control @error('penyetor_nip') is-invalid @enderror"
                                                   value="{{ old('penyetor_nip', $penyetorDefault?->nip) }}"
                                                   inputmode="numeric"
                                                   pattern="\d{18}"
                                                   maxlength="18"
                                                   placeholder="18 digit angka"
                                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label for="kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kepala_stasiun_nama"
                                                   id="kepala_stasiun_nama"
                                                   class="form-control @error('kepala_stasiun_nama') is-invalid @enderror"
                                                   value="{{ old('kepala_stasiun_nama', $kepalaStasiunDefault?->name) }}">
                                            <div class="form-text">Default dari akun role Kepala Stasiun, boleh diubah manual.</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="kepala_stasiun_nip" class="form-label fw-semibold">NIP Kepala Stasiun <span class="text-danger">*</span></label>
                                            <input type="text"
                                                   name="kepala_stasiun_nip"
                                                   id="kepala_stasiun_nip"
                                                   class="form-control @error('kepala_stasiun_nip') is-invalid @enderror"
                                                   value="{{ old('kepala_stasiun_nip', $kepalaStasiunDefault?->nip) }}"
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
                            <button type="submit" class="btn btn-primary px-4" id="submit_button">
                                <i class="bi bi-check-circle me-1"></i> Simpan Invoice
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
    document.addEventListener('DOMContentLoaded', function() {
        const radios = document.querySelectorAll('.picker-radio');
        const rows = document.querySelectorAll('.picker-row');
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

        function clearRowHighlights() {
            rows.forEach(function (row) {
                row.classList.remove('active');
            });
        }

        function selectedPksData(radio) {
            if (!radio) return null;

            return {
                total: Number(radio.getAttribute('data-total') || 0),
                tanggalJatuhTempo: radio.getAttribute('data-tanggal-jatuh-tempo') || '',
                judul: radio.getAttribute('data-judul') || '-',
                client: radio.getAttribute('data-client') || '-',
            };
        }

        function updateJatuhTempo(data) {
            jatuhTempoInput.value = data ? data.tanggalJatuhTempo : '';
            jatuhTempoHint.textContent = data && data.tanggalJatuhTempo
                ? 'Otomatis 20 hari setelah tanggal selesai penyiaran terakhir: ' + data.tanggalJatuhTempo + '.'
                : 'Otomatis 20 hari setelah tanggal selesai penyiaran terakhir.';
        }

        function updateContractSummary(radio) {
            var data = selectedPksData(radio);

            if (!data) {
                infoCard.style.display = 'none';
                emptyState.style.display = 'block';
                updateJatuhTempo(null);
                submitButton.disabled = false;
                return;
            }

            var nominalInvoiceIni = Number(nominalInput.value || 0);

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

            clearRowHighlights();
            radio.closest('tr').classList.add('active');
        }

        function updateInfo(radio) {
            var data = selectedPksData(radio);

            if (data) {
                nominalInput.value = data.total;
                updateContractSummary(radio);
            } else {
                infoCard.style.display = 'none';
                emptyState.style.display = 'block';
            }
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
                var data = selectedPksData(this);
                nominalInput.value = data ? data.total : '';
                updateJatuhTempo(data);
                updateContractSummary(this);
            });
        });

        // Trigger saat halaman load pertama kali (misal jika ada error validation / query param)
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
    document.getElementById('penyetor_nip').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 18);
    });

    document.getElementById('kepala_stasiun_nip').addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 18);
    });
</script>
@endsection
