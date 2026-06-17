@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Data Client</h3>
            <p class="text-muted mb-0">Kelola data mitra yang digunakan saat membuat PKS.</p>
        </div>
        <a href="{{ route('clients.create') }}" class="btn btn-primary align-self-start">
            <i class="bi bi-plus-circle me-1"></i> Tambah Client
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('clients.index') }}" class="row g-2">
                <div class="col-md-10">
                    <input
                        type="text"
                        name="keyword"
                        class="form-control"
                        value="{{ request('keyword') }}"
                        placeholder="Cari nama client, email, kontak, atau nomor HP"
                    >
                </div>
                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama Client</th>
                            <th class="py-3">Jenis</th>
                            <th class="py-3">Narahubung</th>
                            <th class="py-3">Email</th>
                            <th class="py-3 text-center">PKS</th>
                            <th class="px-4 py-3 text-center" style="width: 130px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clients as $client)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $clients->firstItem() + $loop->index }}</td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">{{ $client->nama }}</div>
                                    <small class="text-muted">{{ $client->alamat ?: '-' }}</small>
                                </td>
                                <td class="py-3">{{ $client->jenis_klien }}</td>
                                <td class="py-3">
                                    <div>{{ $client->nama_narahubung ?: '-' }}</div>
                                    <small class="text-muted">{{ $client->no_narahubung ?: '-' }}</small>
                                </td>
                                <td class="py-3">{{ $client->email ?: '-' }}</td>
                                <td class="py-3 text-center">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $client->pks_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('clients.edit', $client->id) }}">
                                                    <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Client
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('clients.destroy', $client->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus data client ini?')">
                                                        <i class="bi bi-trash-fill me-2"></i> Hapus Client
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Belum ada data client.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $clients->links() }}
    </div>
</div>
@endsection
