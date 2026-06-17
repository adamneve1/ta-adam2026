@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Manajemen Akun</h3>
            <p class="text-muted mb-0">Kelola akun pengguna dan role akses aplikasi.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Akun
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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 60px;">No</th>
                            <th class="py-3">Nama</th>
                            <th class="py-3">Email</th>
                            <th class="py-3">NIP</th>
                            <th class="py-3">Role</th>
                            <th class="py-3">Dibuat</th>
                            <th class="px-4 py-3 text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $index => $user)
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $index + 1 }}</td>
                                <td class="py-3 fw-semibold">{{ $user->name }}</td>
                                <td class="py-3">{{ $user->email }}</td>
                                <td class="py-3 text-muted">{{ $user->nip ?: '-' }}</td>
                                <td class="py-3">
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $user->roleLabel() }}
                                    </span>
                                </td>
                                <td class="py-3 text-muted">{{ $user->created_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            <li>
                                                <a class="dropdown-item py-2" href="{{ route('users.edit', $user->id) }}">
                                                    <i class="bi bi-pencil-fill text-warning me-2"></i> Edit Akun
                                                </a>
                                            </li>
                                            @if(! $user->is(auth()->user()) && ! $user->isKepsta() && ! ($user->isAdmin() && $adminCount <= 1))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus akun ini?')">
                                                            <i class="bi bi-trash-fill me-2"></i> Hapus Akun
                                                        </button>
                                                    </form>
                                                </li>
                                            @elseif($user->isKepsta() || ($user->isAdmin() && $adminCount <= 1))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <span class="dropdown-item py-2 text-muted">
                                                        <i class="bi bi-lock-fill me-2"></i> Tidak bisa dihapus
                                                    </span>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    Belum ada akun pengguna.
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
