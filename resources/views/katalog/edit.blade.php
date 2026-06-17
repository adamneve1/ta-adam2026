@extends('layouts.app')

@section('content')

<div class="container-fluid mx-auto" style="max-width: 800px;">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Edit Layanan Katalog</h2>
            <p class="text-muted mb-0">Perbarui detail layanan dan tarif katalog.</p>
        </div>
        <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary align-self-start">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Periksa kembali input:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('katalog.update', $katalog->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Informasi Layanan</h5>

                <div class="mb-3">
                    <label for="nama_layanan" class="form-label">Nama Layanan</label>
                    <input
                        type="text"
                        id="nama_layanan"
                        name="nama_layanan"
                        class="form-control @error('nama_layanan') is-invalid @enderror"
                        value="{{ old('nama_layanan', $katalog->nama_layanan) }}"
                        placeholder="Contoh: Spot iklan radio"
                    >
                    @error('nama_layanan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea
                        id="deskripsi"
                        name="deskripsi"
                        rows="4"
                        class="form-control @error('deskripsi') is-invalid @enderror"
                        placeholder="Tambahkan keterangan singkat layanan"
                    >{{ old('deskripsi', $katalog->deskripsi) }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="border-top pt-4">
                    <h5 class="mb-3">Harga</h5>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="tarif_regular" class="form-label">Harga Regular</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input
                                    type="number"
                                    id="tarif_regular"
                                    name="tarif_regular"
                                    class="form-control @error('tarif_regular') is-invalid @enderror"
                                    value="{{ old('tarif_regular', $tarifRegular->tarif ?? 0) }}"
                                    min="0"
                                    placeholder="0"
                                >
                                @error('tarif_regular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="tarif_prime" class="form-label">Harga Prime</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input
                                    type="number"
                                    id="tarif_prime"
                                    name="tarif_prime"
                                    class="form-control @error('tarif_prime') is-invalid @enderror"
                                    value="{{ old('tarif_prime', $tarifPrime->tarif ?? 0) }}"
                                    min="0"
                                    placeholder="0"
                                >
                                @error('tarif_prime')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-end gap-2">
                <a href="{{ route('katalog.index') }}" class="btn btn-light border">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i> Simpan Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

@endsection
