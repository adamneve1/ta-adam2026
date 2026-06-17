@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Tambah Client</h3>
            <p class="text-muted mb-0">Isi data mitra yang akan digunakan pada PKS.</p>
        </div>
        <a href="{{ route('clients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="jenis_klien" class="form-label fw-semibold">Jenis Client <span class="text-danger">*</span></label>
                        <select name="jenis_klien" id="jenis_klien" class="form-select @error('jenis_klien') is-invalid @enderror" required>
                            <option value="">Pilih jenis client</option>
                            @foreach($jenisKlien as $jenis)
                                <option value="{{ $jenis }}" {{ old('jenis_klien') === $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                            @endforeach
                        </select>
                        @error('jenis_klien')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="nama" class="form-label fw-semibold">Nama Client <span class="text-danger">*</span></label>
                        <input type="text" name="nama" id="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_narahubung" class="form-label fw-semibold">Nama Narahubung</label>
                        <input type="text" name="nama_narahubung" id="nama_narahubung" class="form-control @error('nama_narahubung') is-invalid @enderror" value="{{ old('nama_narahubung') }}">
                        @error('nama_narahubung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="no_narahubung" class="form-label fw-semibold">No. Narahubung</label>
                        <input type="text" name="no_narahubung" id="no_narahubung" class="form-control @error('no_narahubung') is-invalid @enderror" value="{{ old('no_narahubung') }}">
                        @error('no_narahubung')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="agen_rri" class="form-label fw-semibold">Agen RRI</label>
                        <input type="text" name="agen_rri" id="agen_rri" class="form-control @error('agen_rri') is-invalid @enderror" value="{{ old('agen_rri') }}">
                        @error('agen_rri')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_penanggung_jawab" class="form-label fw-semibold">Nama Penanggung Jawab</label>
                        <input type="text" name="nama_penanggung_jawab" id="nama_penanggung_jawab" class="form-control @error('nama_penanggung_jawab') is-invalid @enderror" value="{{ old('nama_penanggung_jawab') }}">
                        @error('nama_penanggung_jawab')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jabatan" class="form-label fw-semibold">Jabatan</label>
                        <input type="text" name="jabatan" id="jabatan" class="form-control @error('jabatan') is-invalid @enderror" value="{{ old('jabatan') }}">
                        @error('jabatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label fw-semibold">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="catatan" class="form-label fw-semibold">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('clients.index') }}" class="btn btn-light border">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
