@extends('layouts.app')

@section('content')
@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag;
    $previewRows = $previewRows ?? [];
    $validRows = $validRows ?? [];
    $validCount = collect($previewRows)->where('is_valid', true)->count();
    $invalidCount = collect($previewRows)->where('is_valid', false)->count();
    $errorSummary = collect($previewRows)
        ->flatMap(fn ($row) => $row['errors'] ?? [])
        ->countBy()
        ->sortDesc();
    $primaryError = function (array $errors): string {
        $priority = [
            'Invoice dengan kode billing ini belum dibuat.',
            'Invoice sudah lunas.',
            'Invoice sudah memiliki pembayaran.',
            'Nominal setoran tidak sama dengan nominal invoice.',
            'NTPN sudah pernah diimport/dicatat.',
            'NTPN duplikat di file CSV.',
            'Kode billing kosong.',
            'Tanggal bayar tidak terbaca.',
            'NTPN kosong.',
            'Setoran per akun kosong atau tidak valid.',
        ];

        foreach ($priority as $message) {
            if (in_array($message, $errors, true)) {
                return $message;
            }
        }

        return $errors[0] ?? 'Data perlu dicek.';
    };
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
@endphp

<div class="container-fluid">
    <div class="d-flex flex-column flex-lg-row justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('payment.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke List Pembayaran
            </a>
            <h3 class="mb-1 text-gray-800">Import Rekap SIMPONI</h3>
            <p class="text-muted mb-0">Upload CSV rekap SIMPONI dari Kemenkeu, cek preview validasi, lalu import baris yang valid.</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Periksa file CSV.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-xl-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Upload CSV</h5>
                    <small class="text-muted">Gunakan file CSV dari rekap pembayaran/penyetoran SIMPONI.</small>
                </div>
                <div class="card-body">
                    <form action="{{ route('payment.import-simponi.preview') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label fw-semibold">File CSV SIMPONI</label>
                            <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt" required>
                            <small class="text-muted">Jika masih Excel, simpan dulu sebagai CSV dari Microsoft Excel.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Preview Data
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Kolom yang Dibaca</h5>
                    <small class="text-muted">Nama kolom mengikuti rekap SIMPONI.</small>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach(['TANGGAL BAYAR', 'KODE BILLING', 'NTPN', 'NTB/NTP', 'SETORAN PER AKUN (Rp)', 'KETERANGAN'] as $column)
                            <div class="col-12 col-md-6">
                                <div class="border rounded px-3 py-2 bg-light small fw-semibold">{{ $column }}</div>
                            </div>
                        @endforeach
                    </div>
                    <div class="alert alert-info py-2 small mt-3 mb-0">
                        Baris valid harus punya invoice internal yang sudah dibuat, kode billing cocok, nominal sama, invoice belum lunas, dan NTPN belum pernah dicatat.
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($previewRows))
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-semibold text-uppercase">Total Baris</div>
                        <div class="fs-4 fw-bold text-dark">{{ count($previewRows) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-semibold text-uppercase">Valid</div>
                        <div class="fs-4 fw-bold text-success">{{ $validCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small fw-semibold text-uppercase">Perlu Dicek</div>
                        <div class="fs-4 fw-bold text-danger">{{ $invalidCount }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if($errorSummary->isNotEmpty())
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Ringkasan Perlu Dicek</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($errorSummary as $message => $count)
                            <span class="badge bg-light text-dark border px-3 py-2">
                                {{ $message }} <span class="text-danger fw-bold">{{ $count }}</span>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex flex-column flex-lg-row justify-content-between gap-2">
                <div>
                    <h5 class="mb-0">Preview Import</h5>
                    <small class="text-muted">Hanya baris valid yang akan disimpan sebagai pembayaran.</small>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <form action="{{ route('payment.import-simponi.reset') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Bersihkan Preview
                        </button>
                    </form>

                    <form action="{{ route('payment.import-simponi.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success" @disabled($validCount === 0)>
                            <i class="bi bi-check-circle me-1"></i> Import {{ $validCount }} Data Valid
                        </button>
                    </form>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-4 py-3">Baris</th>
                                <th class="py-3">Kode Billing</th>
                                <th class="py-3">Invoice</th>
                                <th class="py-3">Klien</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">NTPN</th>
                                <th class="py-3 text-end">Setoran</th>
                                <th class="py-3 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewRows as $row)
                                <tr>
                                    <td class="px-4 py-3 text-muted">{{ $row['row_number'] }}</td>
                                    <td class="py-3"><code>{{ $row['kode_billing'] ?: '-' }}</code></td>
                                    <td class="py-3">
                                        <span class="fw-semibold">{{ $row['invoice_nomor'] }}</span>
                                        @if($row['invoice_nominal'] > 0)
                                            <small class="text-muted d-block">Invoice: {{ $rupiah($row['invoice_nominal']) }}</small>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ $row['client'] }}</td>
                                    <td class="py-3">{{ $row['tanggal_pembayaran'] ? \Carbon\Carbon::parse($row['tanggal_pembayaran'])->locale('id')->translatedFormat('d M Y') : '-' }}</td>
                                    <td class="py-3">{{ $row['ntpn'] ?: '-' }}</td>
                                    <td class="py-3 text-end fw-semibold">{{ $rupiah($row['jumlah_pembayaran']) }}</td>
                                    <td class="py-3 text-center">
                                        @if($row['is_valid'])
                                            <span class="badge bg-success text-white">Valid</span>
                                        @else
                                            <span class="badge bg-danger text-white mb-1">Tidak Valid</span>
                                            <div class="small text-muted">
                                                {{ $primaryError($row['errors'] ?? []) }}
                                                @if(count($row['errors'] ?? []) > 1)
                                                    <span class="d-block text-secondary">+{{ count($row['errors']) - 1 }} alasan lain</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
