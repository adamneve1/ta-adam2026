@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <a href="{{ route('invoice.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h3 class="text-gray-800">Buat Invoice Tagihan</h3>
        <p class="text-muted">Buat invoice/tagihan baru berdasarkan Kontrak PKS yang terdaftar di RRI Batam.</p>
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
            <form action="{{ route('invoice.store') }}" method="POST">
                @csrf

                <!-- Pilih Kontrak PKS -->
                <div class="mb-3">
                    <label for="pks_select" class="form-label fw-semibold">Pilih Kontrak PKS <span class="text-danger">*</span></label>
                    <select name="pks_id" id="pks_select" class="form-select @error('pks_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Kontrak --</option>
                        @foreach($pksList as $pks)
                            <option value="{{ $pks->id }}" 
                                    data-total="{{ (int) $pks->total }}" 
                                    data-judul="{{ $pks->judul }}"
                                    data-client="{{ $pks->client->nama ?? '-' }}"
                                    {{ (old('pks_id') == $pks->id || $selectedPksId == $pks->id) ? 'selected' : '' }}>
                                {{ $pks->nomor }} - {{ $pks->judul }} (Nilai: Rp {{ number_format($pks->total, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text text-muted">Invoice ini akan ditagihkan kepada klien dari PKS terpilih.</div>
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
                </div>

                <div class="row">
                    <!-- Nomor Invoice -->
                    <div class="col-md-6 mb-3">
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

                    <!-- Nominal Tagihan -->
                    <div class="col-md-6 mb-3">
                        <label for="nominal_input" class="form-label fw-semibold">Nominal Tagihan (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">Rp</span>
                            <input type="number" 
                                   name="nominal" 
                                   id="nominal_input" 
                                   class="form-control @error('nominal') is-invalid @enderror" 
                                   value="{{ old('nominal') }}" 
                                   min="1" 
                                   required>
                        </div>
                        <div class="form-text">Default terisi otomatis sesuai nilai total kontrak PKS.</div>
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
                               value="{{ old('tanggal_invoice', date('Y-m-d')) }}" 
                               required>
                    </div>

                    <!-- Tanggal Jatuh Tempo -->
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_jatuh_tempo" class="form-label fw-semibold">Tanggal Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="tanggal_jatuh_tempo" 
                               id="tanggal_jatuh_tempo" 
                               class="form-control @error('tanggal_jatuh_tempo') is-invalid @enderror" 
                               value="{{ old('tanggal_jatuh_tempo', date('Y-m-d', strtotime('+30 days'))) }}" 
                               required>
                        <div class="form-text">Batas jatuh tempo pembayaran tagihan (Default: 30 hari).</div>
                    </div>
                </div>

                <!-- Kode Billing SIMPONI (Nullable) -->
                <div class="mb-4">
                    <label for="kode_billing" class="form-label fw-semibold">Kode Billing SIMPONI <span class="text-muted">(Opsional)</span></label>
                    <input type="text" 
                           name="kode_billing" 
                           id="kode_billing" 
                           class="form-control @error('kode_billing') is-invalid @enderror" 
                           value="{{ old('kode_billing') }}" 
                           placeholder="Masukkan 15 digit kode billing Simponi jika sudah ada">
                    <div class="form-text text-muted">Dapat dikosongkan dahulu dan di-input nanti saat proses pembuatan billing selesai.</div>
                </div>

                <!-- Button Actions -->
                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('invoice.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-check-circle me-1"></i> Simpan Invoice
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
        const infoCard = document.getElementById('pks_info_card');
        const infoJudul = document.getElementById('info_judul');
        const infoClient = document.getElementById('info_client');

        function updateInfo() {
            const selectedOption = pksSelect.options[pksSelect.selectedIndex];
            if (selectedOption && selectedOption.value !== '') {
                const total = selectedOption.getAttribute('data-total');
                const judul = selectedOption.getAttribute('data-judul');
                const client = selectedOption.getAttribute('data-client');

                // Isi nominal otomatis jika kosong
                if (nominalInput.value === '') {
                    nominalInput.value = total;
                }

                // Update Info Card
                infoJudul.textContent = judul;
                infoClient.textContent = client;
                infoCard.style.display = 'block';
            } else {
                infoCard.style.display = 'none';
            }
        }

        pksSelect.addEventListener('change', updateInfo);
        
        // Trigger saat halaman load pertama kali (misal jika ada error validation / edit mode)
        if (pksSelect.value !== '') {
            updateInfo();
        }
    });
</script>
@endsection
