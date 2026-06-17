<?php

namespace Database\Seeders;

use App\Models\Katalog;
use App\Models\Tarif;
use Illuminate\Database\Seeder;

class KatalogSeeder extends Seeder
{
    /**
     * Seed katalog layanan beserta tarif regular dan prime.
     */
    public function run(): void
    {
        $katalogs = [
            [
                'nama_layanan' => 'Spot Iklan Radio',
                'deskripsi' => 'Penayangan iklan audio berdurasi singkat pada program siaran radio.',
                'tarif_regular' => 150000,
                'tarif_prime' => 250000,
            ],
            [
                'nama_layanan' => 'Adlibs Penyiar',
                'deskripsi' => 'Pembacaan materi promosi secara langsung oleh penyiar saat program berlangsung.',
                'tarif_regular' => 200000,
                'tarif_prime' => 300000,
            ],
            [
                'nama_layanan' => 'Talkshow Interaktif',
                'deskripsi' => 'Sesi dialog bersama narasumber untuk promosi, edukasi, atau publikasi program.',
                'tarif_regular' => 1500000,
                'tarif_prime' => 2500000,
            ],
            [
                'nama_layanan' => 'Live Report',
                'deskripsi' => 'Laporan langsung dari lokasi kegiatan atau acara mitra.',
                'tarif_regular' => 1000000,
                'tarif_prime' => 1750000,
            ],
            [
                'nama_layanan' => 'Liputan Berita',
                'deskripsi' => 'Peliputan kegiatan mitra untuk kebutuhan publikasi berita radio.',
                'tarif_regular' => 750000,
                'tarif_prime' => 1250000,
            ],
            [
                'nama_layanan' => 'Iklan Layanan Masyarakat',
                'deskripsi' => 'Penayangan pesan informasi atau imbauan untuk kepentingan publik.',
                'tarif_regular' => 100000,
                'tarif_prime' => 175000,
            ],
        ];

        foreach ($katalogs as $item) {
            $katalog = Katalog::updateOrCreate(
                ['nama_layanan' => $item['nama_layanan']],
                ['deskripsi' => $item['deskripsi']]
            );

            Tarif::updateOrCreate(
                ['katalog_id' => $katalog->id, 'waktu' => 'regular'],
                ['tarif' => $item['tarif_regular']]
            );

            Tarif::updateOrCreate(
                ['katalog_id' => $katalog->id, 'waktu' => 'prime'],
                ['tarif' => $item['tarif_prime']]
            );
        }
    }
}
