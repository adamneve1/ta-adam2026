<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Penerimaan</title>
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
        .sign-table { width: 100%; border-collapse: collapse; margin-top: 28px; page-break-inside: avoid; }
        .sign-table td { width: 33.33%; text-align: center; vertical-align: top; padding: 0 14px; }
        .sign-position { min-height: 34px; font-weight: 700; }
        .sign-space { height: 62px; }
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
                <p class="title">Rekapitulasi Penerimaan PNBP RRI Batam</p>
                <p class="subtitle">Periode {{ $filters['tanggal_mulai_label'] }} sampai {{ $filters['tanggal_selesai_label'] }}</p>
            </td>
            <td style="width: 16%;"></td>
        </tr>
    </table>

    <table class="meta">
        <tr>
            <td style="width: 110px;">Jumlah Transaksi</td>
            <td style="width: 10px;">:</td>
            <td>{{ $payments->count() }} pembayaran</td>
            <td class="text-right">Tanggal Cetak: {{ now()->locale('id')->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Total Penerimaan</td>
            <td>:</td>
            <td colspan="2"><strong>{{ $rupiah($totalPenerimaan) }}</strong></td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th style="width: 28px;">No</th>
                <th style="width: 72px;">Tanggal</th>
                <th style="width: 118px;">Nomor Pembayaran</th>
                <th style="width: 118px;">Nomor Invoice</th>
                <th>Klien</th>
                <th style="width: 104px;">Kode Billing</th>
                <th style="width: 104px;">NTPN</th>
                <th style="width: 96px;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $index => $payment)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="nowrap">{{ $payment->tanggal_pembayaran->format('d/m/Y') }}</td>
                    <td>{{ $payment->nomor_pembayaran ?? '-' }}</td>
                    <td>
                        {{ $payment->invoice->nomor_invoice ?? '-' }}
                        <br>
                        <span>{{ $payment->invoice->pks->nomor ?? '-' }}</span>
                    </td>
                    <td>{{ $payment->invoice->pks->client->nama ?? '-' }}</td>
                    <td>{{ $payment->kode_billing ?? '-' }}</td>
                    <td>{{ $payment->ntpn ?? '-' }}</td>
                    <td class="text-right nowrap">{{ $rupiah($payment->jumlah_pembayaran) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center">Belum ada penerimaan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Penerimaan</td>
                <td class="text-right nowrap">{{ $rupiah($totalPenerimaan) }}</td>
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
