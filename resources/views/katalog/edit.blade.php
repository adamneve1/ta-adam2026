@extends('layouts.app')

@section('content')

<h3>Edit Produk</h3>

<form action="{{ route('katalog.update', $katalog->id) }}" method="POST">
@csrf
@method('PUT')

<div class="mb-3">
    <label>Nama Layanan</label>
    <input type="text" name="nama_layanan" value="{{ $katalog->nama_layanan }}" class="form-control">
</div>

<div class="mb-3">
    <label>Deskripsi</label>
    <textarea name="deskripsi" class="form-control">{{ $katalog->deskripsi }}</textarea>
</div>

<hr>

<h5>Harga</h5>

<div class="mb-3">
    <label>Harga Regular</label>
    <input type="number" name="tarif_regular" 
        value="{{ $tarifRegular->tarif ?? 0 }}" 
        class="form-control">
</div>

<div class="mb-3">
    <label>Harga Prime</label>
    <input type="number" name="tarif_prime" 
        value="{{ $tarifPrime->tarif ?? 0 }}" 
        class="form-control">
</div>

<button class="btn btn-success">Update</button>

</form>

@endsection