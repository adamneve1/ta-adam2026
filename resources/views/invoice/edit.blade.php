@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
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

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('invoice.update', $invoice->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Pilih Kontrak PKS -->
                <div class="mb-3">
                    <label for="pks_select" class="form-label fw-semibold">Pilih Kontrak PKS <span class="text-danger">*</span></label>
                    <select name="pks_id" id="pks_select" class="form-select @error('pks_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kontrak --</option>
                        @foreach($pksList as $pks)
                            @php
                                $isSelected = old('pks_id', $invoice->pks_id) == $pks->id;
                                $totalDitagihkan = (float) ($pks->total_ditagihkan ?? 0);
                                $sisaKontrak = max((float) $pks->total - $totalDitagihkan, 0);
                                $tanggalTerakhirPenyiaran = $pks->tanggal_terakhir_penyiaran
                                    ? \Carbon\Carbon::parse($pks->tanggal_terakhir_penyiaran)->toDateString()
                                    : '';
                                $tanggalJatuhTempoInvoice = $pks->tanggal_jatuh_tempo_invoice ?? '';
                            @endphp
                            <option value="{{ $pks->id }}" 
                                    data-total="{{ (int) $pks->total }}" 
                                    data-total-ditagihkan="{{ (int) $totalDitagihkan }}"
                                    data-sisa-kontrak="{{ (int) $sisaKontrak }}"
                                    data-jumlah-invoice="{{ $pks->invoices_count }}"
                                    data-tanggal-terakhir-penyiaran="{{ $tanggalTerakhirPenyiaran }}"
                                    data-tanggal-jatuh-tempo="{{ $tanggalJatuhTempoInvoice }}"
                                    data-judul="{{ $pks->judul }}"
                                    data-client="{{ $pks->client->nama ?? '-' }}"
                                    {{ $sisaKontrak <= 0 && !$isSelected ? 'disabled' : '' }}
                                    {{ $isSelected ? 'selected' : '' }}>
                                {{ $pks->nomor }} - {{ $pks->judul }}
                                @if($sisaKontrak <= 0 && !$isSelected)
                                    (Sudah ditagihkan penuh)
                                @else
                                    (Batas invoice ini: Rp {{ number_format($sisaKontrak, 0, ',', '.') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Info Klien Ringkas (Dinamis via JS) -->
                <div class="mb-3 p-3 bg-light rounded border" id="pks_info_card" style="display: none;">
                    <div class="row">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <span class="d-block text-muted small">Judul PKS:</span>
                            <strong class="text-dark" id="info_judul">-</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-muted small">Nama Klien / Mitra:</span>
                            <strong class="text-dark" id="info_client">-</strong>
                        </div>
                    </div>
                    <hr class="my-3">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <span class="d-block text-muted small">Nilai Kontrak</span>
                            <strong id="info_total_kontrak">Rp 0</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="d-block text-muted small">Sudah Ditagihkan</span>
                            <strong id="info_total_ditagihkan">Rp 0</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="d-block text-muted small">Nominal Invoice Ini</span>
                            <strong id="info_invoice_ini">Rp 0</strong>
                        </div>
                        <div class="col-md-3 col-6">
                            <span class="d-block text-muted small">Sisa Setelah Invoice Ini</span>
                            <strong class="text-primary" id="info_sisa_setelah">Rp 0</strong>
                        </div>
                        <div class="col-12">
                            <small class="text-muted" id="info_termin">Termin lain: 0 invoice. Sisa yang bisa dipakai invoice ini: Rp 0.</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Nomor Invoice -->
                    <div class="col-md-6 mb-3">
                        <label for="nomor_invoice" class="form-label fw-semibold">Nomor Invoice <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nomor_invoice" 
                               id="nomor_invoice" 
                               class="form-control @error('nomor_invoice') is-invalid @enderror" 
                               value="{{ old('nomor_invoice', $invoice->nomor_invoice) }}" 
                               required>
                    </div>

                    <!-- Nominal Tagihan -->
                    <div class="col-md-6 mb-3">
                        <label for="nominal_input" class="form-label fw-semibold">Nominal Tagihan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" 
                                   name="nominal" 
                                   id="nominal_input" 
                                   class="form-control @error('nominal') is-invalid @enderror" 
                                   value="{{ old('nominal', (int) $invoice->nominal) }}" 
                                   min="1" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Tanggal Invoice -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_invoice" class="form-label fw-semibold">Tanggal Invoice <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_invoice" 
                               id="tanggal_invoice" 
                               class="form-control @error('tanggal_invoice') is-invalid @enderror" 
                               value="{{ old('tanggal_invoice', $invoice->tanggal_invoice->format('Y-m-d')) }}" 
                               required>
                    </div>

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_jatuh_tempo" 
                               id="tanggal_jatuh_tempo" 
                               class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                               value="{{ old('tanggal_jatuh_tempo', $invoice->tanggal_jatuh_tempo->format('Y-m-d')) }}" 
                               readonly
                               required>
                        <div class="form-text" id="tanggal_jatuh_tempo_hint">Otomatis 28 hari setelah tanggal selesai penyiaran terakhir.</div>
                    </div>
                </div>

                <!-- Kode Billing SIMPONI (Nullable) -->
                <div class="mb-4">
                    <label for="kode_billing" class="form-label fw-semibold">Kode Billing SIMPONI <span class="text-muted">(Opsional)</span></label>
                    <input type="text" 
                           name="kode_billing" 
                           id="kode_billing" 
                           class="form-control @error('kode_billing') is-invalid @enderror" 
                           value="{{ old('kode_billing', $invoice->kode_billing) }}" 
                           placeholder="Masukkan 15 digit kode billing Simponi jika sudah ada">
                </div>

                <!-- Data Penandatangan -->
                <h6 class="fw-semibold mb-3">Data Penandatangan</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="penyetor_nama" class="form-label fw-semibold">Nama Penyetor <span class="text-danger">*</span></label>
                        <input type="text"
                               name="penyetor_nama"
                               id="penyetor_nama"
                               class="form-control @error('penyetor_nama') is-invalid @enderror"
                               value="{{ old('penyetor_nama', $invoice->penyetor_nama) }}"
                               required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="penyetor_nip" class="form-label fw-semibold">NIP Penyetor <span class="text-danger">*</span></label>
                        <input type="text"
                               name="penyetor_nip"
                               id="penyetor_nip"
                               class="form-control @error('penyetor_nip') is-invalid @enderror"
                               value="{{ old('penyetor_nip', $invoice->penyetor_nip) }}"
                               inputmode="numeric"
                               pattern="[0-9]{18}"
                               minlength="18"
                               maxlength="18"
                               placeholder="18 digit angka"
                               required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun <span class="text-danger">*</span></label>
                        <input type="text"
                               name="kepala_stasiun_nama"
                               id="kepala_stasiun_nama"
                               class="form-control @error('kepala_stasiun_nama') is-invalid @enderror"
                               value="{{ old('kepala_stasiun_nama', $invoice->kepala_stasiun_nama ?: $kepalaStasiunDefault?->name) }}"
                               required>
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
                               pattern="[0-9]{18}"
                               minlength="18"
                               maxlength="18"
                               placeholder="18 digit angka"
                               required>
                    </div>
                </div>

                <!-- Button Actions -->
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('invoice.show', $invoice->id) }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4" id="submit_button">
                        <i class="bi bi-save me-1"></i> Perbarui Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const pksSelect = document.getElementById('pks_select');
        const nominalInput = document.getElementById('nominal_input');
        const jatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');
        const jatuhTempoHint = document.getElementById('tanggal_jatuh_tempo_hint');
        const infoCard = document.getElementById('pks_info_card');
        const infoJudul = document.getElementById('info_judul');
        const infoClient = document.getElementById('info_client');
        const infoTotalKontrak = document.getElementById('info_total_kontrak');
        const infoTotalDitagihkan = document.getElementById('info_total_ditagihkan');
        const infoInvoiceIni = document.getElementById('info_invoice_ini');
        const infoSisaSetelah = document.getElementById('info_sisa_setelah');
        const infoTermin = document.getElementById('info_termin');
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
                totalDitagihkan: Number(option.getAttribute('data-total-ditagihkan') || 0),
                sisaKontrak: Number(option.getAttribute('data-sisa-kontrak') || 0),
                jumlahInvoice: Number(option.getAttribute('data-jumlah-invoice') || 0),
                tanggalTerakhirPenyiaran: option.getAttribute('data-tanggal-terakhir-penyiaran') || '',
                tanggalJatuhTempo: option.getAttribute('data-tanggal-jatuh-tempo') || '',
                judul: option.getAttribute('data-judul') || '-',
                client: option.getAttribute('data-client') || '-',
            };
        }

        function updateJatuhTempo(data) {
            jatuhTempoInput.value = data ? data.tanggalJatuhTempo : '';
            jatuhTempoHint.textContent = data && data.tanggalTerakhirPenyiaran
                ? 'Otomatis 28 hari setelah tanggal selesai penyiaran terakhir: ' + data.tanggalTerakhirPenyiaran + '.'
                : 'Otomatis 28 hari setelah tanggal selesai penyiaran terakhir.';
        }

        function updateContractSummary() {
            const data = selectedPksData();

            if (!data) {
                infoCard.style.display = 'none';
                updateJatuhTempo(null);
                submitButton.disabled = false;
                return;
            }

            const nominalInvoiceIni = Number(nominalInput.value || 0);
            const sisaSetelahInvoiceIni = Math.max(data.sisaKontrak - nominalInvoiceIni, 0);

            infoJudul.textContent = data.judul;
            infoClient.textContent = data.client;
            infoTotalKontrak.textContent = rupiah(data.total);
            infoTotalDitagihkan.textContent = rupiah(data.totalDitagihkan);
            infoInvoiceIni.textContent = rupiah(nominalInvoiceIni);
            infoSisaSetelah.textContent = rupiah(sisaSetelahInvoiceIni);
            infoTermin.textContent = 'Termin lain: ' + data.jumlahInvoice + ' invoice. Sisa yang bisa dipakai invoice ini: ' + rupiah(data.sisaKontrak) + '.';
            nominalInput.max = data.sisaKontrak;
            submitButton.disabled = data.sisaKontrak <= 0 || nominalInvoiceIni > data.sisaKontrak;
            updateJatuhTempo(data);
            infoCard.style.display = 'block';
        }

        function updateInfo() {
            const data = selectedPksData();

            if (data) {
                updateContractSummary();
            } else {
                infoCard.style.display = 'none';
            }
        }

        pksSelect.addEventListener('change', function() {
            const data = selectedPksData();
            nominalInput.value = data ? data.sisaKontrak : '';
            updateJatuhTempo(data);
            updateContractSummary();
        });

        nominalInput.addEventListener('input', updateContractSummary);
        
        if (pksSelect.value !== '') {
            updateInfo();
        }
    });
</script>
@endsection
