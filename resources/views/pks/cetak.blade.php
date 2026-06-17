@php
    \Carbon\Carbon::setLocale('id');

    $docDate = \Carbon\Carbon::parse($pks->tanggal);
    $items = collect($pks->items ?? [])->values();
    $itemUtama = $items->first();
    $tanggalMulaiItems = $items->pluck('tanggal_mulai')->filter();
    $tanggalSelesaiItems = $items->pluck('tanggal_selesai')->filter();
    $periodeAwal = $tanggalMulaiItems->isNotEmpty() ? \Carbon\Carbon::parse($tanggalMulaiItems->min()) : $docDate;
    $periodeAkhir = $tanggalSelesaiItems->isNotEmpty() ? \Carbon\Carbon::parse($tanggalSelesaiItems->max()) : $docDate;
    $jatuhTempo = $periodeAkhir->copy()->addDays(20);

    $nama_instansi_p1 = 'Lembaga Penyiaran Publik Radio Republik Indonesia';
    $nama_instansi_p2 = $pks->client->nama ?: 'Universitas Ibnu Sina';
    $judul_kerjasama = $pks->judul ?: 'Kerjasama Penyiaran Spot Iklan di Batam';
    $nomor_surat_p1 = $pks->nomor ?: '6665/PKS/RRI-BTM/05/2026';
    $nomor_surat_p2 = $pks->nomor_referensi ?: '________________________';

    $penyebut = function ($nilai) use (&$penyebut) {
        $nilai = abs((int)$nilai);
        $huruf = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($nilai < 12) return $huruf[$nilai];
        if ($nilai < 20) return $penyebut($nilai - 10) . ' Belas';
        if ($nilai < 100) return $penyebut(intdiv($nilai, 10)) . ' Puluh ' . $penyebut($nilai % 10);
        if ($nilai < 200) return 'Seratus ' . $penyebut($nilai - 100);
        if ($nilai < 1000) return $penyebut(intdiv($nilai, 100)) . ' Ratus ' . $penyebut($nilai % 100);
        if ($nilai < 2000) return 'Seribu ' . $penyebut($nilai - 1000);
        if ($nilai < 1000000) return $penyebut(intdiv($nilai, 1000)) . ' Ribu ' . $penyebut($nilai % 1000);
        if ($nilai < 1000000000) return $penyebut(intdiv($nilai, 1000000)) . ' Juta ' . $penyebut($nilai % 1000000);
        return $penyebut(intdiv($nilai, 1000000000)) . ' Miliar ' . $penyebut($nilai % 1000000000);
    };

    $hari_pelaksanaan = ucfirst($docDate->translatedFormat('l'));
    $tanggal_kata = ucfirst($penyebut($docDate->format('d')));
    $bulan_kata = ucfirst($docDate->translatedFormat('F'));
    $tahun_kata = trim(preg_replace('/\s+/', ' ', $penyebut($docDate->format('Y'))));
    $tanggal_angka = $docDate->translatedFormat('d-m-Y');

    $nama_p1 = 'Suhendra, S.E';
    $jabatan_p1 = 'Kepala RRI Batam';
    $alamat_p1 = 'Jalan Abulyatama No. 2 Belian, Batam Kota, Kota Batam Kepulauan Riau.';

    $nama_p2 = ($pks->client->nama_penanggung_jawab ?? $pks->client->nama) ?: 'Universitas Ibnu Sina';
    $jabatan_p2 = $pks->client->jabatan ?: 'Koordinator Promosi';
    $alamat_p2 = $pks->client->alamat ?: 'Lubuk Baja, Kecamatan Lubuk Baja, Kota Batam, Kepulauan Riau 29444';

    $media_penyiaran = $items->pluck('channel')->filter()->map(fn ($channel) => strtoupper($channel))->unique()->implode(', ') ?: 'PRO 2';
    $jumlah_siaran = (int) $items->sum('qty');
    $periode_awal = $periodeAwal->translatedFormat('d F Y');
    $periode_akhir = $periodeAkhir->translatedFormat('d F Y');
    $proses_hari_invoice = 5;
    $persen_denda = 2;
    $tanggal_jatuh_tempo = $jatuhTempo->translatedFormat('d F Y');
    $masa_berlaku_awal = $docDate->translatedFormat('d F Y');
    $masa_berlaku_akhir = $jatuhTempo->translatedFormat('d F Y');
    $total_biaya_angka = (int)($pks->total ?? 2100000);

    $total_biaya_kata = trim(preg_replace('/\s+/', ' ', $penyebut($total_biaya_angka))) . ' rupiah';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>Perjanjian Kerjasama</title>
    <style>
        @page {
            margin: 25mm;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #000;
        }
        
        /* Layout Pembagian Halaman A4 Presisi */
        .page {
            background-color: #fff;
            margin: 0 auto 12mm auto;
            page-break-after: always;
        }
        .page:last-child {
            page-break-after: auto;
        }
        
        /* Logo diletakkan di kiri atas sesuai gambar */
        .logo-container {
            text-align: left;
            margin-bottom: 35px;
        }
        .logo-container img {
            max-width: 180px;
            max-height: 90px;
            object-fit: contain;
        }

        /* Teks Header Rata Tengah */
        .header-text {
            text-align: center;
            font-weight: bold;
            line-height: 1.5;
            margin-bottom: 25px;
        }
        .header-text p {
            margin: 0;
            text-align: center;
        }

        /* Blok Nomor Surat, Rata Tengah dengan titik dua rapi */
        .nomor-surat {
            margin: 0 auto 35px auto;
            font-weight: bold;
            text-align: center;
        }
        .nomor-surat table {
            margin: 0 auto;
            text-align: center;
            border-collapse: collapse;
        }
        .nomor-surat td {
            padding: 2px 5px;
        }

        /* Teks Paragraf */
        p { 
            margin-top: 0;
            margin-bottom: 15px; 
            text-align: justify; 
        }
        
        /* Tabel Identitas Menjorok ke dalam seperti di gambar */
        table.identitas { 
            width: 90%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            margin-left: 50px;
        }
        table.identitas td { 
            vertical-align: top; 
            padding: 3px 5px; 
        }
        .td-label { width: 90px; }
        .td-titikdua { width: 15px; text-align: center;}

        /* Judul Pasal */
        .pasal-title {
            text-align: center;
            font-weight: bold;
            margin-top: 35px;
            margin-bottom: 20px;
            line-height: 1.5;
            page-break-after: avoid;
            break-after: avoid-page;
        }
        .pasal-title + .list-item,
        .pasal-title + p {
            page-break-before: avoid;
            break-before: avoid-page;
        }
        .break-before {
            page-break-before: always;
            break-before: page;
        }

        /* Layout List dengan Hanging Indent yang sangat Rapi */
        .list-item {
            position: relative;
            margin-bottom: 12px;
            padding-left: 30px;
            text-align: justify;
            page-break-inside: avoid;
        }
        .list-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 24px;
        }
        .list-text {
            display: block;
        }
        .list-sub-item {
            position: relative;
            margin-top: 8px;
            margin-left: 30px;
            padding-left: 25px;
            text-align: justify;
            page-break-inside: avoid;
        }
        .list-sub-number {
            position: absolute;
            left: 0;
            top: 0;
            width: 20px;
        }
        
        /* Area Tanda Tangan */
        table.ttd-area { 
            width: 100%; 
            text-align: center; 
            margin-top: 40px;
            border-collapse: collapse;
        }
        table.ttd-area td {
            vertical-align: top;
        }
        .ttd-space { 
            height: 100px; 
        }

        @media screen {
            body {
                background-color: #f3f4f6;
                padding: 30px 0;
            }
            .page {
                width: 210mm;
                min-height: 297mm;
                padding: 2.5cm 2.5cm;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                box-sizing: border-box;
                position: relative;
            }
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
                margin: 0;
            }
            .page {
                margin: 0;
                box-shadow: none;
                page-break-after: always;
                width: auto;
                min-height: auto;
                padding: 0;
            }
            .page:last-child { page-break-after: auto; }
        }
    </style>
</head>
<body>

    <!-- ================= HALAMAN 1 ================= -->
    <div class="page">
        <!-- LOGO KIRI ATAS -->
        <div class="logo-container">
            <img src="{{ public_path('images/RRI_logo.png') }}" alt="Logo">
        </div>

        <!-- KOP / JUDUL DOKUMEN TENGAH -->
        <div class="header-text">
            <p>PERJANJIAN KERJASAMA</p>
            <p>{{ strtoupper($nama_instansi_p1) }}</p>
            <p>DENGAN</p>
            <p>{{ strtoupper($nama_instansi_p2) }}</p>
            <p>TENTANG</p>
            <p>{{ strtoupper($judul_kerjasama) }}</p>
        </div>

        <!-- NOMOR SURAT TENGAH -->
        <div class="nomor-surat">
            <table>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $nomor_surat_p1 }}</td>
                </tr>
                <tr>
                    <td>Nomor</td>
                    <td>:</td>
                    <td>{{ $nomor_surat_p2 }}</td>
                </tr>
            </table>
        </div>

        <!-- MUKADIMAH -->
        <p>Pada hari ini {{ $hari_pelaksanaan }} tanggal {{ $tanggal_kata }} bulan {{ $bulan_kata }} tahun {{ $tahun_kata }}, kami yang bertandatangan di bawah ini:</p>

        <!-- IDENTITAS PIHAK PERTAMA MENJOROK KE DALAM -->
        <table class="identitas">
            <tr>
                <td class="td-label">Nama</td>
                <td class="td-titikdua">:</td>
                <td>{{ $nama_p1 }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_p1 }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $alamat_p1 }}</td>
            </tr>
        </table>
        
        <p>Dalam hal ini bertindak untuk dan atas nama Lembaga Penyiaran Publik Radio Republik Indonesia dalam perjanjian ini disebut sebagai <strong>PIHAK PERTAMA</strong>, dan:</p>

        <!-- IDENTITAS PIHAK KEDUA MENJOROK KE DALAM -->
        <table class="identitas">
            <tr>
                <td class="td-label">Nama</td>
                <td class="td-titikdua">:</td>
                <td>{{ $nama_p2 }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>{{ $jabatan_p2 }}</td>
            </tr>
            <tr>
                <td>Alamat</td>
                <td>:</td>
                <td>{{ $alamat_p2 }}</td>
            </tr>
        </table>
        
        <p>Dalam hal ini bertindak untuk dan atas nama Universitas Ibnu Sina dalam perjanjian ini disebut sebagai <strong>PIHAK KEDUA</strong>.</p>

        <p><strong>PIHAK PERTAMA</strong> dan <strong>PIHAK KEDUA</strong> selanjutnya disebut <strong>PARA PIHAK</strong>, dalam kedudukannya seperti tersebut di atas telah sepakat dan mengikatkan diri dalam kerjasama yang diatur sebagai berikut:</p>
    </div>

    <!-- ================= HALAMAN 2 ================= -->
    <div class="page">
        <!-- PASAL 1 -->
        <div class="pasal-title">
            PASAL 1<br>RUANG LINGKUP KERJASAMA
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text">PARA PIHAK sepakat melakukan kerjasama dalam penyelenggaraan kerjasama Jasa Penyiaran yang disiarkan melalui {{ $media_penyiaran }} sebanyak {{ $jumlah_siaran }} kali siar dengan jangka waktu siar {{ $periode_awal }} - {{ $periode_akhir }}, dengan rincian sebagai berikut:</span>
        </div>
        @foreach($items as $item)
            <div class="list-sub-item">
                <span class="list-sub-number">{{ chr(96 + $loop->iteration) }}.</span>
                <span class="list-text">
                    {{ $item->katalog->nama_layanan ?? 'Jasa Penyiaran' }}
                    melalui {{ strtoupper($item->channel ?? '-') }}
                    periode {{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->translatedFormat('d F Y') : '-' }}
                    - {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->translatedFormat('d F Y') : '-' }}
                    sebanyak {{ (int) ($item->qty ?? 0) }} kali siar.
                </span>
            </div>
        @endforeach
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text">Besaran dan Tata Cara Pengenaan Tarif PNBP yang berlaku atas kerjasama Penyiaran mengacu pada PP. No.68 Tahun 2020 dan Peraturan Dirut RRI No. 5 Tahun 2023.</span>
        </div>

        <!-- PASAL 2 -->
        <div class="pasal-title">
            PASAL 2<br>HAK DAN KEWAJIBAN PARA PIHAK
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> berhak menerima pembayaran Jasa Penyiaran PNBP sebagaimana diatur Pasal 1 (satu) di atas;</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> berhak menerima Materi siap siar, dari <strong>PIHAK KEDUA</strong>;</span>
        </div>
        <div class="list-item">
            <span class="list-number">3.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> berkewajiban menyediakan Alokasi Waktu Penyiaran;</span>
        </div>
        <div class="list-item">
            <span class="list-number">4.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> membuat Surat Penagihan (SPn), Billing Simponi dan Log Proof paling lama {{ $proses_hari_invoice }} (lima) hari kerja setelah masa siar ;</span>
        </div>
        <div class="list-item">
            <span class="list-number">5.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> wajib menggantikan waktu siar pada kesempatan pertama atas penundaan waktu siar dikarenakan adanya peristiwa Kenegaraan, Pertahanan dan Keamanan, Sosial dan Budaya, Keagamaan, Kebencanaan, Kemanusian serta acara-acara khusus RRI yang bersifat mendesak;</span>
        </div>
        <div class="list-item">
            <span class="list-number">6.</span>
            <span class="list-text"><strong>PIHAK KEDUA</strong> berkewajiban melakukan pembayaran Jasa Penyiaran PNBP pada pasal 1 ayat (1);</span>
        </div>
        <div class="list-item">
            <span class="list-number">7.</span>
            <span class="list-text"><strong>PIHAK KEDUA</strong> berkewajiban membayar sanksi Administratif berupa denda sebesar {{ $persen_denda }}% per bulan dari sisa terhutang, sebagai akibat dari belum dibayarnya kewajiban <strong>PIHAK KEDUA</strong> sesuai surat penagihan yang disampaikan oleh <strong>PIHAK PERTAMA</strong>;</span>
        </div>

        <!-- PASAL 3 -->
        <div class="pasal-title">
            PASAL 3<br>CARA PEMBAYARAN
        </div>
        <p>Pembayaran dilakukan dengan cara menyetor langsung ke rekening kas Negara, melalui Billing Simponi.</p>

        <!-- PASAL 4 -->
        <div class="pasal-title" style="margin-bottom: 16px;">
            PASAL 4<br>JANGKA WAKTU
        </div>

        <!-- LIST PASAL 4 -->
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text">Perjanjian ini berlaku tanggal {{ $masa_berlaku_awal }} sampai dengan {{ $masa_berlaku_akhir }}</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text">Jatuh Tempo pembayaran tanggal {{ $tanggal_jatuh_tempo }}</span>
        </div>
        <div class="list-item">
            <span class="list-number">3.</span>
            <span class="list-text"><strong>PIHAK PERTAMA</strong> akan menerbitkan Surat Penagihan maksimal 3 (tiga) hari kalender setelah berakhirnya masa penyiaran.</span>
        </div>

        <!-- PASAL 5 -->
        <div class="pasal-title">
            PASAL 5<br>TARIF
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text">Tarif PNBP yang berlaku atas kerjasama Jasa Penyiaran mengacu pada Lampiran PP Nomor 68 Tahun 2020 dan Peraturan Dirut RRI No. 5 Tahun 2023.</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text">Biaya Jasa Penyiaran:</span>
        </div>
        @foreach($items as $item)
            <div class="list-sub-item">
                <span class="list-sub-number">{{ chr(96 + $loop->iteration) }}.</span>
                <span class="list-text">
                    {{ $item->katalog->nama_layanan ?? 'Jasa Penyiaran' }},
                    {{ ($item->waktu ?? 'regular') === 'prime' ? 'Prime Time' : 'Regular Time' }},
                    {{ strtoupper($item->channel ?? '-') }}
                    sebanyak {{ (int) ($item->qty ?? 0) }} kali:
                    {{ (int) ($item->qty ?? 0) }} x {{ number_format((int) ($item->tarif ?? 0), 0, ',', '.') }}
                    = Rp {{ number_format((int) ($item->subtotal ?? 0), 0, ',', '.') }}
                </span>
            </div>
        @endforeach
        <div class="list-item" style="margin-top: 15px;">
            <span class="list-number">3.</span>
            <span class="list-text">Total Biaya : Rp {{ number_format($total_biaya_angka, 0, ',', '.') }} ({{ ucwords($total_biaya_kata) }})</span>
        </div>

        <!-- PASAL 6 -->
        <div class="pasal-title">
            PASAL 6<br>SANKSI DAN DENDA
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text"><strong>PIHAK KEDUA</strong> wajib membayar PNBP Terutang paling lambat pada saat jatuh tempo sesuai dengan ketentuan peraturan perundang-undangan;</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text"><strong>PIHAK KEDUA</strong> yang tidak melakukan pembayaran PNBP Terutang sampai dengan jatuh tempo dikenai sanksi administratif berupa denda sebesar 2% (dua persen) per bulan dari jumlah PNBP terutang dan bagian dari bulan dihitung satu bulan penuh;</span>
        </div>
        <div class="list-item">
            <span class="list-number">3.</span>
            <span class="list-text">Sanksi administratif berupa denda dikenakan untuk waktu paling lama 24 (dua puluh empat) bulan.</span>
        </div>

        <!-- PASAL 7 -->
        <div class="pasal-title">
            PASAL 7<br>KEADAAN MEMAKSA (FORCE MAJEURE)
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text">Keadaan Kahar (force majeure) dalam Perjanjian Kerjasama adalah Kebakaran, gempa Bumi, badai, topan, banjir, dan bencana Alam lainnya serta Huru Hara, Perang, Makar, kerusuhan, Perselisihan Buruh, pemogokan, kebijakan Moneter, yang berpengaruh langsung pada pelaksanaan perjanjian ini;</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text">Tidak satupun PIHAK dikenai tanggung jawab untuk memenuhi kewajiban berdasarkan perjanjian ini sepanjang hal tersebut terhalangi, tercegah atau tertunda pelaksanaannya oleh keadaan Kahar (force majeure).</span>
        </div>
    </div>

    <!-- ================= HALAMAN 3 ================= -->
    <div class="page">
        <!-- PASAL 8 -->
        <div class="pasal-title">
            PASAL 8<br>PENUTUP
        </div>
        
        <div class="list-item">
            <span class="list-number">1.</span>
            <span class="list-text">Hal yang belum tercantum dalam kerjasama ini akan diatur kemudian dengan kesepakatan para pihak dalam bentuk Adendum, dan merupakan bagian yang tidak terpisahkan dari kerjasama ini;</span>
        </div>
        <div class="list-item">
            <span class="list-number">2.</span>
            <span class="list-text">Dalam hal terjadinya permasalahan, akan diselesaikan dengan cara musyawarah dan mufakat, namun apabila tidak terjadi mufakat akan diselesaikan di Pengadilan setempat.</span>
        </div>

        <p style="text-align: justify; margin-top: 35px; margin-bottom: 60px;">Demikian Perjanjian kerjasama ini dibuat dalam rangkap 2 (dua) dan bermaterai cukup, masing-masing rangkap ditandatangani oleh Para Pihak dan memiliki kekuatan hukum yang sama.</p>

        <!-- AREA TANDA TANGAN -->
        <table class="ttd-area">
            <tr>
                <td style="width: 50%;">
                    <strong>PIHAK PERTAMA</strong><br>
                    Kepala RRI Batam
                    <div class="ttd-space"></div>
                    <strong>{{ $nama_p1 }}</strong>
                </td>
                <td style="width: 50%;">
                    <strong>PIHAK KEDUA</strong><br>
                    {{ $jabatan_p2 }}
                    <div class="ttd-space"></div>
                    <strong>{{ $nama_p2 }}</strong>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
