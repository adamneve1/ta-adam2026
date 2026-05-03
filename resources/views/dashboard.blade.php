@extends('layouts.app')

@section('title', 'Dashboard - PNBP RRI')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <!-- Welcome Card -->
    <div class="col-12 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="card-title mb-2">
                            <i class="fas fa-user-circle me-2"></i>Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}!
                        </h4>
                        <p class="card-text mb-0">Selamat bekerja di Sistem Manajemen PNBP RRI</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Katalog
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Katalog::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-list fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total PKS
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ \App\Models\Pks::count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-contract fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            PKS Bulan Ini
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Pks::whereMonth('tanggal', date('m'))->whereYear('tanggal', date('Y'))->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Pendapatan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            Rp {{ number_format(\App\Models\PksItem::sum(\DB::raw('qty * tarif')), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent PKS -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-contract me-2"></i>PKS Terbaru
                </h6>
            </div>
            <div class="card-body">
                @if(\App\Models\Pks::count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Item</th>
                                    <th>Total</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Pks::latest()->take(5)->get() as $pks)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($pks->tanggal)->format('d M Y') }}</td>
                                        <td>{{ $pks->items->count() }} item</td>
                                        <td>Rp {{ number_format($pks->items->sum(function($item) { return $item->qty * $item->tarif; }), 0, ',', '.') }}</td>
                                        <td>
                                            <a href="{{ route('pks.show', $pks) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada PKS yang dibuat</p>
                        <a href="{{ route('pks.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Buat PKS Pertama
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('pks.create') }}" class="btn btn-primary btn-custom">
                        <i class="fas fa-plus-circle me-2"></i>Buat PKS Baru
                    </a>
                    <a href="{{ route('katalog.create') }}" class="btn btn-success btn-custom">
                        <i class="fas fa-list me-2"></i>Tambah Katalog
                    </a>
                    <a href="{{ route('pks.index') }}" class="btn btn-info btn-custom">
                        <i class="fas fa-file-contract me-2"></i>Lihat Semua PKS
                    </a>
                    <a href="{{ route('katalog.index') }}" class="btn btn-secondary btn-custom">
                        <i class="fas fa-list-alt me-2"></i>Lihat Katalog
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card shadow mt-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Info Sistem
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Versi Aplikasi:</small>
                    <div class="fw-bold">1.0.0</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Terakhir Update:</small>
                    <div class="fw-bold">{{ date('d M Y') }}</div>
                </div>
                <div class="mb-0">
                    <small class="text-muted">Status:</small>
                    <div class="badge bg-success">Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
