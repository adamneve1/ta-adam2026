<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $statusOptions = [
            'belum_billing' => 'Billing Belum Dibuat',
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'overdue' => 'Lewat Tempo',
            'paid' => 'Lunas',
        ];
        $today = Carbon::today();

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
            ->when($request->filled('status') && array_key_exists($request->status, $statusOptions), function ($query) use ($request, $today) {
                if ($request->status === 'paid') {
                    $query->where('status', Invoice::STATUS_PAID);
                    return;
                }

                $query->where('status', '!=', Invoice::STATUS_PAID);

                if ($request->status === 'overdue') {
                    $query->whereDate('tanggal_jatuh_tempo', '<', $today);
                    return;
                }

                if ($request->status === 'belum_billing') {
                    $query->where(function ($q) {
                        $q->whereNull('kode_billing')
                            ->orWhere('kode_billing', '');
                    });
                    return;
                }

                $query->whereNotNull('kode_billing')
                    ->where('kode_billing', '!=', '')
                    ->whereDate('tanggal_jatuh_tempo', '>=', $today);
            })
            ->latest('tanggal_invoice')
            ->paginate(10)
            ->withQueryString();

        return view('invoice.index', compact('invoices', 'statusOptions'));
    }

    public function create(Request $request)
    {
        $pksList = $this->getPksListWithInvoiceTotals();
        $selectedPksId = $request->query('pks_id');
        $kepalaStasiunDefault = $this->getKepalaStasiunDefault();

        $defaultNomorInvoice = $this->generateNomorInvoice();

        return view('invoice.create', compact('pksList', 'selectedPksId', 'defaultNomorInvoice', 'kepalaStasiunDefault'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pks_id' => 'required|exists:pks,id',
            'nomor_invoice' => 'nullable|string',
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

        if ($pks->invoices()->exists()) {
            return back()
                ->withErrors(['pks_id' => 'Kontrak PKS ini sudah memiliki invoice.'])
                ->withInput();
        }

        if (abs((float) $request->nominal - (float) $pks->total) > 0.01) {
            return $this->backWithContractTotalError((float) $pks->total);
        }

        $tanggalJatuhTempo = $this->getTanggalJatuhTempoInvoice($pks);
        $nomorInvoice = $this->generateNomorInvoice($request->tanggal_invoice);

        Invoice::create([
            'pks_id' => $request->pks_id,
            'nomor_invoice' => $nomorInvoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'status' => $request->filled('kode_billing')
                ? Invoice::STATUS_MENUNGGU_PEMBAYARAN
                : Invoice::STATUS_BELUM_BILLING,
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
        if ($invoice->isPaid()) {
            return redirect()->route('invoice.show', $invoice->id)->with('error', 'Invoice yang sudah lunas tidak dapat diubah.');
        }

        $pksList = $this->getPksListWithInvoiceTotals($invoice->id, $invoice->pks_id);
        $kepalaStasiunDefault = $this->getKepalaStasiunDefault();

        return view('invoice.edit', compact('invoice', 'pksList', 'kepalaStasiunDefault'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // Mencegah update jika invoice sudah lunas
        if ($invoice->isPaid()) {
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

        $pksId = (int) $request->pks_id;
        $pks = Pks::findOrFail($pksId);

        if ($pksId !== (int) $invoice->pks_id && $pks->invoices()->exists()) {
            return back()
                ->withErrors(['pks_id' => 'Kontrak PKS ini sudah memiliki invoice.'])
                ->withInput();
        }

        if (abs((float) $request->nominal - (float) $pks->total) > 0.01) {
            return $this->backWithContractTotalError((float) $pks->total);
        }

        $tanggalJatuhTempo = $this->getTanggalJatuhTempoInvoice($pks);

        $invoice->update([
            'pks_id' => $pksId,
            'nomor_invoice' => $request->nomor_invoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
            'kode_billing' => $request->kode_billing,
            'status' => $request->filled('kode_billing')
                ? Invoice::STATUS_MENUNGGU_PEMBAYARAN
                : Invoice::STATUS_BELUM_BILLING,
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

        if ($invoice->isPaid()) {
            return back()->with('error', 'Kode Billing invoice yang sudah lunas tidak dapat diubah.');
        }

        $invoice->update([
            'kode_billing' => $request->kode_billing,
            'status' => Invoice::STATUS_MENUNGGU_PEMBAYARAN,
        ]);

        return back()->with('success', 'Kode Billing SIMPONI berhasil diperbarui');
    }

    public function cetak($id)
    {
        // 1. Ambil data invoice beserta relasi PKS dan Client-nya
        $invoice = Invoice::with(['pks.client', 'pks.items.katalog'])->findOrFail($id);

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

    private function getPksListWithInvoiceTotals(?int $excludedInvoiceId = null, ?int $currentPksId = null)
    {
        $pksList = Pks::with('client')
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

        return $pksList->filter(function ($pks) use ($currentPksId) {
            return $pks->invoices_count === 0 || ($currentPksId && $pks->id === $currentPksId);
        })->values();
    }

    private function getTanggalJatuhTempoInvoice(Pks $pks): string
    {
        $pks->loadMissing('items');

        $tanggalTerakhirPenyiaran = $pks->items->max('tanggal_selesai')
            ?? $pks->items->max('tanggal_mulai')
            ?? $pks->tanggal;

        return \Carbon\Carbon::parse($tanggalTerakhirPenyiaran)
            ->addDays(20)
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

    private function backWithContractTotalError(float $contractTotal)
    {
        return back()
            ->withErrors([
                'nominal' => 'Nominal invoice harus sama dengan total kontrak PKS: Rp ' . number_format($contractTotal, 0, ',', '.'),
            ])
            ->withInput();
    }

    private function getKepalaStasiunDefault(): ?User
    {
        return User::whereIn('role', ['kepala_stasiun', 'Kepala Stasiun', 'atasan', 'kepsta'])->latest()->first();
    }

    private function validationMessages(): array
    {
        return [
            'penyetor_nip.regex' => 'NIP penyetor harus berisi tepat 18 digit angka.',
            'kepala_stasiun_nip.regex' => 'NIP kepala stasiun harus berisi tepat 18 digit angka.',
        ];
    }

    private function generateNomorInvoice(?string $tanggalInvoice = null): string
    {
        $date = $tanggalInvoice ? Carbon::parse($tanggalInvoice) : Carbon::now();
        $year = $date->format('Y');
        $month = $date->format('m');
        $prefix = '/KEU/INV/RRI-BTM/';

        $lastSequence = Invoice::where('nomor_invoice', 'like', '%/KEU/INV/RRI-BTM/%/' . $year)
            ->pluck('nomor_invoice')
            ->map(function ($nomor) {
                return (int) strtok($nomor, '/');
            })
            ->max() ?? 0;

        $sequence = $lastSequence + 1;

        do {
            $nomorInvoice = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT)
                . $prefix
                . $month
                . '/'
                . $year;
            $sequence++;
        } while (Invoice::where('nomor_invoice', $nomorInvoice)->exists());

        return $nomorInvoice;
    }
}
