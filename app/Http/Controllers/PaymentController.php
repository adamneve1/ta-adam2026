<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class PaymentController extends Controller
{
    private const SIMPONI_IMPORT_RULES_VERSION = 'requires_invoice_v1';

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
            ->paginate(10)
            ->withQueryString();

        return view('payment.index', compact('payments'));
    }

    public function create(Request $request)
    {
        $invoices = Invoice::with('pks.client')
            ->where('status', '!=', 'paid')
            ->whereNotNull('kode_billing')
            ->doesntHave('payments')
            ->latest()
            ->get();

        $selectedInvoiceId = $request->query('invoice_id');

        return view('payment.create', compact('invoices', 'selectedInvoiceId'));
    }

    public function importSimponi()
    {
        if (session('simponi_import_rules_version') !== self::SIMPONI_IMPORT_RULES_VERSION) {
            session()->forget(['simponi_import_preview', 'simponi_import_valid_rows']);
        }

        return view('payment.import-simponi', [
            'previewRows' => session('simponi_import_preview', []),
            'validRows' => session('simponi_import_valid_rows', []),
        ]);
    }

    public function previewImportSimponi(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'file.required' => 'File CSV rekap SIMPONI wajib diupload.',
            'file.mimes' => 'File harus berformat CSV atau TXT.',
        ]);

        $parsedRows = $this->parseSimponiCsv($request->file('file')->getRealPath());
        $previewRows = $this->buildSimponiPreviewRows($parsedRows);
        $validRows = collect($previewRows)
            ->where('is_valid', true)
            ->pluck('payload')
            ->values()
            ->all();

        session([
            'simponi_import_preview' => $previewRows,
            'simponi_import_valid_rows' => $validRows,
            'simponi_import_rules_version' => self::SIMPONI_IMPORT_RULES_VERSION,
        ]);

        return view('payment.import-simponi', compact('previewRows', 'validRows'));
    }

    public function resetImportSimponi()
    {
        session()->forget([
            'simponi_import_preview',
            'simponi_import_valid_rows',
            'simponi_import_rules_version',
        ]);

        return redirect()
            ->route('payment.import-simponi')
            ->with('success', 'Preview import SIMPONI sudah dibersihkan.');
    }

    public function storeImportSimponi()
    {
        if (session('simponi_import_rules_version') !== self::SIMPONI_IMPORT_RULES_VERSION) {
            session()->forget(['simponi_import_preview', 'simponi_import_valid_rows']);

            return redirect()
                ->route('payment.import-simponi')
                ->with('error', 'Aturan import berubah. Silakan preview ulang file CSV terlebih dahulu.');
        }

        $validRows = session('simponi_import_valid_rows', []);

        if (empty($validRows)) {
            return redirect()
                ->route('payment.import-simponi')
                ->with('error', 'Tidak ada baris valid yang bisa diimport. Upload dan preview file CSV terlebih dahulu.');
        }

        $imported = 0;

        DB::transaction(function () use ($validRows, &$imported) {
            $tahun = date('Y');
            $bulan = date('m');
            $urut = Payment::whereYear('created_at', $tahun)->count() + 1;

            foreach ($validRows as $row) {
                $invoice = Invoice::where('kode_billing', $row['kode_billing'])->first();

                if (!$invoice || $invoice->isPaid() || $invoice->payments()->exists()) {
                    continue;
                }

                if (Payment::where('ntpn', $row['ntpn'])->exists()) {
                    continue;
                }

                if (abs((float) $row['jumlah_pembayaran'] - (float) $invoice->nominal) > 0.01) {
                    continue;
                }

                $payment = new Payment();
                $payment->nomor_pembayaran = 'KWT/' . str_pad((string) $urut, 4, '0', STR_PAD_LEFT) . '/PKS/RRI-BTM/' . $bulan . '/' . $tahun;
                $payment->invoice_id = $invoice->id;
                $payment->tanggal_pembayaran = $row['tanggal_pembayaran'];
                $payment->kode_billing = $invoice->kode_billing;
                $payment->ntpn = $row['ntpn'];
                $payment->ntb = $row['ntb'] ?: null;
                $payment->jumlah_pembayaran = $row['jumlah_pembayaran'];
                $payment->catatan = $row['catatan'];
                $payment->kwitansi_penyetor_nama = $invoice->penyetor_nama;
                $payment->kwitansi_penyetor_nip = $invoice->penyetor_nip;
                $payment->kwitansi_kepala_stasiun_nama = $invoice->kepala_stasiun_nama;
                $payment->kwitansi_kepala_stasiun_nip = $invoice->kepala_stasiun_nip;
                $payment->save();

                $invoice->update(['status' => Invoice::STATUS_PAID]);

                $urut++;
                $imported++;
            }
        });

        session()->forget(['simponi_import_preview', 'simponi_import_valid_rows']);

        return redirect()
            ->route('payment.index')
            ->with('success', $imported . ' pembayaran berhasil diimport dari rekap SIMPONI.');
    }

    public function store(Request $request)
    {
        $request->validate($this->paymentValidationRules([
            'invoice_id' => 'required|exists:invoices,id',
        ]), $this->validationMessages());

        $invoice = Invoice::findOrFail($request->invoice_id);

        if ($invoice->isPaid() || $invoice->payments()->exists()) {
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
        $payment->kode_billing = $invoice->kode_billing;
        $payment->ntpn = $request->ntpn;
        $payment->ntb = $request->filled('ntb') ? $request->ntb : null;
        $payment->jumlah_pembayaran = $request->jumlah_pembayaran;
        $payment->catatan = $request->catatan;
        $payment->bukti_pembayaran_path = $buktiPath;
        $payment->kwitansi_penyetor_nama = $request->kwitansi_penyetor_nama;
        $payment->kwitansi_penyetor_nip = $request->kwitansi_penyetor_nip;
        $payment->kwitansi_kepala_stasiun_nama = $request->kwitansi_kepala_stasiun_nama;
        $payment->kwitansi_kepala_stasiun_nip = $request->kwitansi_kepala_stasiun_nip;
        $payment->save();

        $invoice->update(['status' => Invoice::STATUS_PAID]);

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

        if ($payment->invoice && abs((float) $request->jumlah_pembayaran - (float) $payment->invoice->nominal) > 0.01) {
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
        $payment->kode_billing = $payment->invoice->kode_billing ?? $payment->kode_billing;
        $payment->ntpn = $request->ntpn;
        $payment->ntb = $request->filled('ntb') ? $request->ntb : $payment->ntb;
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
            'ntpn.required' => 'NTPN wajib diisi untuk mengonfirmasi pembayaran.',
            'kwitansi_penyetor_nip.regex' => 'NIP penyetor kwitansi harus berisi tepat 18 digit angka.',
            'kwitansi_kepala_stasiun_nip.regex' => 'NIP kepala stasiun kwitansi harus berisi tepat 18 digit angka.',
        ];
    }

    private function paymentValidationRules(array $extraRules = []): array
    {
        return $extraRules + [
            'tanggal_pembayaran' => 'required|date',
            'ntpn' => 'required|string|max:255',
            'ntb' => 'nullable|string|max:255',
            'jumlah_pembayaran' => 'required|numeric|min:1',
            'catatan' => 'nullable|string',
            'bukti_pembayaran' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'kwitansi_penyetor_nama' => 'required|string|max:255',
            'kwitansi_penyetor_nip' => ['required', 'regex:/^\d{18}$/'],
            'kwitansi_kepala_stasiun_nama' => 'required|string|max:255',
            'kwitansi_kepala_stasiun_nip' => ['required', 'regex:/^\d{18}$/'],
        ];
    }

    private function parseSimponiCsv(string $path): array
    {
        $handle = fopen($path, 'r');

        if (!$handle) {
            return [];
        }

        $firstLine = fgets($handle);
        rewind($handle);

        $delimiter = $this->detectCsvDelimiter($firstLine ?: '');
        $headers = fgetcsv($handle, 0, $delimiter) ?: [];
        $headers = array_map(fn ($header) => $this->normalizeCsvHeader($header), $headers);
        $rows = [];

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (collect($data)->every(fn ($value) => blank($value))) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $header) {
                $row[$header] = trim((string) ($data[$index] ?? ''));
            }

            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    private function buildSimponiPreviewRows(array $rows): array
    {
        $seenNtpn = [];

        return collect($rows)->map(function ($row, $index) use (&$seenNtpn) {
            $kodeBilling = $row['KODEBILLING'] ?? '';
            $ntpn = $row['NTPN'] ?? '';
            $ntb = $row['NTBNTP'] ?? '';
            $keterangan = $row['KETERANGAN'] ?? '';
            $namaWajibBayar = $row['NAMAWAJIBBAYARBAYARSETOR'] ?? '';
            $jumlahPembayaran = $this->parseRupiahValue($row['SETORANPERAKUNRP'] ?? '');
            $tanggalPembayaran = $this->parseSimponiDate($row['TANGGALBAYAR'] ?? '');
            $invoice = $kodeBilling ? Invoice::with('pks.client')->where('kode_billing', $kodeBilling)->first() : null;
            $errors = [];

            if (!$kodeBilling) {
                $errors[] = 'Kode billing kosong.';
            }

            if (!$tanggalPembayaran) {
                $errors[] = 'Tanggal bayar tidak terbaca.';
            }

            if (!$ntpn) {
                $errors[] = 'NTPN kosong.';
            } elseif (Payment::where('ntpn', $ntpn)->exists()) {
                $errors[] = 'NTPN sudah pernah diimport/dicatat.';
            } elseif (in_array($ntpn, $seenNtpn, true)) {
                $errors[] = 'NTPN duplikat di file CSV.';
            }

            if ($ntpn) {
                $seenNtpn[] = $ntpn;
            }

            if (!$invoice) {
                $errors[] = 'Invoice dengan kode billing ini belum dibuat.';
            } else {
                if ($invoice->isPaid()) {
                    $errors[] = 'Invoice sudah lunas.';
                }

                if ($invoice->payments()->exists()) {
                    $errors[] = 'Invoice sudah memiliki pembayaran.';
                }

                if (abs($jumlahPembayaran - (float) $invoice->nominal) > 0.01) {
                    $errors[] = 'Nominal setoran tidak sama dengan nominal invoice.';
                }
            }

            if ($jumlahPembayaran <= 0) {
                $errors[] = 'Setoran per akun kosong atau tidak valid.';
            }

            $catatan = trim('Import SIMPONI'
                . ($namaWajibBayar ? ' - Wajib bayar: ' . $namaWajibBayar : '')
                . ($keterangan ? ' - ' . $keterangan : ''));

            return [
                'row_number' => $index + 2,
                'kode_billing' => $kodeBilling,
                'tanggal_pembayaran' => $tanggalPembayaran,
                'ntpn' => $ntpn,
                'ntb' => $ntb,
                'nama_wajib_bayar' => $namaWajibBayar,
                'jumlah_pembayaran' => $jumlahPembayaran,
                'invoice_nomor' => $invoice->nomor_invoice ?? '-',
                'client' => $invoice->pks->client->nama ?? '-',
                'invoice_nominal' => $invoice ? (float) $invoice->nominal : 0,
                'invoice_match_status' => $invoice ? 'Invoice internal ditemukan' : 'Invoice internal belum dibuat',
                'errors' => $errors,
                'is_valid' => empty($errors),
                'payload' => [
                    'kode_billing' => $kodeBilling,
                    'tanggal_pembayaran' => $tanggalPembayaran,
                    'ntpn' => $ntpn,
                    'ntb' => $ntb,
                    'jumlah_pembayaran' => $jumlahPembayaran,
                    'catatan' => $catatan,
                ],
            ];
        })->values()->all();
    }

    private function detectCsvDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t"];
        $counts = [];

        foreach ($delimiters as $delimiter) {
            $counts[$delimiter] = substr_count($line, $delimiter);
        }

        arsort($counts);

        return array_key_first($counts) ?: ',';
    }

    private function normalizeCsvHeader(?string $header): string
    {
        $header = preg_replace('/^\xEF\xBB\xBF/', '', (string) $header);

        return preg_replace('/[^A-Z0-9]/', '', strtoupper($header));
    }

    private function parseRupiahValue(?string $value): float
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        $value = str_replace(['Rp', 'rp', ' ', "\xc2\xa0"], '', $value);

        if (str_contains($value, ',') && str_contains($value, '.')) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } elseif (substr_count($value, '.') > 1) {
            $value = str_replace('.', '', $value);
        } elseif (str_contains($value, ',')) {
            $value = str_replace(',', '.', $value);
        }

        $value = preg_replace('/[^0-9.\-]/', '', $value);

        return is_numeric($value) ? (float) $value : 0;
    }

    private function parseSimponiDate(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::create(1899, 12, 30)->addDays((int) $value)->toDateString();
        }

        foreach (['Y-m-d', 'd/m/Y', 'd-m-Y', 'm/d/Y'] as $format) {
            try {
                return Carbon::createFromFormat($format, $value)->toDateString();
            } catch (\Throwable) {
                //
            }
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }
}
