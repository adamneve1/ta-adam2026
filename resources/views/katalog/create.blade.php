@extends('layouts.app')

@section('content')

<h3>Tambah Produk</h3>

<form action="{{ route('katalog.store') }}" method="POST">
@csrf

<div class="mb-3">
    <label>Nama Layanan</label>
    <input type="text" name="nama_layanan" class="form-control" required>
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control"></textarea>
</div>

<button class="btn btn-success">Simpan</button>
<a href="{{ route('katalog.index') }}" class="btn btn-secondary">Kembali</a>

</form>

@endsection