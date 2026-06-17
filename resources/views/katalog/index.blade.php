@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h2 class="mb-1">Katalog Layanan</h2>
            <p class="text-muted mb-0">Kelola daftar layanan dan tarif yang digunakan dalam kontrak PKS.</p>
        </div>
        <a href="{{ route('katalog.create') }}" class="btn btn-primary align-self-start">
            <i class="bi bi-plus-circle me-1"></i> Tambah Layanan
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('katalog.index') }}" class="row g-2">
                <div class="{{ request()->filled('keyword') ? 'col-md-8' : 'col-md-10' }}">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nama atau deskripsi layanan"
                    >
                </div>
                <div class="{{ request()->filled('keyword') ? 'col-md-2' : 'col-md-2' }} d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
                @if(request()->filled('keyword'))
                    <div class="col-md-2 d-grid">
                        <a href="{{ route('katalog.index') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Nama Layanan</th>
                            <th class="py-3">Deskripsi</th>
                            <th class="py-3 text-nowrap" style="width: 150px;">Regular</th>
                            <th class="py-3 text-nowrap" style="width: 150px;">Prime</th>
                            <th class="px-4 py-3 text-center" style="width: 90px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($katalogs as $k)
                            @php
                                $tarifRegular = optional($k->tarifs->where('waktu', 'regular')->first())->tarif;
                                $tarifPrime = optional($k->tarifs->where('waktu', 'prime')->first())->tarif;
                            @endphp
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-semibold text-dark">{{ $k->nama_layanan }}</div>
                                </td>
                                <td class="py-3 text-muted">
                                    {{ $k->deskripsi ?: '-' }}
                                </td>
                                <td class="py-3 fw-semibold text-nowrap">
                                    {{ $tarifRegular !== null ? 'Rp ' . number_format($tarifRegular, 0, ',', '.') : '-' }}
                                </td>
                                <td class="py-3 fw-semibold text-nowrap">
                                    {{ $tarifPrime !== null ? 'Rp ' . number_format($tarifPrime, 0, ',', '.') : '-' }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('katalog.edit', $k->id) }}">
                                                    <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Layanan
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    {{ request()->filled('keyword') ? 'Tidak ada layanan yang cocok dengan pencarian.' : 'Belum ada layanan katalog.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
