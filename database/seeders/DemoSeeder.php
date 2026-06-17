<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Katalog;
use App\Models\Payment;
use App\Models\Pks;
use App\Models\PksItem;
use App\Models\Tarif;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed data demo untuk memperlihatkan alur PKS, invoice, dan pembayaran.
     */
    public function run(): void
    {
        $periodEnd = Carbon::parse('2026-06-11');

        $clients = $this->seedClients();
        Pks::where('nomor_referensi', 'like', 'REF/DEMO/%')->delete();

        $contracts = [
            [
                'nomor' => '0001/PKS/RRI-BTM/01/2026',
                'judul' => 'Paket Publikasi Event Visit Batam',
                'client' => 'Dinas Pariwisata Kota Batam',
                'tanggal' => Carbon::parse('2026-01-01'),
                'deskripsi' => 'Publikasi radio untuk agenda promosi pariwisata daerah.',
                'status' => 'paid',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 20, 'mulai' => 1, 'selesai' => 7],
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 1, 'mulai' => 8, 'selesai' => 8],
                ],
            ],
            [
                'nomor' => '0002/PKS/RRI-BTM/01/2026',
                'judul' => 'Sosialisasi Layanan Digital',
                'client' => 'Bank Kepri Batam',
                'tanggal' => Carbon::parse('2026-01-09'),
                'deskripsi' => 'Kampanye layanan digital dan edukasi nasabah melalui siaran radio.',
                'status' => 'waiting',
                'items' => [
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 12, 'mulai' => 2, 'selesai' => 9],
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 10, 'mulai' => 2, 'selesai' => 9],
                ],
            ],
            [
                'nomor' => '0003/PKS/RRI-BTM/01/2026',
                'judul' => 'Liputan Kegiatan CSR',
                'client' => 'PT Pelabuhan Batam Sejahtera',
                'tanggal' => Carbon::parse('2026-01-18'),
                'deskripsi' => 'Peliputan dan live report kegiatan tanggung jawab sosial perusahaan.',
                'status' => 'overdue',
                'items' => [
                    ['layanan' => 'Live Report', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 2, 'mulai' => 1, 'selesai' => 2],
                    ['layanan' => 'Liputan Berita', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 2, 'selesai' => 2],
                ],
            ],
            [
                'nomor' => '0004/PKS/RRI-BTM/02/2026',
                'judul' => 'Publikasi Pameran UMKM',
                'client' => 'Koperasi UMKM Madani',
                'tanggal' => Carbon::parse('2026-02-01'),
                'deskripsi' => 'Paket publikasi menjelang pameran dan bazar UMKM.',
                'status' => 'no_billing',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 15, 'mulai' => 1, 'selesai' => 10],
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 10, 'mulai' => 1, 'selesai' => 10],
                ],
            ],
            [
                'nomor' => '0005/PKS/RRI-BTM/02/2026',
                'judul' => 'Talkshow Kesehatan Masyarakat',
                'client' => 'RS Harapan Bunda Batam',
                'tanggal' => Carbon::parse('2026-02-09'),
                'deskripsi' => 'Dialog interaktif edukasi kesehatan untuk masyarakat Batam.',
                'status' => 'no_invoice',
                'items' => [
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 4, 'selesai' => 4],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 8, 'mulai' => 1, 'selesai' => 4],
                ],
            ],
            [
                'nomor' => '0006/PKS/RRI-BTM/02/2026',
                'judul' => 'Kampanye Keselamatan Pelayaran',
                'client' => 'KSOP Khusus Batam',
                'tanggal' => Carbon::parse('2026-02-18'),
                'deskripsi' => 'Sosialisasi keselamatan pelayaran dan kepatuhan dokumen kapal.',
                'status' => 'paid',
                'items' => [
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 18, 'mulai' => 1, 'selesai' => 8],
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 1, 'mulai' => 7, 'selesai' => 7],
                ],
            ],
            [
                'nomor' => '0007/PKS/RRI-BTM/03/2026',
                'judul' => 'Promosi Hunian Baru',
                'client' => 'PT Citra Mandiri Properti',
                'tanggal' => Carbon::parse('2026-03-01'),
                'deskripsi' => 'Publikasi produk hunian dan promo pembelian rumah.',
                'status' => 'waiting',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 25, 'mulai' => 2, 'selesai' => 15],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 10, 'mulai' => 5, 'selesai' => 13],
                ],
            ],
            [
                'nomor' => '0008/PKS/RRI-BTM/03/2026',
                'judul' => 'Festival Kuliner Melayu',
                'client' => 'Komunitas Kuliner Melayu Batam',
                'tanggal' => Carbon::parse('2026-03-09'),
                'deskripsi' => 'Publikasi festival kuliner dan talkshow pelaku UMKM makanan.',
                'status' => 'no_billing',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 20, 'mulai' => 2, 'selesai' => 12],
                    ['layanan' => 'Live Report', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 13, 'selesai' => 13],
                ],
            ],
            [
                'nomor' => '0009/PKS/RRI-BTM/03/2026',
                'judul' => 'Siaran Edukasi Pajak Daerah',
                'client' => 'Bapenda Kota Batam',
                'tanggal' => Carbon::parse('2026-03-17'),
                'deskripsi' => 'Edukasi wajib pajak melalui spot dan dialog interaktif.',
                'status' => 'overdue',
                'items' => [
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 2, 'mulai' => 3, 'selesai' => 10],
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 14, 'mulai' => 4, 'selesai' => 14],
                ],
            ],
            [
                'nomor' => '0010/PKS/RRI-BTM/03/2026',
                'judul' => 'Publikasi Pelatihan Vokasi',
                'client' => 'BLK Industri Batam',
                'tanggal' => Carbon::parse('2026-03-26'),
                'deskripsi' => 'Informasi pendaftaran pelatihan vokasi dan sertifikasi kerja.',
                'status' => 'no_invoice',
                'items' => [
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 12, 'mulai' => 2, 'selesai' => 12],
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 10, 'mulai' => 2, 'selesai' => 12],
                ],
            ],
            [
                'nomor' => '0011/PKS/RRI-BTM/04/2026',
                'judul' => 'Grand Opening Klinik Utama',
                'client' => 'Klinik Utama Medika Prima',
                'tanggal' => Carbon::parse('2026-04-03'),
                'deskripsi' => 'Promosi layanan klinik dan informasi jadwal dokter.',
                'status' => 'paid',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 18, 'mulai' => 3, 'selesai' => 13],
                    ['layanan' => 'Live Report', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 14, 'selesai' => 14],
                ],
            ],
            [
                'nomor' => '0012/PKS/RRI-BTM/04/2026',
                'judul' => 'Kampanye Donor Darah',
                'client' => 'PMI Kota Batam',
                'tanggal' => Carbon::parse('2026-04-11'),
                'deskripsi' => 'Ajakan donor darah dan publikasi lokasi kegiatan.',
                'status' => 'waiting',
                'items' => [
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 20, 'mulai' => 1, 'selesai' => 12],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 8, 'mulai' => 3, 'selesai' => 12],
                ],
            ],
            [
                'nomor' => '0013/PKS/RRI-BTM/04/2026',
                'judul' => 'Publikasi Pendaftaran Kampus',
                'client' => 'Universitas Maritim Batam',
                'tanggal' => Carbon::parse('2026-04-20'),
                'deskripsi' => 'Promosi penerimaan mahasiswa baru dan program beasiswa.',
                'status' => 'paid',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 30, 'mulai' => 2, 'selesai' => 22],
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'prime', 'channel' => 'Pro 2', 'qty' => 1, 'mulai' => 18, 'selesai' => 18],
                ],
            ],
            [
                'nomor' => '0014/PKS/RRI-BTM/04/2026',
                'judul' => 'Roadshow Musik Lokal',
                'client' => 'Sanggar Seni Hang Nadim',
                'tanggal' => Carbon::parse('2026-04-29'),
                'deskripsi' => 'Publikasi kegiatan seni dan siaran langsung komunitas musik lokal.',
                'status' => 'no_billing',
                'items' => [
                    ['layanan' => 'Live Report', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 2, 'mulai' => 3, 'selesai' => 4],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 6, 'mulai' => 2, 'selesai' => 7],
                ],
            ],
            [
                'nomor' => '0015/PKS/RRI-BTM/05/2026',
                'judul' => 'Informasi Layanan Kependudukan',
                'client' => 'Disdukcapil Kota Batam',
                'tanggal' => Carbon::parse('2026-05-04'),
                'deskripsi' => 'Sosialisasi layanan administrasi kependudukan dan kanal pengaduan.',
                'status' => 'overdue',
                'items' => [
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 25, 'mulai' => 2, 'selesai' => 12],
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 11, 'selesai' => 11],
                ],
            ],
            [
                'nomor' => '0016/PKS/RRI-BTM/05/2026',
                'judul' => 'Promosi Paket Wisata Pulau',
                'client' => 'CV Bahari Wisata Nusantara',
                'tanggal' => Carbon::parse('2026-05-12'),
                'deskripsi' => 'Promosi paket wisata bahari dan agenda libur sekolah.',
                'status' => 'waiting',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'prime', 'channel' => 'Pro 2', 'qty' => 22, 'mulai' => 2, 'selesai' => 15],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 8, 'mulai' => 2, 'selesai' => 15],
                ],
            ],
            [
                'nomor' => '0017/PKS/RRI-BTM/05/2026',
                'judul' => 'Peluncuran Produk UMKM',
                'client' => 'Rumah Kreatif Tanjung Uma',
                'tanggal' => Carbon::parse('2026-05-20'),
                'deskripsi' => 'Publikasi produk baru UMKM binaan dan liputan kegiatan peluncuran.',
                'status' => 'no_invoice',
                'items' => [
                    ['layanan' => 'Liputan Berita', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 2, 'selesai' => 2],
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 12, 'mulai' => 1, 'selesai' => 7],
                ],
            ],
            [
                'nomor' => '0018/PKS/RRI-BTM/05/2026',
                'judul' => 'Publikasi Expo Pendidikan',
                'client' => 'Yayasan Cahaya Ilmu Batam',
                'tanggal' => Carbon::parse('2026-05-28'),
                'deskripsi' => 'Promosi expo pendidikan, seminar, dan konsultasi sekolah.',
                'status' => 'paid',
                'items' => [
                    ['layanan' => 'Spot Iklan Radio', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 24, 'mulai' => 1, 'selesai' => 9],
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 1, 'mulai' => 8, 'selesai' => 8],
                ],
            ],
            [
                'nomor' => '0019/PKS/RRI-BTM/06/2026',
                'judul' => 'Sosialisasi Transportasi Publik',
                'client' => 'Trans Batam Mandiri',
                'tanggal' => Carbon::parse('2026-06-04'),
                'deskripsi' => 'Informasi rute baru, tarif, dan penggunaan kartu transportasi.',
                'status' => 'waiting',
                'items' => [
                    ['layanan' => 'Iklan Layanan Masyarakat', 'waktu' => 'prime', 'channel' => 'Pro 1', 'qty' => 16, 'mulai' => 1, 'selesai' => 7],
                    ['layanan' => 'Live Report', 'waktu' => 'regular', 'channel' => 'Pro 2', 'qty' => 1, 'mulai' => 6, 'selesai' => 6],
                ],
            ],
            [
                'nomor' => '0020/PKS/RRI-BTM/06/2026',
                'judul' => 'Kampanye Literasi Digital',
                'client' => 'Kominfo Provinsi Kepri',
                'tanggal' => Carbon::parse('2026-06-11'),
                'deskripsi' => 'Edukasi keamanan digital dan etika bermedia sosial.',
                'status' => 'no_billing',
                'items' => [
                    ['layanan' => 'Talkshow Interaktif', 'waktu' => 'prime', 'channel' => 'Pro 2', 'qty' => 2, 'mulai' => 0, 'selesai' => 0],
                    ['layanan' => 'Adlibs Penyiar', 'waktu' => 'regular', 'channel' => 'Pro 1', 'qty' => 12, 'mulai' => 0, 'selesai' => 0],
                ],
            ],
        ];

        foreach ($contracts as $index => $contract) {
            $pks = Pks::updateOrCreate(
                ['nomor' => $contract['nomor']],
                [
                    'judul' => $contract['judul'],
                    'nomor_referensi' => 'REF/DEMO/' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                    'client_id' => $clients[$contract['client']]->id,
                    'deskripsi' => $contract['deskripsi'],
                    'tanggal' => $contract['tanggal']->toDateString(),
                    'total' => 0,
                ]
            );

            $total = $this->syncItems($pks, $contract['items'], Carbon::parse($contract['tanggal']));
            $pks->update(['total' => $total]);

            if ($contract['status'] !== 'no_invoice') {
                $this->syncInvoice($pks, $contract['status'], $index + 1, $periodEnd);
            }
        }
    }

    private function seedClients(): array
    {
        $rows = [
            'Dinas Pariwisata Kota Batam' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Maya Pratiwi',
                'no_narahubung' => '081234560101',
                'email' => 'pariwisata.demo@batam.go.id',
                'nama_penanggung_jawab' => 'Rudi Kurniawan',
                'jabatan' => 'Kepala Bidang Promosi',
                'alamat' => 'Jl. Raja H. Fisabilillah No. 1, Batam Kota',
            ],
            'Bank Kepri Batam' => [
                'jenis_klien' => 'BUMD',
                'nama_narahubung' => 'Nadia Fitri',
                'no_narahubung' => '081234560102',
                'email' => 'marketing.demo@bankkepri.co.id',
                'nama_penanggung_jawab' => 'Andi Saputra',
                'jabatan' => 'Branch Manager',
                'alamat' => 'Komplek Ruko Palm Spring, Batam Center',
            ],
            'PT Pelabuhan Batam Sejahtera' => [
                'jenis_klien' => 'Perusahaan Swasta',
                'nama_narahubung' => 'Bima Santoso',
                'no_narahubung' => '081234560103',
                'email' => 'csr.demo@pbs.co.id',
                'nama_penanggung_jawab' => 'Dewi Lestari',
                'jabatan' => 'Corporate Secretary',
                'alamat' => 'Kawasan Pelabuhan Batu Ampar, Batam',
            ],
            'Koperasi UMKM Madani' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Siti Rahma',
                'no_narahubung' => '081234560104',
                'email' => 'umkm.demo@madani.or.id',
                'nama_penanggung_jawab' => 'Hendra Wijaya',
                'jabatan' => 'Ketua Koperasi',
                'alamat' => 'Jl. Hang Tuah, Bengkong, Batam',
            ],
            'RS Harapan Bunda Batam' => [
                'jenis_klien' => 'Perusahaan Swasta',
                'nama_narahubung' => 'dr. Laila Anjani',
                'no_narahubung' => '081234560105',
                'email' => 'humas.demo@rsharapanbunda.co.id',
                'nama_penanggung_jawab' => 'Irwan Maulana',
                'jabatan' => 'Manajer Humas',
                'alamat' => 'Jl. Seraya Atas No. 12, Batam',
            ],
            'KSOP Khusus Batam' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Farhan Akbar',
                'no_narahubung' => '081234560106',
                'email' => 'humas.demo@ksopbatam.go.id',
                'nama_penanggung_jawab' => 'Marzuki Amin',
                'jabatan' => 'Kepala Seksi Lalu Lintas Laut',
                'alamat' => 'Jl. RE Martadinata, Sekupang, Batam',
            ],
            'PT Citra Mandiri Properti' => [
                'jenis_klien' => 'Perusahaan Swasta',
                'nama_narahubung' => 'Reno Pratama',
                'no_narahubung' => '081234560107',
                'email' => 'promo.demo@citramandiri.co.id',
                'nama_penanggung_jawab' => 'Grace Natalia',
                'jabatan' => 'Marketing Director',
                'alamat' => 'Jl. Ahmad Yani, Batam Kota',
            ],
            'Komunitas Kuliner Melayu Batam' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Nur Aisyah',
                'no_narahubung' => '081234560108',
                'email' => 'festival.demo@kulimelayu.org',
                'nama_penanggung_jawab' => 'Taufik Hidayat',
                'jabatan' => 'Koordinator Acara',
                'alamat' => 'Jl. Laksamana Bintan, Sei Panas, Batam',
            ],
            'Bapenda Kota Batam' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Vina Oktaviani',
                'no_narahubung' => '081234560109',
                'email' => 'publikasi.demo@bapenda.batam.go.id',
                'nama_penanggung_jawab' => 'Arif Munandar',
                'jabatan' => 'Kepala Subbagian Umum',
                'alamat' => 'Jl. Raja Isa No. 17, Batam Center',
            ],
            'BLK Industri Batam' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Rika Amelia',
                'no_narahubung' => '081234560110',
                'email' => 'info.demo@blkbatam.go.id',
                'nama_penanggung_jawab' => 'Yusuf Maulana',
                'jabatan' => 'Kepala Program Pelatihan',
                'alamat' => 'Kawasan Industri Tunas, Batam',
            ],
            'Klinik Utama Medika Prima' => [
                'jenis_klien' => 'Perusahaan Swasta',
                'nama_narahubung' => 'Aulia Maharani',
                'no_narahubung' => '081234560111',
                'email' => 'humas.demo@medikaprima.co.id',
                'nama_penanggung_jawab' => 'dr. Bagas Wicaksono',
                'jabatan' => 'Direktur Klinik',
                'alamat' => 'Jl. Teuku Umar, Lubuk Baja, Batam',
            ],
            'PMI Kota Batam' => [
                'jenis_klien' => 'Organisasi Nirlaba',
                'nama_narahubung' => 'Laras Puspita',
                'no_narahubung' => '081234560112',
                'email' => 'donor.demo@pmibatam.or.id',
                'nama_penanggung_jawab' => 'M. Fadli Rahman',
                'jabatan' => 'Sekretaris',
                'alamat' => 'Jl. Jenderal Sudirman, Batam Center',
            ],
            'Universitas Maritim Batam' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Dian Anggraini',
                'no_narahubung' => '081234560113',
                'email' => 'pmb.demo@umbatam.ac.id',
                'nama_penanggung_jawab' => 'Prof. Rahmat Efendi',
                'jabatan' => 'Wakil Rektor Bidang Kerja Sama',
                'alamat' => 'Jl. Hang Lekiu, Nongsa, Batam',
            ],
            'Sanggar Seni Hang Nadim' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Ilham Saputra',
                'no_narahubung' => '081234560114',
                'email' => 'acara.demo@hangnadimart.org',
                'nama_penanggung_jawab' => 'Melati Kirana',
                'jabatan' => 'Ketua Sanggar',
                'alamat' => 'Jl. Engku Putri, Batam Center',
            ],
            'Disdukcapil Kota Batam' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Rizal Hanafiah',
                'no_narahubung' => '081234560115',
                'email' => 'layanan.demo@disdukcapil.batam.go.id',
                'nama_penanggung_jawab' => 'Sri Handayani',
                'jabatan' => 'Kepala Bidang Pelayanan',
                'alamat' => 'Jl. Ir. Sutami, Sekupang, Batam',
            ],
            'CV Bahari Wisata Nusantara' => [
                'jenis_klien' => 'Perusahaan Swasta',
                'nama_narahubung' => 'Kevin Halim',
                'no_narahubung' => '081234560116',
                'email' => 'sales.demo@bahariwisata.co.id',
                'nama_penanggung_jawab' => 'Mira Septiani',
                'jabatan' => 'Owner',
                'alamat' => 'Ruko Tiban Center, Sekupang, Batam',
            ],
            'Rumah Kreatif Tanjung Uma' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Eka Putri',
                'no_narahubung' => '081234560117',
                'email' => 'program.demo@rktanjunguma.org',
                'nama_penanggung_jawab' => 'Hasan Basri',
                'jabatan' => 'Koordinator Program',
                'alamat' => 'Tanjung Uma, Lubuk Baja, Batam',
            ],
            'Yayasan Cahaya Ilmu Batam' => [
                'jenis_klien' => 'Lembaga',
                'nama_narahubung' => 'Niken Permata',
                'no_narahubung' => '081234560118',
                'email' => 'expo.demo@cahayailmu.sch.id',
                'nama_penanggung_jawab' => 'Agus Salim',
                'jabatan' => 'Ketua Yayasan',
                'alamat' => 'Jl. Gajah Mada, Tiban, Batam',
            ],
            'Trans Batam Mandiri' => [
                'jenis_klien' => 'BUMD',
                'nama_narahubung' => 'Teguh Prakoso',
                'no_narahubung' => '081234560119',
                'email' => 'operasional.demo@transbatam.co.id',
                'nama_penanggung_jawab' => 'Novi Yuliana',
                'jabatan' => 'Manajer Operasional',
                'alamat' => 'Terminal Batam Center, Batam',
            ],
            'Kominfo Provinsi Kepri' => [
                'jenis_klien' => 'Instansi Pemerintahan',
                'nama_narahubung' => 'Putra Ramadhan',
                'no_narahubung' => '081234560120',
                'email' => 'literasi.demo@kominfo.kepriprov.go.id',
                'nama_penanggung_jawab' => 'Ratna Sari',
                'jabatan' => 'Kepala Bidang Informasi Publik',
                'alamat' => 'Dompak, Tanjungpinang',
            ],
        ];

        $clients = [];

        foreach ($rows as $name => $data) {
            $clients[$name] = Client::updateOrCreate(
                ['nama' => $name],
                $data + [
                    'agen_rri' => 'LPU RRI Batam',
                    'catatan' => 'Data demo untuk presentasi aplikasi.',
                ]
            );
        }

        return $clients;
    }

    private function syncItems(Pks $pks, array $items, Carbon $today): float
    {
        $pks->items()->delete();
        $total = 0;

        foreach ($items as $item) {
            $katalog = Katalog::where('nama_layanan', $item['layanan'])->firstOrFail();
            $tarif = Tarif::where('katalog_id', $katalog->id)
                ->where('waktu', $item['waktu'])
                ->firstOrFail();
            $subtotal = (float) $tarif->tarif * (int) $item['qty'];

            PksItem::create([
                'pks_id' => $pks->id,
                'katalog_id' => $katalog->id,
                'waktu' => $item['waktu'],
                'channel' => $item['channel'],
                'tanggal_mulai' => $today->copy()->addDays($item['mulai'])->toDateString(),
                'tanggal_selesai' => $today->copy()->addDays($item['selesai'])->toDateString(),
                'qty' => $item['qty'],
                'tarif' => $tarif->tarif,
                'subtotal' => $subtotal,
            ]);

            $total += $subtotal;
        }

        return $total;
    }

    private function syncInvoice(Pks $pks, string $status, int $number, Carbon $today): void
    {
        $invoiceDate = $pks->tanggal;
        $documentMonth = Carbon::parse($pks->tanggal)->format('m');
        $dueDate = $pks->items()->max('tanggal_selesai')
            ? Carbon::parse($pks->items()->max('tanggal_selesai'))->addDays(20)
            : Carbon::parse($pks->tanggal)->addDays(20);
        $billingCode = $status === 'no_billing' ? null : '8202606' . str_pad((string) $number, 8, '0', STR_PAD_LEFT);

        if ($status === 'overdue') {
            $dueDate = $today->copy()->subDays(5);
        }

        $invoice = Invoice::updateOrCreate(
            ['nomor_invoice' => str_pad((string) $number, 4, '0', STR_PAD_LEFT) . '/KEU/INV/RRI-BTM/' . $documentMonth . '/2026'],
            [
                'pks_id' => $pks->id,
                'nominal' => $pks->total,
                'tanggal_invoice' => $invoiceDate,
                'tanggal_jatuh_tempo' => $dueDate->toDateString(),
                'status' => $status === 'paid' ? Invoice::STATUS_PAID : ($billingCode ? Invoice::STATUS_MENUNGGU_PEMBAYARAN : Invoice::STATUS_BELUM_BILLING),
                'kode_billing' => $billingCode,
                'penyetor_nama' => 'Bendahara Penerimaan RRI Batam',
                'penyetor_nip' => '199205072019031008',
                'kepala_stasiun_nama' => 'Suhendra, SE',
                'kepala_stasiun_nip' => '197204121998031002',
            ]
        );

        if ($status === 'paid') {
            $this->syncPayment($invoice, $number, $today);
        }
    }

    private function syncPayment(Invoice $invoice, int $number, Carbon $today): void
    {
        $documentMonth = Carbon::parse($invoice->tanggal_invoice)->format('m');
        $paymentDate = Carbon::parse($invoice->tanggal_invoice)->addDays(14);

        if ($paymentDate->gt($today)) {
            $paymentDate = $today->copy();
        }

        Payment::where('invoice_id', $invoice->id)
            ->where('nomor_pembayaran', 'KWT/' . str_pad((string) $number, 4, '0', STR_PAD_LEFT) . '/PKS/RRI-BTM/' . $documentMonth . '/2026')
            ->delete();

        $payment = new Payment();
        $payment->forceFill([
            'nomor_pembayaran' => 'KWT/' . str_pad((string) $number, 4, '0', STR_PAD_LEFT) . '/PKS/RRI-BTM/' . $documentMonth . '/2026',
            'invoice_id' => $invoice->id,
            'tanggal_pembayaran' => $paymentDate->toDateString(),
            'kode_billing' => $invoice->kode_billing,
            'ntpn' => 'NTPN' . str_pad((string) $number, 12, '0', STR_PAD_LEFT),
            'ntb' => 'NTB' . str_pad((string) $number, 12, '0', STR_PAD_LEFT),
            'jumlah_pembayaran' => $invoice->nominal,
            'catatan' => 'Pembayaran demo melalui SIMPONI.',
            'kwitansi_penyetor_nama' => $invoice->penyetor_nama,
            'kwitansi_penyetor_nip' => $invoice->penyetor_nip,
            'kwitansi_kepala_stasiun_nama' => $invoice->kepala_stasiun_nama,
            'kwitansi_kepala_stasiun_nip' => $invoice->kepala_stasiun_nip,
        ])->save();
    }
}
