<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with('invoice.pks.client')
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;

                $query->where(function ($q) use ($keyword) {
                    $q->where('nomor_pembayaran', 'like', '%' . $keyword . '%')
                        ->orWhere('kode_billing', 'like', '%' . $keyword . '%')
                        ->orWhere('ntpn', 'like', '%' . $keyword . '%')
                        ->orWhereHas('invoice', function ($invoiceQuery) use ($keyword) {
                            $invoiceQuery->where('nomor_invoice', 'like', '%' . $keyword . '%')
                                ->orWhereHas('pks', function ($pksQuery) use ($keyword) {
                                    $pksQuery->where('judul', 'like', '%' . $keyword . '%')
                                        ->orWhere('nomor', 'like', '%' . $keyword . '%')
                                        ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                                            $clientQuery->where('nama', 'like', '%' . $keyword . '%');
                                        });
                                });
                        });
                });
            })
            ->when($request->filled('tanggal'), function ($query) use ($request) {
                $query->whereDate('tanggal_pembayaran', $request->tanggal);
            })
            ->orderByDesc('tanggal_pembayaran')
            ->latest()
            ->get();

        return view('payment.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoices = Invoice::with('pks.client')
            ->where('status', '!=', 'paid')
            ->doesntHave('payments')
            ->latest()
            ->get();

        $selectedInvoiceId = $request->query('invoice_id');

        return view('payment.create', compact('invoices', 'selectedInvoiceId'));
    }

    public function store(Request $request)
    {
        $request->validate($this->paymentValidationRules([
            'invoice_id' => 'required|exists:invoices,id',
        ]), $this->validationMessages());

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($invoice->status === 'paid' || $invoice->payments()->exists()) {
            return back()
                ->withErrors(['invoice_id' => 'Invoice ini sudah memiliki pembayaran.'])
                ->withInput();
        }

        if (!$invoice->kode_billing) {
            return back()
                ->withErrors(['invoice_id' => 'Invoice ini belum memiliki kode billing SIMPONI.'])
                ->withInput();
        }

        if (abs((float) $request->jumlah_pembayaran - (float) $invoice->nominal) > 0.01) {
            return back()
                ->withErrors(['jumlah_pembayaran' => 'Jumlah pembayaran harus sama dengan nominal invoice.'])
                ->withInput();
        }

        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        $tahun = date('Y');
        $bulan = date('m');
        $urut = Payment::whereYear('created_at', $tahun)->count() + 1;
        $nomorPembayaran = 'KWT/' . str_pad($urut, 4, '0', STR_PAD_LEFT) . '/PKS/RRI-BTM/' . $bulan . '/' . $tahun;

        $payment = new Payment();
        $payment->nomor_pembayaran = $nomorPembayaran;
        $payment->invoice_id = $invoice->id;
        $payment->tanggal_pembayaran = $request->tanggal_pembayaran;
        $payment->kode_billing = $request->kode_billing ?: $invoice->kode_billing;
        $payment->ntpn = $request->ntpn;
        $payment->jumlah_pembayaran = $request->jumlah_pembayaran;
        $payment->catatan = $request->catatan;
        $payment->bukti_pembayaran_path = $buktiPath;
        $payment->kwitansi_penyetor_nama = $request->kwitansi_penyetor_nama;
        $payment->kwitansi_penyetor_nip = $request->kwitansi_penyetor_nip;
        $payment->kwitansi_kepala_stasiun_nama = $request->kwitansi_kepala_stasiun_nama;
        $payment->kwitansi_kepala_stasiun_nip = $request->kwitansi_kepala_stasiun_nip;
        $payment->save();

        $invoice->update(['status' => 'paid']);

        return redirect()->route('invoice.show', $payment->invoice_id)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function edit($id)
    {
        $payment = Payment::with('invoice.pks.client')->findOrFail($id);

        return view('payment.edit', compact('payment'));
    }

    public function update(Request $request, $id)
    {
        $payment = Payment::with('invoice')->findOrFail($id);

        $request->validate($this->paymentValidationRules(), $this->validationMessages());

        if (abs((float) $request->jumlah_pembayaran - (float) $payment->invoice->nominal) > 0.01) {
            return back()
                ->withErrors(['jumlah_pembayaran' => 'Jumlah pembayaran harus sama dengan nominal invoice.'])
                ->withInput();
        }

        if ($request->hasFile('bukti_pembayaran')) {
            if ($payment->bukti_pembayaran_path) {
                Storage::disk('public')->delete($payment->bukti_pembayaran_path);
            }

            $payment->bukti_pembayaran_path = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
        }

        $payment->tanggal_pembayaran = $request->tanggal_pembayaran;
        $payment->kode_billing = $request->kode_billing ?: $payment->invoice->kode_billing;
        $payment->ntpn = $request->ntpn;
        $payment->jumlah_pembayaran = $request->jumlah_pembayaran;
        $payment->catatan = $request->catatan;
        $payment->kwitansi_penyetor_nama = $request->kwitansi_penyetor_nama;
        $payment->kwitansi_penyetor_nip = $request->kwitansi_penyetor_nip;
        $payment->kwitansi_kepala_stasiun_nama = $request->kwitansi_kepala_stasiun_nama;
        $payment->kwitansi_kepala_stasiun_nip = $request->kwitansi_kepala_stasiun_nip;
        $payment->save();

        return redirect()->route('payment.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function cetakKwitansi($id)
    {
        $payment = Payment::with('invoice.pks.client')->findOrFail($id);

        $pdf = Pdf::loadView('payment.kwitansi', compact('payment'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'defaultMediaType' => 'print',
            ]);

        return $pdf->stream('Kwitansi-' . $payment->id . '.pdf');
    }

    private function validationMessages(): array
    {
        return [
            'kwitansi_penyetor_nip.regex' => 'NIP penyetor kwitansi harus berisi tepat 18 digit angka.',
            'kwitansi_kepala_stasiun_nip.regex' => 'NIP kepala stasiun kwitansi harus berisi tepat 18 digit angka.',
        ];
    }

    private function paymentValidationRules(array $extraRules = []): array
    {
        return $extraRules + [
            'tanggal_pembayaran' => 'required|date',
            'kode_billing' => 'nullable|string|max:255',
            'ntpn' => 'nullable|string|max:255',
            'jumlah_pembayaran' => 'required|numeric|min:1',
            'catatan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kwitansi_penyetor_nama' => 'required|string|max:255',
            'kwitansi_penyetor_nip' => ['required', 'regex:/^\d{18}$/'],
            'kwitansi_kepala_stasiun_nama' => 'required|string|max:255',
            'kwitansi_kepala_stasiun_nip' => ['required', 'regex:/^\d{18}$/'],
        ];
    }
}
