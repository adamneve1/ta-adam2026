@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">
    <div class="row mb-4">
        <div class="col">
            <h2 class="mb-0">Produk</h2>
        </div>
        <div class="col-auto">
            <a href="{{ route('katalog.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Nama Layanan</th>
                        <th>Regular</th>
                        <th>Prime</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($katalogs as $k)
                        <tr>
                            <td>{{ $k->nama_layanan }}</td>
                            <td>
                                {{ optional($k->tarifs->where('waktu','regular')->first())->tarif }}
                            </td>
                            <td>
                                {{ optional($k->tarifs->where('waktu','prime')->first())->tarif }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('katalog.edit',$k->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Tidak ada produk
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection