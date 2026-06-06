@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1 text-gray-800">Daftar PKS</h3>
            <p class="text-muted mb-0">Manajemen Perjanjian Kerja Sama (PKS).</p>
        </div>
        @if(auth()->user()->isLpu())
            <a href="{{ route('pks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> Buat PKS
            </a>
        @endif
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

   <div class="mb-4">
    <form method="GET" action="{{ route('pks.index') }}">
        <div class="row g-2">

            <div class="col">
                <input
                    type="text"
                    name="keyword"
                    class="form-control form-control-sm"
                    value="{{ request('keyword') }}"
                    placeholder="Cari nomor, referensi, judul, client..."
                >
            </div>

            <div class="col-auto">
                <input
                    type="date"
                    name="tanggal"
                    class="form-control form-control-sm"
                    value="{{ request('tanggal') }}"
                >
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">
                    Filter
                </button>
            </div>

            @if(request()->filled('keyword') || request()->filled('tanggal'))
                <div class="col-auto">
                    <a href="{{ route('pks.index') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            @endif

        </div>
    </form>
</div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow: visible;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 50px;">No</th>
                            <th class="py-3">Nomor / Referensi</th>
                            <th class="py-3">Judul</th>
                            <th class="py-3">Client</th>
                            <th class="py-3">Tanggal</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Sisa Kontrak</th>
                            <th class="py-3">Status Tagihan</th>
                            <th class="px-4 py-3 text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pks as $i => $p)
                            @php
                                $totalDitagihkan = (float) ($p->total_ditagihkan ?? 0);
                                $sisaKontrak = max((float) $p->total - $totalDitagihkan, 0);
                                $isLunas = $p->total > 0 && $sisaKontrak <= 0;
                                $hasInvoice = $p->invoices_count > 0;

                                $statusLabel = 'Belum Ditagih';
                                $statusClass = 'bg-secondary';

                                if ($isLunas) {
                                    $statusLabel = 'Tertagih Penuh';
                                    $statusClass = 'bg-success';
                                } elseif ($hasInvoice) {
                                    $statusLabel = 'Sebagian Ditagih';
                                    $statusClass = 'bg-warning text-dark';
                                }

                            @endphp
                            <tr>
                                <td class="px-4 py-3 text-muted">{{ $pks->firstItem() + $i }}</td>
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="fw-semibold">{{ $p->nomor }}</span>
                                        <small class="text-muted">Referensi: {{ $p->nomor_referensi ?: '-' }}</small>
                                    </div>
                                </td>
                                <td class="py-3">{{ $p->judul }}</td>
                                <td class="py-3">{{ $p->client->nama ?? '-' }}</td>
                                <td class="py-3">{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                                <td class="py-3 fw-bold text-gray-900">Rp {{ number_format($p->total, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <div class="d-flex flex-column gap-1">
                                        <span class="fw-semibold">Rp {{ number_format($sisaKontrak, 0, ',', '.') }}</span>
                                        <small class="text-muted">Ditagih: Rp {{ number_format($totalDitagihkan, 0, ',', '.') }}</small>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="dropdown">
                                        <!-- Tombol Pemicu Dropdown (Titik 3 Vertikal) -->
                                        <button class="btn btn-link text-secondary p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical fs-5"></i>
                                        </button>
                                        
                                        <!-- Menu Dropdown -->
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border">
                                            @if(auth()->user()->isLpu())
                                                <li>
                                                    <a class="dropdown-item py-2" href="{{ route('pks.edit', $p->id) }}">
                                                        <i class="bi bi-pencil-fill text-warning me-2"></i> Edit PKS
                                                    </a>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item py-2 text-success" href="{{ route('pks.cetak', $p->id) }}" target="_blank">
                                                    <i class="bi bi-printer-fill me-2"></i> Cetak PKS
                                                </a>
                                            </li>
                                            @if(auth()->user()->isLpu())
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('pks.destroy', $p->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item py-2 text-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus PKS ini?')">
                                                            <i class="bi bi-trash-fill me-2"></i> Hapus PKS
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-file-earmark-text fs-1 d-block mb-3 opacity-50"></i>
                                    {{ request()->filled('keyword') || request()->filled('tanggal') ? 'Tidak ada PKS yang cocok dengan filter saat ini.' : 'Belum ada PKS yang dibuat.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($pks->hasPages())
        <div class="d-flex justify-content-end mt-3">
            {{ $pks->links() }}
        </div>
    @endif
</div>
@endsection
