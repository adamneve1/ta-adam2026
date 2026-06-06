<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pks;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with('pks.client')
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;

                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_invoice', 'like', '%' . $keyword . '%')
                        ->orWhereHas('pks', function ($pksQuery) use ($keyword) {
                            $pksQuery->where('judul', 'like', '%' . $keyword . '%')
                                ->orWhere('nomor', 'like', '%' . $keyword . '%')
                                ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                                    $clientQuery->where('nama', 'like', '%' . $keyword . '%');
                                });
                        });
                });
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('tanggal_invoice', $request->tanggal);
            })
            ->latest()
            ->get();

        return view('invoice.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $pksList = $this->getPksListWithInvoiceTotals();
        $selectedPksId = $request->query('pks_id');
        $kepalaStasiunDefault = $this->getKepalaStasiunDefault();

        // 1. Ambil jumlah Invoice yang terbuat di tahun berjalan saat ini
        $last = Invoice::whereYear('created_at', date('Y'))->count();
        $urut = $last + 1;
        // 2. Format nomor urut dinas RRI Batam (Contoh: 0001/KEU/INV/RRI-BTM/05/2026)
        $defaultNomorInvoice = str_pad($urut, 4, '0', STR_PAD_LEFT)
            . '/KEU/INV/RRI-BTM/'
            . date('m')
            . '/'
            . date('Y');
        // 3. Kirim variabel $defaultNomorInvoice ke view
        return view('invoice.create', compact('pksList', 'selectedPksId', 'defaultNomorInvoice', 'kepalaStasiunDefault'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pks_id' => 'required|exists:pks,id',
            'nomor_invoice' => 'required|string|unique:invoices,nomor_invoice',
            'nominal' => 'required|numeric|min:1',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'kode_billing' => 'nullable|string',
            'penyetor_nama' => 'required|string|max:255',
            'penyetor_nip' => ['required', 'regex:/^\d{18}$/'],
            'kepala_stasiun_nama' => 'required|string|max:255',
            'kepala_stasiun_nip' => ['required', 'regex:/^\d{18}$/'],
        ], $this->validationMessages());

        $pks = Pks::findOrFail($request->pks_id);
        $sisaKontrak = $this->getSisaKontrak($pks);
        $tanggalJatuhTempo = $this->getTanggalJatuhTempoInvoice($pks);

        if ((float) $request->nominal > $sisaKontrak) {
            return $this->backWithNominalError($sisaKontrak);
        }

        Invoice::create([
            'pks_id' => $request->pks_id,
            'nomor_invoice' => $request->nomor_invoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'status' => 'unpaid',
            'kode_billing' => $request->kode_billing,
            'penyetor_nama' => $request->penyetor_nama,
            'penyetor_nip' => $request->penyetor_nip,
            'kepala_stasiun_nama' => $request->kepala_stasiun_nama,
            'kepala_stasiun_nip' => $request->kepala_stasiun_nip,
        ]);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['pks.client', 'pks.items', 'payments' => function ($query) {
            $query->orderByDesc('tanggal_pembayaran')->latest();
        }])->findOrFail($id);

        return view('invoice.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Mencegah edit jika invoice sudah lunas
        if ($invoice->status === 'paid') {
            return redirect()->route('invoice.show', $invoice->id)->with('error', 'Invoice yang sudah lunas tidak dapat diubah.');
        }

        $pksList = $this->getPksListWithInvoiceTotals($invoice->id);
        $kepalaStasiunDefault = $this->getKepalaStasiunDefault();

        return view('invoice.edit', compact('invoice', 'pksList', 'kepalaStasiunDefault'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Mencegah update jika invoice sudah lunas
        if ($invoice->status === 'paid') {
            return redirect()->route('invoice.show', $invoice->id)->with('error', 'Invoice yang sudah lunas tidak dapat diubah.');
        }
        
        $request->validate([
            'pks_id' => 'required|exists:pks,id',
            'nomor_invoice' => 'required|string|unique:invoices,nomor_invoice,' . $invoice->id,
            'nominal' => 'required|numeric|min:1',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'kode_billing' => 'nullable|string',
            'penyetor_nama' => 'required|string|max:255',
            'penyetor_nip' => ['required', 'regex:/^\d{18}$/'],
            'kepala_stasiun_nama' => 'required|string|max:255',
            'kepala_stasiun_nip' => ['required', 'regex:/^\d{18}$/'],
        ], $this->validationMessages());

        $pks = Pks::findOrFail($request->pks_id);
        $sisaKontrak = $this->getSisaKontrak($pks, $invoice->id);
        $tanggalJatuhTempo = $this->getTanggalJatuhTempoInvoice($pks);

        if ((float) $request->nominal > $sisaKontrak) {
            return $this->backWithNominalError($sisaKontrak);
        }

        $invoice->update([
            'pks_id' => $request->pks_id,
            'nomor_invoice' => $request->nomor_invoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'kode_billing' => $request->kode_billing,
            'penyetor_nama' => $request->penyetor_nama,
            'penyetor_nip' => $request->penyetor_nip,
            'kepala_stasiun_nama' => $request->kepala_stasiun_nama,
            'kepala_stasiun_nip' => $request->kepala_stasiun_nip,
        ]);

        return redirect()->route('invoice.show', $invoice->id)->with('success', 'Invoice berhasil diperbarui');
    }

    public function updateBilling(Request $request, $id)
    {
        $request->validate([
            'kode_billing' => 'required|string',
        ]);

        $invoice = Invoice::findOrFail($id);
        $invoice->update([
            'kode_billing' => $request->kode_billing,
        ]);

        return back()->with('success', 'Kode Billing SIMPONI berhasil diperbarui');
    }

    public function cetak($id)
    {
        // 1. Ambil data invoice beserta relasi PKS dan Client-nya
        $invoice = Invoice::with(['pks.client'])->findOrFail($id);

        // 2. Load halaman Blade khusus cetak dan konversi menjadi PDF A4 Portrait
        $pdf = Pdf::loadView('invoice.cetak', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'defaultMediaType' => 'print',
                'enable_remote' => true,
                'isPhpEnabled' => true,
            ]);

        // 3. Alirkan (stream) PDF langsung ke tab browser baru
        return $pdf->stream('Invoice-' . str_replace('/', '-', $invoice->nomor_invoice) . '.pdf');
    }

    private function getPksListWithInvoiceTotals(?int $excludedInvoiceId = null)
    {
        return Pks::with('client')
            ->withMax('items as tanggal_selesai_terakhir', 'tanggal_selesai')
            ->withMax('items as tanggal_mulai_terakhir', 'tanggal_mulai')
            ->withCount(['invoices' => function ($query) use ($excludedInvoiceId) {
                $query->when($excludedInvoiceId, function ($query) use ($excludedInvoiceId) {
                    $query->where('id', '!=', $excludedInvoiceId);
                });
            }])
            ->withSum(['invoices as total_ditagihkan' => function ($query) use ($excludedInvoiceId) {
                $query->when($excludedInvoiceId, function ($query) use ($excludedInvoiceId) {
                    $query->where('id', '!=', $excludedInvoiceId);
                });
            }], 'nominal')
            ->latest()
            ->get();
    }

    private function getTanggalJatuhTempoInvoice(Pks $pks): string
    {
        $pks->loadMissing('items');

        $tanggalTerakhirPenyiaran = $pks->items->max('tanggal_selesai')
            ?? $pks->items->max('tanggal_mulai')
            ?? $pks->tanggal;

        return \Carbon\Carbon::parse($tanggalTerakhirPenyiaran)
            ->addDays(28)
            ->toDateString();
    }

    private function getSisaKontrak(Pks $pks, ?int $excludedInvoiceId = null): float
    {
        $totalDitagihkan = Invoice::where('pks_id', $pks->id)
            ->when($excludedInvoiceId, function ($query) use ($excludedInvoiceId) {
                $query->where('id', '!=', $excludedInvoiceId);
            })
            ->sum('nominal');

        return max((float) $pks->total - (float) $totalDitagihkan, 0);
    }

    private function backWithNominalError(float $sisaKontrak)
    {
        return back()
            ->withErrors([
                'nominal' => 'Nominal invoice melebihi sisa kontrak. Sisa yang bisa ditagihkan: Rp ' . number_format($sisaKontrak, 0, ',', '.'),
            ])
            ->withInput();
    }

    private function getKepalaStasiunDefault(): ?User
    {
        return User::whereIn('role', ['Kepala Stasiun', 'atasan'])->latest()->first();
    }

    private function validationMessages(): array
    {
        return [
            'penyetor_nip.regex' => 'NIP penyetor harus berisi tepat 18 digit angka.',
            'kepala_stasiun_nip.regex' => 'NIP kepala stasiun harus berisi tepat 18 digit angka.',
        ];
    }
}
