<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Pks;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('pks.client')->latest()->get();
        return view('invoice.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $pksList = Pks::with('client')->latest()->get();
        $selectedPksId = $request->query('pks_id');

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
        return view('invoice.create', compact('pksList', 'selectedPksId', 'defaultNomorInvoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pks_id' => 'required|exists:pks,id',
            'nomor_invoice' => 'required|string|unique:invoices,nomor_invoice',
            'nominal' => 'required|numeric|min:0',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'kode_billing' => 'nullable|string',
        ]);

        Invoice::create([
            'pks_id' => $request->pks_id,
            'nomor_invoice' => $request->nomor_invoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'status' => 'unpaid',
            'kode_billing' => $request->kode_billing,
        ]);

        return redirect()->route('invoice.index')->with('success', 'Invoice berhasil dibuat');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['pks.client'])->findOrFail($id);
        return view('invoice.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        // Mencegah edit jika invoice sudah lunas
        if ($invoice->status === 'paid') {
            return redirect()->route('invoice.show', $invoice->id)->with('error', 'Invoice yang sudah lunas tidak dapat diubah.');
        }

        $pksList = Pks::with('client')->latest()->get();
        return view('invoice.edit', compact('invoice', 'pksList'));
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
            'nominal' => 'required|numeric|min:0',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'required|date|after_or_equal:tanggal_invoice',
            'kode_billing' => 'nullable|string',
        ]);

        $invoice->update([
            'pks_id' => $request->pks_id,
            'nomor_invoice' => $request->nomor_invoice,
            'nominal' => $request->nominal,
            'tanggal_invoice' => $request->tanggal_invoice,
            'tanggal_jatuh_tempo' => $request->tanggal_jatuh_tempo,
            'kode_billing' => $request->kode_billing,
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
            ]);

        // 3. Alirkan (stream) PDF langsung ke tab browser baru
        return $pdf->stream('Invoice-' . str_replace('/', '-', $invoice->nomor_invoice) . '.pdf');
    }w  

    
}
