<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Pks;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapitulasiController extends Controller
{
    public function penerimaan(Request $request)
    {
        $filters = $this->getDateFilters($request);
        $payments = $this->penerimaanQuery($filters['tanggal_mulai'], $filters['tanggal_selesai'])->get();
        $totalPenerimaan = $payments->sum('jumlah_pembayaran');

        return view('rekapitulasi.penerimaan', compact('payments', 'totalPenerimaan', 'filters'));
    }

    public function exportPenerimaan(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'signers' => 'required|array|size:3',
            'signers.*.posisi' => 'required|string|max:255',
            'signers.*.nama' => 'required|string|max:255',
            'signers.*.nip' => ['required', 'regex:/^\d{18}$/'],
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'signers.*.posisi.required' => 'Posisi/jabatan penandatangan wajib diisi.',
            'signers.*.nama.required' => 'Nama penandatangan wajib diisi.',
            'signers.*.nip.required' => 'NIP penandatangan wajib diisi.',
            'signers.*.nip.regex' => 'NIP penandatangan harus berisi tepat 18 digit angka.',
        ]);

        $filters = $this->getDateFilters($request);
        $payments = $this->penerimaanQuery($filters['tanggal_mulai'], $filters['tanggal_selesai'])->get();
        $totalPenerimaan = $payments->sum('jumlah_pembayaran');
        $signers = collect($validated['signers'])->values();

        $pdf = Pdf::loadView('rekapitulasi.penerimaan-pdf', compact('payments', 'totalPenerimaan', 'filters', 'signers'))
            ->setPaper('a4', 'landscape')
            ->setOption([
                'defaultMediaType' => 'print',
                'enable_remote' => true,
            ]);

        $filename = 'Rekapitulasi-Penerimaan-' . $filters['tanggal_mulai'] . '-sd-' . $filters['tanggal_selesai'] . '.pdf';

        return $pdf->stream($filename);
    }

    public function kerjaSama(Request $request)
    {
        $filters = $this->getDateFilters($request);
        $pksList = $this->kerjaSamaQuery($filters['tanggal_mulai'], $filters['tanggal_selesai'])->get();
        $totalNilaiKerjaSama = $pksList->sum('total');

        return view('rekapitulasi.kerja-sama', compact('pksList', 'totalNilaiKerjaSama', 'filters'));
    }

    public function exportKerjaSama(Request $request)
    {
        $validated = $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'signers' => 'required|array|size:2',
            'signers.*.posisi' => 'required|string|max:255',
            'signers.*.nama' => 'required|string|max:255',
            'signers.*.nip' => ['required', 'regex:/^\d{18}$/'],
        ], [
            'tanggal_selesai.after_or_equal' => 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.',
            'signers.*.posisi.required' => 'Posisi/jabatan penandatangan wajib diisi.',
            'signers.*.nama.required' => 'Nama penandatangan wajib diisi.',
            'signers.*.nip.required' => 'NIP penandatangan wajib diisi.',
            'signers.*.nip.regex' => 'NIP penandatangan harus berisi tepat 18 digit angka.',
        ]);

        $filters = $this->getDateFilters($request);
        $pksList = $this->kerjaSamaQuery($filters['tanggal_mulai'], $filters['tanggal_selesai'])->get();
        $totalNilaiKerjaSama = $pksList->sum('total');
        $signers = collect($validated['signers'])->values();

        $pdf = Pdf::loadView('rekapitulasi.kerja-sama-pdf', compact('pksList', 'totalNilaiKerjaSama', 'filters', 'signers'))
            ->setPaper('a4', 'landscape')
            ->setOption([
                'defaultMediaType' => 'print',
                'enable_remote' => true,
            ]);

        $filename = 'Rekapitulasi-Kerja-Sama-PNBP-' . $filters['tanggal_mulai'] . '-sd-' . $filters['tanggal_selesai'] . '.pdf';

        return $pdf->stream($filename);
    }

    private function getDateFilters(Request $request): array
    {
        $tanggalMulai = $request->input('tanggal_mulai') ?: Carbon::now()->startOfMonth()->toDateString();
        $tanggalSelesai = $request->input('tanggal_selesai') ?: Carbon::now()->toDateString();

        return [
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'tanggal_mulai_label' => Carbon::parse($tanggalMulai)->locale('id')->translatedFormat('d F Y'),
            'tanggal_selesai_label' => Carbon::parse($tanggalSelesai)->locale('id')->translatedFormat('d F Y'),
        ];
    }

    private function penerimaanQuery(string $tanggalMulai, string $tanggalSelesai)
    {
        return Payment::with('invoice.pks.client')
            ->whereBetween('tanggal_pembayaran', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal_pembayaran')
            ->orderBy('id');
    }

    private function kerjaSamaQuery(string $tanggalMulai, string $tanggalSelesai)
    {
        return Pks::with(['client', 'items.katalog', 'invoices' => function ($query) {
                $query->latest();
            }])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal')
            ->orderBy('id');
    }
}
