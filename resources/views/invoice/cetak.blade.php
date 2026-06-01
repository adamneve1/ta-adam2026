<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->nomor_invoice }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }
        .header-table {
            width: 100%;
            border-bottom: 3px double #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
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
            font-size: 16px;
            text-transform: uppercase;
        }
        .header-text p {
            margin: 4px 0 0 0;
            font-size: 11px;
            color: #555;
        }
        .invoice-title {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .invoice-title h3 {
            margin: 0;
            font-size: 15px;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .invoice-title p {
            margin: 5px 0 0 0;
            font-family: monospace;
            font-size: 12px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            vertical-align: top;
            padding: 4px 0;
        }
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .detail-table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        .detail-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .payment-instruction {
            border: 1px dashed #333;
            padding: 10px;
            background-color: #fafafa;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .payment-instruction h4 {
            margin: 0 0 5px 0;
            font-size: 12px;
            color: #d9534f;
        }
        .payment-instruction p {
            margin: 0;
            font-size: 11px;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
        }
        .footer-table td {
            width: 50%;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT RRI BATAM -->
    <table class="header-table">
        <tr>
            <td style="width: 15%;">
                <!-- Logo RRI (Menggunakan path internal asset) -->
                <img src="images/RRI_Logo.png" class="logo" alt="Logo RRI">
            </td>
            <td class="header-text" style="width: 85%;">
                <h2>Lembaga Penyiaran Publik Radio Republik Indonesia</h2>
                <h3>Stasiun Batam</h3>
                <p>Jalan RRI No. 1, Batam Centre, Kota Batam, Kepulauan Riau</p>
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
                    <span style="font-size: 11px; color: #555;">Berdasarkan Perjanjian Kerja Sama (PKS) Nomor: {{ $invoice->pks->nomor }}</span>
                </td>
                <td style="text-align: right; font-size: 13px;">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" style="text-align: right;">Total Pembayaran:</td>
                <td style="text-align: right; color: #2e7d32; font-size: 14px;">Rp {{ number_format($invoice->nominal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- INSTRUKSI PEMBAYARAN SIMPONI -->
    <div class="payment-instruction">
        <h4>PETUNJUK PEMBAYARAN:</h4>
        <p>Pembayaran wajib disetorkan langsung ke Kas Negara sebagai Penerimaan Negara Bukan Pajak (PNBP) menggunakan <strong>Kode Billing SIMPONI</strong> di bawah ini:</p>
        <p style="margin-top: 8px; font-size: 14px;">
            Kode Billing SIMPONI: 
            <strong style="font-family: monospace; background-color: #eee; padding: 2px 6px; border: 1px solid #ccc; font-size: 16px;">
                {{ $invoice->kode_billing ?? 'BELUM DI-GENERATE' }}
            </strong>
        </p>
        <p style="margin-top: 8px; font-style: italic; color: #666;">*Pembayaran dapat dilakukan melalui teller bank, ATM, Pos Indonesia, atau Mobile Banking.</p>
    </div>

    <!-- TANDA TANGAN / PENGESAHAN -->
    <table class="footer-table">
        <tr>
            <td>
                <!-- Sisi Kiri: Kosong atau Tanda Tangan Penerima -->
            </td>
            <td>
                <p>Batam, {{ $invoice->tanggal_invoice->format('d F Y') }}</p>
                <p><strong>Kuasa Pengguna Anggaran (KPA) /<br>Bendahara Penerimaan LPP RRI Batam</strong></p>
                <br><br><br><br>
                <p>___________________________</p>
                <p>NIP. ........................................</p>
            </td>
        </tr>
    </table>

</body>
</html>