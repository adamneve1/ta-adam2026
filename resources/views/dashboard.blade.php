@extends('layouts.app')

@section('title', 'Dashboard - PNBP RRI')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
@php
    $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    $canViewPks = auth()->user()->isLpu() || auth()->user()->isKepsta();
    $canViewInvoices = auth()->user()->isPenyetor() || auth()->user()->isKepsta();

    $periodOptions = [
        'month' => 'Bulan',
        'quarter' => 'Triwulan',
        'year' => 'Tahun',
        'custom' => 'Custom',
    ];

    $summaryCards = [
        [
            'label' => 'Kontrak PNBP',
            'value' => $rupiah($stats['nilai_kontrak']),
            'meta' => $stats['total_pks'] . ' PKS pada ' . strtolower($periodLabel),
            'icon' => 'bi-file-earmark-text',
        ],
        [
            'label' => 'Sudah Terealisasi',
            'value' => $rupiah($stats['penerimaan_periode']),
            'meta' => 'PNBP sudah dibayar',
            'tone' => 'text-success',
            'icon' => 'bi-check2-circle',
        ],
        [
            'label' => 'Belum Terealisasi',
            'value' => $rupiah($stats['belum_terealisasi']),
            'meta' => 'Kontrak belum menjadi penerimaan',
            'tone' => $stats['belum_terealisasi'] > 0 ? 'text-danger' : 'text-success',
            'icon' => 'bi-hourglass-split',
        ],
        [
            'label' => 'Capaian Realisasi',
            'value' => $stats['realisasi_penerimaan'] . '%',
            'meta' => 'Dari kontrak PNBP periode ini',
            'tone' => $stats['realisasi_penerimaan'] >= 100 ? 'text-success' : '',
            'progress' => $stats['realisasi_penerimaan'],
            'icon' => 'bi-graph-up-arrow',
        ],
    ];

    $priorityItems = array_values(array_filter([
        $canViewPks ? [
            'label' => 'PKS belum invoice',
            'value' => $stats['pks_belum_invoice'],
            'href' => route('pks.index', ['status' => 'no_invoice']),
            'icon' => 'bi-file-earmark-plus',
        ] : null,
        $canViewInvoices ? [
            'label' => 'Menunggu pembayaran',
            'value' => $stats['invoice_menunggu_bayar'],
            'href' => route('invoice.index', ['status' => 'menunggu_pembayaran']),
            'icon' => 'bi-credit-card',
        ] : null,
        $canViewInvoices ? [
            'label' => 'Invoice lewat tempo',
            'value' => $stats['invoice_jatuh_tempo'],
            'href' => route('invoice.index', ['status' => 'overdue']),
            'tone' => 'text-danger',
            'icon' => 'bi-calendar-x',
        ] : null,
    ]));

    $needsAction = collect($priorityItems)->sum('value');
@endphp

<style>
    .dashboard-page {
        --dash-border: #e7eaf0;
        --dash-muted: #6b7280;
        --dash-ink: #111827;
        color: var(--dash-ink);
    }

    .dashboard-page .panel {
        background: #fff;
        border: 1px solid var(--dash-border);
        border-radius: 8px;
    }

    .dashboard-page .panel-header {
        padding: 1rem 1rem .75rem;
        border-bottom: 1px solid var(--dash-border);
    }

    .dashboard-page .metric-label {
        color: var(--dash-muted);
        font-size: .74rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .dashboard-page .metric-value {
        margin-top: .45rem;
        color: var(--dash-ink);
        font-size: clamp(1.45rem, 1.1rem + .8vw, 1.9rem);
        font-weight: 700;
        line-height: 1.15;
    }

    .dashboard-page .metric-value.text-success {
        color: var(--bs-success) !important;
    }

    .dashboard-page .metric-value.text-danger {
        color: var(--bs-danger) !important;
    }

    .dashboard-page .metric-icon {
        align-items: center;
        background: #f8fafc;
        border: 1px solid var(--dash-border);
        border-radius: 8px;
        color: var(--dash-muted);
        display: inline-flex;
        flex-shrink: 0;
        height: 34px;
        justify-content: center;
        width: 34px;
    }

    .dashboard-page .metric-icon.text-success {
        background: #ecfdf3;
        border-color: #bbf7d0;
        color: var(--bs-success) !important;
    }

    .dashboard-page .metric-icon.text-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: var(--bs-danger) !important;
    }

    .dashboard-page .muted-text {
        color: var(--dash-muted);
        font-size: .88rem;
    }

    .dashboard-page .priority-link {
        color: var(--dash-ink);
        padding: .9rem 0;
        border-bottom: 1px solid var(--dash-border);
    }

    .dashboard-page .priority-link:last-child {
        border-bottom: 0;
    }

    .dashboard-page .priority-icon {
        align-items: center;
        background: #f8fafc;
        border: 1px solid var(--dash-border);
        border-radius: 7px;
        color: var(--dash-muted);
        display: inline-flex;
        flex-shrink: 0;
        height: 30px;
        justify-content: center;
        width: 30px;
    }

    .dashboard-page .priority-icon.text-danger {
        background: #fef2f2;
        border-color: #fecaca;
        color: var(--bs-danger) !important;
    }

    .dashboard-page .section-icon {
        color: var(--bs-primary);
        font-size: 1rem;
    }

    .dashboard-page .custom-range-form {
        align-items: center;
        background: transparent;
        border: 0;
        display: flex;
        gap: .5rem;
        min-height: 34px;
        padding: 0;
    }

    .dashboard-page .custom-range-form .form-control {
        background-color: #fff;
        border: 1px solid var(--dash-border);
        border-radius: 6px;
        color: var(--dash-ink);
        font-size: .84rem;
        height: 34px;
        max-width: 146px;
        padding-bottom: 0;
        padding-top: 0;
    }

    .dashboard-page .custom-range-form .btn {
        height: 34px;
        line-height: 1;
        padding-bottom: 0;
        padding-top: 0;
    }

    .dashboard-page .min-w-0 {
        min-width: 0;
    }

    .dashboard-page .soft-badge {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .dashboard-page .btn-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .dashboard-page .filter-row {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-top: .9rem;
    }

    .dashboard-page .dashboard-toolbar {
        align-items: flex-start;
        display: flex;
        justify-content: flex-start;
    }

    .dashboard-page .filter-shell {
        background: transparent;
        border: 0;
        border-radius: 0;
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        min-height: 34px;
        padding: 0;
    }

    .dashboard-page .filter-shell form {
        align-items: center;
        display: flex;
        flex-shrink: 0;
    }

    .dashboard-page .period-filter {
        display: inline-flex;
        gap: .15rem;
    }

    .dashboard-page .period-filter .btn {
        border: 0;
        border-radius: 6px;
        color: var(--dash-muted);
        font-size: .84rem;
        height: 34px;
        line-height: 1;
        padding: 0 .7rem;
    }

    .dashboard-page .period-filter .btn.active {
        background: #eef2f7;
        color: var(--dash-ink);
        font-weight: 600;
    }

    .dashboard-page .year-filter {
        background-color: #fff;
        border: 1px solid var(--dash-border);
        border-radius: 6px;
        color: var(--dash-ink);
        font-size: .84rem;
        height: 34px;
        min-width: 104px;
        padding-bottom: 0;
        padding-top: 0;
    }

    .dashboard-page .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
    }

    .dashboard-page .action-group .btn {
        min-height: 38px;
    }

    @media (min-width: 1200px) {
        .dashboard-page .dashboard-toolbar {
            justify-content: flex-end;
        }
    }

    @media (max-width: 575.98px) {
        .dashboard-page .filter-shell,
        .dashboard-page .filter-row,
        .dashboard-page .dashboard-toolbar,
        .dashboard-page .action-group,
        .dashboard-page .custom-range-form {
            width: 100%;
        }

        .dashboard-page .filter-shell {
            display: flex;
        }

        .dashboard-page .period-filter {
            flex: 1;
        }

        .dashboard-page .period-filter .btn {
            flex: 1;
            padding-left: .45rem;
            padding-right: .45rem;
        }

        .dashboard-page .action-group .btn {
            flex: 1;
        }

        .dashboard-page .custom-range-form {
            flex-wrap: wrap;
        }

        .dashboard-page .custom-range-form .form-control {
            flex: 1 1 120px;
            max-width: none;
        }
    }

    .dashboard-page .mini-progress {
        background: #eef2f7;
        border-radius: 999px;
        height: 6px;
        overflow: hidden;
    }

    .dashboard-page .mini-progress-bar {
        background: var(--bs-primary);
        border-radius: inherit;
        height: 100%;
    }
</style>

<div class="container-fluid dashboard-page">
    <div class="d-flex flex-column flex-xl-row justify-content-between gap-3 mb-4">
        <div>
            <div class="metric-label mb-2">Dashboard</div>
            <h3 class="mb-1">Selamat datang, {{ auth()->user()->name ?? 'Admin' }}</h3>
            <p class="muted-text mb-0">Ringkasan singkat untuk memantau pekerjaan hari ini.</p>

            <div class="filter-row">
                <div class="filter-shell" aria-label="Filter dashboard">
                    <form method="GET" action="{{ route('dashboard') }}">
                        <input type="hidden" name="period" value="{{ $period }}">
                        @if($period === 'custom')
                            <input type="hidden" name="start_date" value="{{ $startDateInput }}">
                            <input type="hidden" name="end_date" value="{{ $endDateInput }}">
                        @endif
                        <select name="year" class="form-select form-select-sm year-filter" aria-label="Tahun anggaran" onchange="this.form.submit()">
                            @foreach($yearOptions as $year)
                                <option value="{{ $year }}" @selected($selectedYear === $year)>TA {{ $year }}</option>
                            @endforeach
                        </select>
                    </form>

                    <div class="period-filter" aria-label="Periode dashboard">
                        @foreach($periodOptions as $optionValue => $optionLabel)
                            @php
                                $periodQuery = ['year' => $selectedYear, 'period' => $optionValue];
                                if ($optionValue === 'custom') {
                                    $periodQuery['start_date'] = $startDateInput;
                                    $periodQuery['end_date'] = $endDateInput;
                                }
                            @endphp
                            <a href="{{ route('dashboard', $periodQuery) }}"
                               class="btn btn-sm d-inline-flex align-items-center justify-content-center {{ $period === $optionValue ? 'active' : '' }}"
                               aria-current="{{ $period === $optionValue ? 'page' : 'false' }}">
                                {{ $optionLabel }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($period === 'custom')
                    <form method="GET" action="{{ route('dashboard') }}" class="custom-range-form" aria-label="Custom range dashboard">
                        <input type="hidden" name="period" value="custom">
                        <input type="hidden" name="year" value="{{ $selectedYear }}">
                        <input type="date" name="start_date" value="{{ $startDateInput }}" class="form-control form-control-sm" aria-label="Tanggal mulai" required>
                        <span class="muted-text">s/d</span>
                        <input type="date" name="end_date" value="{{ $endDateInput }}" class="form-control form-control-sm" aria-label="Tanggal akhir" required>
                        <button type="submit" class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center">
                            Terapkan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="dashboard-toolbar">
            <div class="action-group">
                @if(auth()->user()->isLpu())
                    <a href="{{ route('pks.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-file-earmark-plus me-1"></i> Buat PKS
                    </a>
                @endif
                @if(auth()->user()->isPenyetor())
                    <a href="{{ route('invoice.create') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-receipt-cutoff me-1"></i> Buat Invoice
                    </a>
                    <a href="{{ route('payment.create') }}" class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-credit-card me-1"></i> Catat Bayar
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @foreach($summaryCards as $card)
            <div class="col-12 col-md-6 col-xl-3">
                <div class="panel h-100 p-3">
                    <div class="d-flex align-items-start justify-content-between gap-3">
                        <div class="metric-label">{{ $card['label'] }}</div>
                        <span class="metric-icon {{ $card['tone'] ?? '' }}" aria-hidden="true">
                            <i class="bi {{ $card['icon'] }}"></i>
                        </span>
                    </div>
                    <div class="metric-value {{ $card['tone'] ?? '' }}">{{ $card['value'] }}</div>
                    <div class="muted-text mt-2">{{ $card['meta'] }}</div>
                    @isset($card['progress'])
                        <div class="mini-progress mt-3" role="progressbar" aria-label="Realisasi penerimaan" aria-valuenow="{{ $card['progress'] }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="mini-progress-bar" style="width: {{ $card['progress'] }}%;"></div>
                        </div>
                    @endisset
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-12 {{ $canViewInvoices ? 'col-lg-5' : '' }}">
            <section class="panel h-100">
                <div class="panel-header">
                    <h5 class="mb-1 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-circle section-icon" aria-hidden="true"></i>
                        Belum Terealisasi
                    </h5>
                    <div class="muted-text">{{ $needsAction }} item perlu dipantau.</div>
                </div>
                <div class="px-3">
                    @forelse($priorityItems as $item)
                        <a href="{{ $item['href'] }}" class="priority-link d-flex align-items-center justify-content-between gap-3 text-decoration-none">
                            <span class="d-flex align-items-center gap-2 min-w-0">
                                <span class="priority-icon {{ $item['tone'] ?? '' }}" aria-hidden="true">
                                    <i class="bi {{ $item['icon'] }}"></i>
                                </span>
                                <span class="{{ $item['tone'] ?? '' }}">{{ $item['label'] }}</span>
                            </span>
                            <span class="badge rounded-pill soft-badge">{{ $item['value'] }}</span>
                        </a>
                    @empty
                        <div class="text-center text-muted py-5">Tidak ada item operasional untuk role ini.</div>
                    @endforelse
                </div>
            </section>
        </div>

        @if($canViewInvoices)
            <div class="col-12 col-lg-7">
                <section class="panel h-100">
                    <div class="panel-header d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="mb-1 d-flex align-items-center gap-2">
                                <i class="bi bi-receipt section-icon" aria-hidden="true"></i>
                                Invoice Terbaru
                            </h5>
                            <div class="muted-text">3 invoice terakhir pada {{ strtolower($periodLabel) }}.</div>
                        </div>
                        <a href="{{ route('invoice.index') }}" class="btn btn-light border btn-sm btn-icon" title="Lihat semua invoice">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>

                    <div class="list-group list-group-flush">
                        @forelse($recentInvoices as $invoice)
                            <a href="{{ route('invoice.show', $invoice->id) }}" class="list-group-item list-group-item-action px-3 py-3">
                                <div class="d-flex justify-content-between gap-3">
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $invoice->nomor_invoice }}</div>
                                        <small class="text-muted d-block text-truncate">{{ $invoice->pks->client->nama ?? '-' }}</small>
                                    </div>
                                    <span class="badge {{ $invoice->statusBadgeClass() }} align-self-start">{{ $invoice->statusLabel() }}</span>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-muted py-5">Belum ada invoice yang dibuat.</div>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </div>
</div>
@endsection
