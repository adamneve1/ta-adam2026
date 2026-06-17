<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Kerja Sama PNBP</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: Arial, sans-serif; font-size: 10px; color: #111; }
        .header { width: 100%; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 14px; }
        .logo { width: 92px; height: auto; }
        .title { text-align: center; font-size: 16px; font-weight: 700; text-transform: uppercase; margin: 0 0 4px 0; }
        .subtitle { text-align: center; font-size: 11px; margin: 0; }
        .meta { width: 100%; margin: 10px 0 12px 0; border-collapse: collapse; }
        .meta td { padding: 2px 0; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #333; padding: 5px 4px; vertical-align: top; }
        .table th { background: #e9ecef; text-align: center; font-weight: 700; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
        .total-row th, .total-row td { background: #f3f4f6; font-weight: 700; }
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 30px; page-break-inside: avoid; }
        .sign-table td { width: 50%; text-align: center; vertical-align: top; padding: 0 36px; }
        .sign-position { min-height: 34px; font-weight: 700; }
        .sign-space { height: 68px; }
        .sign-name { font-weight: 700; text-decoration: underline; margin-bottom: 3px; }
    </style>
</head>
<body>
    @php
        $rupiah = fn ($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <table class="header">
        <tr>
            <td style="width: 16%;">
                <img src="{{ public_path('images/RRI_Logo.png') }}" class="logo" alt="Logo RRI">
            </td>
            <td style="width: 68%;">
                <p class="title">Rekapitulasi Kerja Sama PNBP RRI Batam</p>
                <p class="subtitle">Periode {{ $filters['tanggal_mulai_label'] }} sampai {{ $filters['tanggal_selesai_label'] }}</p>
            </td>
            <td style="width: 16%;"></td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td style="width: 120px;">Jumlah PKS</td>
            <td style="width: 10px;">:</td>
            <td>{{ $pksList->count() }} kontrak</td>
            <td class="text-right">Tanggal Cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Total Nilai Kerja Sama</td>
            <td>:</td>
            <td colspan="2"><strong>{{ $rupiah($totalNilaiKerjaSama) }}</strong></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 72px;">Tanggal</th>
                <th style="width: 120px;">Nomor PKS</th>
                <th>Judul Kerja Sama</th>
                <th style="width: 130px;">Mitra</th>
                <th style="width: 150px;">Layanan</th>
                <th style="width: 90px;">Status</th>
                <th style="width: 96px;">Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pksList as $index => $pks)
                @php
                    $invoice = $pks->invoices->first();
                    $layanan = $pks->items->pluck('katalog.nama_layanan')->filter()->unique()->values();
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ $pks->tanggal->format('d/m/Y') }}</td>
                    <td>{{ $pks->nomor }}</td>
                    <td>{{ $pks->judul }}</td>
                    <td>{{ $pks->client->nama ?? '-' }}</td>
                    <td>{{ $layanan->implode(', ') ?: '-' }}</td>
                    <td class="text-center">{{ $invoice ? $invoice->statusLabel() : 'Belum Invoice' }}</td>
                    <td class="text-right nowrap">{{ $rupiah($pks->total) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada kerja sama PNBP pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Nilai Kerja Sama</td>
                <td class="text-right nowrap">{{ $rupiah($totalNilaiKerjaSama) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="sign-table">
        <tr>
            @foreach($signers as $signer)
                <td>
                    <div class="sign-position">{{ $signer['posisi'] }}</div>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ $signer['nama'] }}</div>
                    <div>NIP. {{ $signer['nip'] }}</div>
                </td>
            @endforeach
        </tr>
    </table>
</body>
</html>
