<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\Pks;
use App\Models\PksItem;
use App\Models\Client;
use App\Models\Tarif;
use Barryvdh\DomPDF\Facade\Pdf;

class PksController extends Controller
{


public function index(Request $request)
{
    $pks = Pks::with('client')
        ->withCount('invoices')
        ->withSum('invoices as total_ditagihkan', 'nominal')
        ->when($request->filled('keyword'), function ($query) use ($request) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('nomor', 'like', '%' . $keyword . '%')
                    ->orWhere('nomor_referensi', 'like', '%' . $keyword . '%')
                    ->orWhere('judul', 'like', '%' . $keyword . '%')
                    ->orWhereHas('client', function ($clientQuery) use ($keyword) {
                        $clientQuery->where('nama', 'like', '%' . $keyword . '%');
                    });
            });
        })
        ->when($request->filled('tanggal'), function ($query) use ($request) {
            $query->whereDate('tanggal', $request->tanggal);
        })
        ->orderByDesc('tanggal')
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return view('pks.index', compact('pks'));
}
public function create()
{
    $katalogs = Katalog::all();
    $clients = Client::all();
    $tarifs = Tarif::all();

    return view('pks.create', compact('katalogs', 'clients', 'tarifs'));
}

  public function store(Request $request)
{
    //VALIDASI
   $request->validate([
    'judul' => 'required',
    'tanggal' => 'required|date',
    'client_mode' => 'required|in:registered,new',
    'client_id' => 'nullable|exists:clients,id',
    'items' => 'required|array|min:1',

    'items.*.katalog_id' => 'nullable|exists:katalogs,id',
    'items.*.qty' => 'nullable|numeric|min:1',
    'items.*.waktu' => 'nullable|in:regular,prime',
    'items.*.channel' => 'nullable',
    'items.*.tanggal_mulai' => 'required|date',
    'items.*.tanggal_selesai' => 'required|date|after_or_equal:items.*.tanggal_mulai',
    'items.*.tarif' => 'nullable|numeric|min:0',
]);
// Ambil data items dari request, pastikan tipenya array
$inputItems = $request->input('items', []);

// Filter hanya yang punya katalog_id dan qty yang valid (bukan nol/kosong)
$items = array_filter($inputItems, function ($item) {
    return isset($item['katalog_id']) && $item['katalog_id'] != '' && 
           isset($item['qty']) && $item['qty'] > 0 &&
           !empty($item['tanggal_mulai']) &&
           !empty($item['tanggal_selesai']);
});

if (empty($items)) {
    return back()->withInput()->withErrors(['items' => 'Minimal 1 item harus diisi dengan benar (Katalog & Qty wajib ada).']);
}

    if ($request->client_mode === 'registered') {
        if (!$request->filled('client_id')) {
            return back()->withInput()->withErrors(['client_id' => 'Pilih client yang sudah terdaftar.']);
        }

        $clientId = $request->client_id;
    } else {
        $validatedClient = $request->validate([
            'client.jenis_klien' => 'required|in:Instansi Pemerintahan,Perusahaan Swasta,BUMN,BUMD,Lembaga,Organisasi Nirlaba,Perorangan',
            'client.nama' => 'required|string|max:255',
            'client.nama_narahubung' => 'required|string|max:255',
            'client.no_narahubung' => 'required|string|max:255',
            'client.email' => 'required|email|max:255',
            'client.nama_penanggung_jawab' => 'required|string|max:255',
            'client.jabatan' => 'required|string|max:255',
            'client.alamat' => 'required|string',
            'client.catatan' => 'nullable|string',
        ]);

        $client = Client::create($validatedClient['client']);
        $clientId = $client->id;
    }

   
   // ambil jumlah PKS tahun ini
$last = Pks::whereYear('created_at', date('Y'))->count();
$urut = $last + 1;

// generate nomor PKS
$number = str_pad($urut, 4, '0', STR_PAD_LEFT)
    . '/PKS/RRI-BTM/'
    . date('m')
    . '/'
    . date('Y');

    $pks = Pks::create([
    'nomor' => $number,
    'judul' => $request->judul,
    'nomor_referensi' => $request->nomor_referensi,
    'client_id' => $clientId,
    'deskripsi' => $request->deskripsi,
    'tanggal' => $request->tanggal,
    'total' => 0,
]);

    $total = 0;

   foreach ($items as $item) {

    // ambil tarif dari DB
    $tarifData = Tarif::where('katalog_id', $item['katalog_id'])
        ->where('waktu', $item['waktu'])
        ->first();

    if (!$tarifData) {
        return back()->withErrors('Tarif tidak ditemukan');
    }

    $tarif = $tarifData->tarif;

    $subtotal = $item['qty'] * $tarif;
    $total += $subtotal;

    PksItem::create([
        'pks_id' => $pks->id,
        'katalog_id' => $item['katalog_id'],
        'waktu' => $item['waktu'],
        'channel' => $item['channel'],
        'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
'tanggal_selesai' => $item['tanggal_selesai'] ?? null,  
        'qty' => $item['qty'],
        'tarif' => $tarif, // 🔥 dari DB
        'subtotal' => $subtotal,
    ]);
}

    
    $pks->update(['total' => $total]);

    return redirect()->route('pks.cetak', $pks->id)->with('success', 'PKS berhasil dibuat');
}

public function edit($id)
{
    $pks = Pks::with('items', 'client')->findOrFail($id);
    $katalogs = Katalog::all();
    $clients = Client::all();
    $tarifs = Tarif::all();

    return view('pks.edit', compact('pks', 'katalogs', 'clients', 'tarifs'));
}

public function update(Request $request, $id)
{
    $pks = Pks::findOrFail($id);

    // VALIDASI
    $request->validate([
        'judul' => 'required',
        'tanggal' => 'required|date',
        'client_mode' => 'required|in:registered,new',
        'client_id' => 'nullable|exists:clients,id',
        'items' => 'required|array|min:1',

        'items.*.katalog_id' => 'nullable|exists:katalogs,id',
        'items.*.qty' => 'nullable|numeric|min:1',
        'items.*.waktu' => 'nullable|in:regular,prime',
        'items.*.channel' => 'nullable',
        'items.*.tanggal_mulai' => 'required|date',
        'items.*.tanggal_selesai' => 'required|date|after_or_equal:items.*.tanggal_mulai',
        'items.*.tarif' => 'nullable|numeric|min:0',
    ]);

    $inputItems = $request->input('items', []);

    $items = array_filter($inputItems, function ($item) {
        return isset($item['katalog_id']) && $item['katalog_id'] != '' && 
               isset($item['qty']) && $item['qty'] > 0 &&
               !empty($item['tanggal_mulai']) &&
               !empty($item['tanggal_selesai']);
    });

    if (empty($items)) {
        return back()->withInput()->withErrors(['items' => 'Minimal 1 item harus diisi dengan benar (Katalog & Qty wajib ada).']);
    }

    if ($request->client_mode === 'registered') {
        if (!$request->filled('client_id')) {
            return back()->withInput()->withErrors(['client_id' => 'Pilih client yang sudah terdaftar.']);
        }

        $clientId = $request->client_id;
    } else {
        $validatedClient = $request->validate([
            'client.jenis_klien' => 'required|in:Instansi Pemerintahan,Perusahaan Swasta,BUMN,BUMD,Lembaga,Organisasi Nirlaba,Perorangan',
            'client.nama' => 'required|string|max:255',
            'client.nama_narahubung' => 'required|string|max:255',
            'client.no_narahubung' => 'required|string|max:255',
            'client.email' => 'required|email|max:255',
            'client.nama_penanggung_jawab' => 'required|string|max:255',
            'client.jabatan' => 'required|string|max:255',
            'client.alamat' => 'required|string',
            'client.catatan' => 'nullable|string',
        ]);

        $client = Client::create($validatedClient['client']);
        $clientId = $client->id;
    }

    $pks->update([
        'judul' => $request->judul,
        'nomor_referensi' => $request->nomor_referensi,
        'client_id' => $clientId,
        'deskripsi' => $request->deskripsi,
        'tanggal' => $request->tanggal,
    ]);

    // Hapus item lama
    PksItem::where('pks_id', $pks->id)->delete();

    $total = 0;

    foreach ($items as $item) {
        $tarifData = Tarif::where('katalog_id', $item['katalog_id'])
            ->where('waktu', $item['waktu'])
            ->first();

        if (!$tarifData) {
            return back()->withErrors('Tarif tidak ditemukan untuk layanan yang dipilih.');
        }

        $tarif = $tarifData->tarif;
        $subtotal = $item['qty'] * $tarif;
        $total += $subtotal;

        PksItem::create([
            'pks_id' => $pks->id,
            'katalog_id' => $item['katalog_id'],
            'waktu' => $item['waktu'],
            'channel' => $item['channel'],
            'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $item['tanggal_selesai'] ?? null,  
            'qty' => $item['qty'],
            'tarif' => $tarif,
            'subtotal' => $subtotal,
        ]);
    }
    
    $pks->update(['total' => $total]);

    return redirect()->route('pks.index')->with('success', 'PKS berhasil diperbarui');
}

public function destroy($id)
{
    $pks = Pks::findOrFail($id);
    
    // Cek apakah ada invoice terkait
    $hasInvoice = \App\Models\Invoice::where('pks_id', $pks->id)->exists();
    if($hasInvoice) {
        return back()->with('error', 'Gagal menghapus! PKS ini sudah memiliki Invoice.');
    }

    $pks->items()->delete();
    $pks->delete();

    return redirect()->route('pks.index')->with('success', 'PKS berhasil dihapus.');
}

public function preview(Request $request)
{
    $payloadRaw = $request->query('payload');
    $payload = $payloadRaw ? json_decode($payloadRaw, true) : [];

    $form = $payload['form'] ?? [];
    $items = $payload['items'] ?? [];
    $client = $payload['client'] ?? [];

    $mappedItems = collect($items)->map(function ($item) {
        return (object) [
            'katalog_id' => $item['katalog_id'] ?? null,
            'waktu' => $item['waktu'] ?? null,
            'channel' => $item['channel'] ?? null,
            'tanggal_mulai' => $item['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $item['tanggal_selesai'] ?? null,
            'qty' => (int) ($item['qty'] ?? 0),
            'tarif' => (int) ($item['tarif'] ?? 0),
            'subtotal' => (int) ($item['subtotal'] ?? 0),
            'katalog' => null,
        ];
    });

    $pks = (object) [
        'tanggal' => $form['tanggal'] ?? now()->toDateString(),
        'judul' => $form['judul'] ?? '',
        'nomor' => 'DRAFT',
        'nomor_referensi' => $form['nomor_referensi'] ?? '',
        'total' => $mappedItems->sum('subtotal'),
        'client' => (object) [
            'nama' => $client['nama'] ?? '',
            'nama_penanggung_jawab' => $client['nama_penanggung_jawab'] ?? '',
            'jabatan' => $client['jabatan'] ?? '',
            'alamat' => $client['alamat'] ?? '',
        ],
        'items' => $mappedItems,
    ];

    return Pdf::loadView('pks.cetak', compact('pks'))
        ->setPaper('a4', 'portrait')
        ->setOption([
            'defaultMediaType' => 'print',
        ])
        ->stream('pks-preview.pdf');
}

public function cetak($id)
{
    $pks = Pks::with(['client', 'items.katalog'])->findOrFail($id);

    return Pdf::loadView('pks.cetak', compact('pks'))
        ->setPaper('a4', 'portrait')
        ->setOption([
            'defaultMediaType' => 'print',
        ])
        ->stream('pks-'.$pks->id.'.pdf');
}
}
