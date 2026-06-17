<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->nomor_invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #111;
            line-height: 1.45;
        }
        .wrapper {
            border: 1px solid #111;
            padding: 20px;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #111;
            padding-bottom: 12px;
            margin-bottom: 16px;
        }
        .logo {
            width: 120px;
            height: auto;
        }
        .header-text {
            text-align: center;
        }
        .header-text h2 {
            margin: 0;
            font-size: 17px;
            text-transform: uppercase;
        }
        .header-text h3 {
            margin: 3px 0 0 0;
            font-size: 15px;
        }
        .header-text p {
            margin: 4px 0 0 0;
            font-size: 12px;
            color: #111;
        }
        .invoice-title {
            text-align: center;
            margin: 4px 0 18px 0;
        }
        .invoice-title h3 {
            margin: 0;
            font-size: 30px;
            font-weight: 700;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .invoice-title p {
            margin: 8px 0 0 0;
            font-size: 14px;
            font-weight: 700;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 2px;
            font-size: 14px;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .detail-table th {
            background-color: #f5f5f5;
            border: 1px solid #111;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            font-size: 14px;
        }
        .detail-table td {
            border: 1px solid #111;
            padding: 8px;
            font-size: 14px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .payment-instruction {
            border: 1px solid #111;
            padding: 14px 12px;
            background-color: #fff;
            margin-bottom: 28px;
        }
        .payment-instruction h4 {
            margin: 0 0 6px 0;
            font-size: 14px;
            color: #111;
        }
        .payment-instruction p {
            margin: 0;
            font-size: 13px;
        }
        .footer-table {
            width: 100%;
            margin-top: 28px;
            border-collapse: collapse;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 13px;
        }
        .signature-space {
            height: 80px;
        }
    </style>
</head>
<body>
    <div class="wrapper">

    <!-- KOP SURAT RRI BATAM -->
    <table class="header-table">
        <tr>
            <td style="width: 15%;">
                <!-- Logo RRI (Menggunakan path internal asset) -->
                <img src="images/RRI_Logo.png" class="logo" alt="Logo RRI">
            </td>
            <td class="header-text" style="width: 85%;">
                <h2>LPP RRI BATAM</h2>
                <p>Jl. Abuyaltama No. 2 Batam Kota, Kota Batam, Kepulauan Riau</p>
                <p>Telepon: (0778) 461234 | Email: rribatam@rri.co.id</p>
            </td>
        </tr>
    </table>

    <!-- JUDUL INVOICE -->
    <div class="invoice-title">
        <h3>Invoice Tagihan PNBP</h3>
        <p>Nomor: {{ $invoice->nomor_invoice }}</p>
    </div>

    <!-- DETAIL KONTRAK & KLIEN -->
    <table class="info-table">
        <tr>
            <td style="width: 50%;">
                <strong>Kepada Yth. (Mitra/Klien):</strong><br>
                {{ $invoice->pks->client->nama ?? '-' }}<br>
                U.p. {{ $invoice->pks->client->nama_penanggung_jawab ?? '-' }}<br>
                {{ $invoice->pks->client->alamat ?? '-' }}<br>
                Telp: {{ $invoice->pks->client->no_narahubung ?? '-' }}
            </td>
            <td style="width: 50%; padding-left: 40px;">
                <strong>Detail Rujukan:</strong><br>
                Tanggal Invoice: {{ $invoice->tanggal_invoice->format('d F Y') }}<br>
                Jatuh Tempo: <span style="color: red; font-weight: bold;">{{ $invoice->tanggal_jatuh_tempo->format('d F Y') }}</span><br>
                Kontrak PKS: {{ $invoice->pks->nomor }}<br>
                Judul PKS: {{ $invoice->pks->judul }}
            </td>
        </tr>
    </table>

    <!-- RINCIAN TAGIHAN -->
    <table class="detail-table">
        <thead>
            <tr>
                <th style="width: 8%; text-align: center;">No</th>
                <th style="width: 62%;">Deskripsi Layanan / Kegiatan Jasa PNBP</th>
                <th style="width: 30%; text-align: right;">Jumlah Tagihan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">1</td>
                <td>
                    <strong>Layanan Jasa Penyiaran PNBP RRI Batam</strong><br>
                    <span style="font-size: 12px; color: #111;">Berdasarkan Perjanjian Kerja Sama (PKS) Nomor: {{ $invoice->pks->nomor }}</span>
                </td>
                <td style="text-align: right;">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Pembayaran:</td>
                <td style="text-align: right; font-weight: 700;">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- INSTRUKSI PEMBAYARAN SIMPONI -->
    <div class="payment-instruction">
        <h4>PETUNJUK PEMBAYARAN:</h4>
        <p>Pembayaran wajib disetorkan langsung ke Kas Negara sebagai Penerimaan Negara Bukan Pajak (PNBP) menggunakan <strong>Kode Billing SIMPONI</strong> di bawah ini:</p>
        <p style="margin-top: 8px; font-size: 14px;">
            Kode Billing SIMPONI: 
            <strong style="background-color: #f5f5f5; padding: 2px 6px; border: 1px solid #111; font-size: 16px;">
                {{ $invoice->kode_billing ?? 'BELUM DI-GENERATE' }}
            </strong>
        </p>
        <p style="margin-top: 8px; font-style: italic;">*Pembayaran dapat dilakukan melalui teller bank, ATM, Pos Indonesia, atau Mobile Banking.</p>
    </div>

    <!-- TANDA TANGAN / PENGESAHAN -->
    <table class="footer-table">
        <tr>
            <td>
                <p><strong>Penyetor RRI Batam</strong></p>
                <div class="signature-space"></div>
                <p><strong>{{ $invoice->penyetor_nama ?? '___________________________' }}</strong></p>
                <p>NIP. {{ $invoice->penyetor_nip ?? '........................................' }}</p>
            </td>
            <td>
                <p><strong>Kepala Stasiun RRI Batam</strong></p>
                <div class="signature-space"></div>
                <p><strong>{{ $invoice->kepala_stasiun_nama ?? '___________________________' }}</strong></p>
                <p>NIP. {{ $invoice->kepala_stasiun_nip ?? '........................................' }}</p>
            </td>
        </tr>
    </table>
    </div>

</body>
</html>
