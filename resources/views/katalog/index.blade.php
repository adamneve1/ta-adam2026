@extends('layouts.app')

@section('content')

<h3>Katalog</h3>

<a href="{{ route('katalog.create') }}" class="btn btn-primary mb-3">
    Tambah Produk
</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Layanan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach($katalogs as $k)
        <tr>
            <td>{{ $k->nama_layanan }}</td>
            <td>
                <a href="{{ route('katalog.edit', $k->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('katalog.destroy', $k->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection