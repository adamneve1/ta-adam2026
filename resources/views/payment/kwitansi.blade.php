<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran - {{ $payment->invoice->nomor_invoice ?? $payment->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; line-height: 1.45; }
        .wrapper { border: 1px solid #111; padding: 20px; }
        .head { width: 100%; margin-bottom: 14px; }
        .logo { width: 120px; height: auto; }
        .title { text-align: center; font-size: 34px; font-weight: 700; margin: 2px 0 12px 0; text-decoration: underline; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 4px 2px; vertical-align: top; font-size: 14px; }
        .label { width: 200px; }
        .colon { width: 10px; text-align: center; }
        .value-strong { font-weight: 700; }
        .detail-box { border: 1px solid #111; padding: 14px 12px; margin-top: 8px; }
        .sign-table { width: 100%; margin-top: 28px; border-collapse: collapse; }
        .sign-table td { width: 50%; vertical-align: top; text-align: center; }
        .sign-space { height: 80px; }
        .lunas {
            margin-top: 12px;
            display: inline-block;
            padding: 6px 28px;
            border: 3px solid #e11d1d;
            color: #e11d1d;
            font-size: 42px;
            font-weight: 700;
            transform: rotate(-10deg);
        }
        .notes { margin-top: 20px; font-size: 12px; }
    </style>
</head>
<body>
    @php
        $nominal = (int) round($payment->jumlah_pembayaran);
        $denda = 0;
        $total = $nominal + $denda;
        $monthRomawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'][(int) $payment->tanggal_pembayaran->format('n') - 1];
        $nomorKwitansi = 'KWT/' . str_pad((string) $payment->id, 4, '0', STR_PAD_LEFT) . '/PKS/RRI-BTM/' . $monthRomawi . '/' . $payment->tanggal_pembayaran->format('Y');

        $terbilangFn = function ($angka) use (&$terbilangFn) {
            $angka = abs((int) $angka);
            $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
            if ($angka < 12) return $huruf[$angka];
            if ($angka < 20) return $terbilangFn($angka - 10) . ' Belas';
            if ($angka < 100) return $terbilangFn(intdiv($angka, 10)) . ' Puluh ' . $terbilangFn($angka % 10);
            if ($angka < 200) return 'Seratus ' . $terbilangFn($angka - 100);
            if ($angka < 1000) return $terbilangFn(intdiv($angka, 100)) . ' Ratus ' . $terbilangFn($angka % 100);
            if ($angka < 2000) return 'Seribu ' . $terbilangFn($angka - 1000);
            if ($angka < 1000000) return $terbilangFn(intdiv($angka, 1000)) . ' Ribu ' . $terbilangFn($angka % 1000);
            if ($angka < 1000000000) return $terbilangFn(intdiv($angka, 1000000)) . ' Juta ' . $terbilangFn($angka % 1000000);
            return $terbilangFn(intdiv($angka, 1000000000)) . ' Miliar ' . $terbilangFn($angka % 1000000000);
        };
        $uangTerbilang = trim(preg_replace('/\s+/', ' ', $terbilangFn($nominal))) . ' Rupiah';
    @endphp

    <div class="wrapper">
        <table class="head">
            <tr>
                <td style="width: 18%; text-align: left;">
                    <img src="images/RRI_Logo.png" class="logo" alt="Logo RRI">
                </td>
                <td style="width: 64%; text-align: center;">
                    <p class="title">KWITANSI</p>
                </td>
                <td style="width: 18%;"></td>
            </tr>
        </table>

        <table class="table">
            <tr>
                <td class="label">Nomor</td><td class="colon">:</td><td class="value-strong">{{ $nomorKwitansi }}</td>
            </tr>
        </table>

        <div class="detail-box">
            <table class="table">
                <tr>
                    <td class="label">Sudah Terima Dari</td><td class="colon">:</td><td>{{ $payment->invoice->pks->client->nama ?? '-' }}</td>
                </td>
                <tr>
                    <td class="label">Nomor PKS</td><td class="colon">:</td><td>{{ $payment->invoice->pks->nomor ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Uang Sebesar</td><td class="colon">:</td><td class="value-strong">{{ $uangTerbilang }}</td>
                </tr>
                <tr>
                    <td class="label">Nama Biro Iklan/Klien</td><td class="colon">:</td><td>{{ $payment->invoice->pks->client->nama ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Kerjasama</td><td class="colon">:</td><td>{{ $payment->invoice->pks->judul ?? '-' }} dengan nomor kontrak {{ $payment->invoice->pks->nomor ?? '-' }}</td>
                </tr>
                <tr><td colspan="3" style="height: 14px;"></td></tr>
                <tr>
                    <td class="label">Jumlah</td><td class="colon">:</td><td>Rp {{ number_format($nominal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Denda</td><td class="colon">:</td><td>Rp {{ number_format($denda, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label value-strong">Jumlah Total</td><td class="colon">:</td><td class="value-strong">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <table class="sign-table">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p><strong>Kepala RRI Batam</strong></p>
                    <div class="sign-space"></div>
                    <p><strong>{{ $payment->kwitansi_kepala_stasiun_nama ?? '____________________' }}</strong></p>
                    <p>NIP. {{ $payment->kwitansi_kepala_stasiun_nip ?? '............................' }}</p>
                </td>
                <td>
                    <p>Batam, {{ $payment->tanggal_pembayaran->format('d F Y') }}</p>
                    <p><strong>Penyetor LPP RRI Batam</strong></p>
                    <div class="sign-space"></div>
                    <p><strong>{{ $payment->kwitansi_penyetor_nama ?? '____________________' }}</strong></p>
                    <p>NIP. {{ $payment->kwitansi_penyetor_nip ?? '............................' }}</p>
                </td>
            </tr>
        </table>

        <div class="lunas">LUNAS</div>

        <div class="notes">
            <p style="margin: 0 0 4px 0;"><strong>Catatan:</strong></p>
            <p style="margin: 0;">Lembar 1 Untuk Wajib Bayar;</p>
            <p style="margin: 0;">Lembar 2 Untuk Bendahara Penerima;</p>
            <p style="margin: 0;">Lembar 3 Untuk Petugas Operasional;</p>
            <p style="margin: 0;">Lembar 4 Untuk Petugas Akuntansi.</p>
        </div>
    </div>
</body>
</html>
