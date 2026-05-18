<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Katalog;
use App\Models\Pks;
use App\Models\PksItem;
use App\Models\Client;
use App\Models\Tarif;

class PksController extends Controller
{


public function index()
{
    $pks = Pks::with('client')->latest()->get();

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
    'items' => 'required|array|min:1',

    'items.*.katalog_id' => 'nullable|exists:katalogs,id',
    'items.*.qty' => 'nullable|numeric|min:1',
    'items.*.waktu' => 'nullable|in:regular,prime',
    'items.*.channel' => 'nullable',
    'items.*.tanggal_mulai' => 'nullable|date',
'items.*.tanggal_selesai' => 'nullable|date|after_or_equal:items.*.tanggal_mulai',
    'items.*.tarif' => 'nullable|numeric|min:0',
]);
// Ambil data items dari request, pastikan tipenya array
$inputItems = $request->input('items', []);

// Filter hanya yang punya katalog_id dan qty yang valid (bukan nol/kosong)
$items = array_filter($inputItems, function ($item) {
    return isset($item['katalog_id']) && $item['katalog_id'] != '' && 
           isset($item['qty']) && $item['qty'] > 0;
});

if (empty($items)) {
    return back()->withInput()->withErrors(['items' => 'Minimal 1 item harus diisi dengan benar (Katalog & Qty wajib ada).']);
}

    
    if (!$request->client_id && empty($request->client['nama'])) {
        return back()->withErrors(['client' => 'Client wajib diisi']);
    }

   
   if ($request->client_id) {
    $clientId = $request->client_id;
} else {

    // VALIDASI DULU
    $request->validate([
        'client.nama' => 'required'
    ]);

    //  BARU CREATE
    $client = Client::create($request->client);
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

    return redirect()->back()->with('success', 'PKS berhasil dibuat');
}
public function cetak($id)
{
    $pks = Pks::with(['client','items.katalog'])->findOrFail($id);

    return view('pks.cetak', compact('pks'));
}
}