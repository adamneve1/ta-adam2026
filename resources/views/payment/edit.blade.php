@extends('layouts.app')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
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

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('payment.update', $payment->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="p-3 bg-light rounded border mb-4">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Nomor Pembayaran</small>
                            <strong>{{ $payment->nomor_pembayaran ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Invoice</small>
                            <strong>{{ $payment->invoice->nomor_invoice ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Kontrak</small>
                            <strong>{{ $payment->invoice->pks->nomor ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6 mb-2">
                            <small class="text-muted d-block">Klien</small>
                            <strong>{{ $payment->invoice->pks->client->nama ?? '-' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Nominal Invoice</small>
                            <strong class="text-primary">Rp {{ number_format($payment->invoice->nominal ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pembayaran" class="form-label fw-semibold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pembayaran" id="tanggal_pembayaran" class="form-control @error('tanggal_pembayaran') is-invalid @enderror" value="{{ old('tanggal_pembayaran', $payment->tanggal_pembayaran->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="jumlah_pembayaran" class="form-label fw-semibold">Jumlah Pembayaran <span class="text-danger">*</span></label>
                        <input type="number" name="jumlah_pembayaran" id="jumlah_pembayaran" class="form-control @error('jumlah_pembayaran') is-invalid @enderror" min="1" value="{{ old('jumlah_pembayaran', (int) $payment->jumlah_pembayaran) }}" required>
                        <small class="text-muted">Jumlah pembayaran harus sama dengan nominal invoice.</small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kode_billing" class="form-label fw-semibold">Kode Billing</label>
                        <input type="text" name="kode_billing" id="kode_billing" class="form-control @error('kode_billing') is-invalid @enderror" value="{{ old('kode_billing', $payment->kode_billing) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ntpn" class="form-label fw-semibold">NTPN</label>
                        <input type="text" name="ntpn" id="ntpn" class="form-control @error('ntpn') is-invalid @enderror" value="{{ old('ntpn', $payment->ntpn) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label fw-semibold">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan', $payment->catatan) }}</textarea>
                </div>

                <div class="mb-4">
                    <label for="bukti_pembayaran" class="form-label fw-semibold">Upload Bukti Pembayaran Baru <span class="text-muted">(Opsional)</span></label>
                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="form-control @error('bukti_pembayaran') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">Kosongkan jika tidak ingin mengganti bukti pembayaran.</small>
                </div>

                <h6 class="fw-semibold mb-3">Penandatangan Kwitansi</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="kwitansi_penyetor_nama" class="form-label fw-semibold">Nama Penyetor Kwitansi <span class="text-danger">*</span></label>
                        <input type="text" name="kwitansi_penyetor_nama" id="kwitansi_penyetor_nama" class="form-control @error('kwitansi_penyetor_nama') is-invalid @enderror" value="{{ old('kwitansi_penyetor_nama', $payment->kwitansi_penyetor_nama) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="kwitansi_penyetor_nip" class="form-label fw-semibold">NIP Penyetor Kwitansi <span class="text-danger">*</span></label>
                        <input type="text" name="kwitansi_penyetor_nip" id="kwitansi_penyetor_nip" class="form-control @error('kwitansi_penyetor_nip') is-invalid @enderror" value="{{ old('kwitansi_penyetor_nip', $payment->kwitansi_penyetor_nip) }}" inputmode="numeric" pattern="[0-9]{18}" minlength="18" maxlength="18" placeholder="18 digit angka" required>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="kwitansi_kepala_stasiun_nama" class="form-label fw-semibold">Nama Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                        <input type="text" name="kwitansi_kepala_stasiun_nama" id="kwitansi_kepala_stasiun_nama" class="form-control @error('kwitansi_kepala_stasiun_nama') is-invalid @enderror" value="{{ old('kwitansi_kepala_stasiun_nama', $payment->kwitansi_kepala_stasiun_nama) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="kwitansi_kepala_stasiun_nip" class="form-label fw-semibold">NIP Kepala Stasiun Kwitansi <span class="text-danger">*</span></label>
                        <input type="text" name="kwitansi_kepala_stasiun_nip" id="kwitansi_kepala_stasiun_nip" class="form-control @error('kwitansi_kepala_stasiun_nip') is-invalid @enderror" value="{{ old('kwitansi_kepala_stasiun_nip', $payment->kwitansi_kepala_stasiun_nip) }}" inputmode="numeric" pattern="[0-9]{18}" minlength="18" maxlength="18" placeholder="18 digit angka" required>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 border-top pt-3">
                    <a href="{{ route('payment.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
