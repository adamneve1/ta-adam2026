@extends('layouts.app')

@section('content')

<h3>Daftar PKS</h3>

<a href="{{ route('pks.create') }}" class="btn btn-primary mb-3">
    + Buat PKS
</a>

<table class="table table-bordered">
<thead>
<tr>
    <th>No</th>
    <th>Nomor</th>
    <th>Judul</th>
    <th>Client</th>
    <th>Tanggal</th>
    <th>Total</th>
    <th>Aksi</th>
</tr>
</thead>

<tbody>
@foreach($pks as $i => $p)
<tr>
    <td>{{ $i+1 }}</td>
    <td>{{ $p->nomor }}</td>
    <td>{{ $p->judul }}</td>
    <td>{{ $p->client->nama ?? '-' }}</td>
    <td>{{ $p->tanggal }}</td>
    <td>Rp {{ number_format($p->total) }}</td>

    <td>
        <a href="{{ route('pks.cetak', $p->id) }}" target="_blank" class="btn btn-success btn-sm">
            Cetak
        </a>
    </td>
</tr>
@endforeach
</tbody>
</table>


@endsection