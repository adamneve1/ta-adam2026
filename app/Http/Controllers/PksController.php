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
        'items.*.katalog_id' => 'required|exists:katalogs,id',
        'items.*.qty' => 'required|numeric|min:1',
        'items.*.tarif' => 'required|numeric|min:0',
    ]);

    
    if (!$request->client_id && empty($request->client['nama'])) {
        return back()->withErrors(['client' => 'Client wajib diisi']);
    }

   
    if ($request->client_id) {
        $clientId = $request->client_id;
    } else {
        $client = Client::create($request->client);
        $clientId = $client->id;
    }

   
    $last = Pks::latest()->first();
    $number = 'PKS-' . date('Y') . '-' . str_pad(($last->id ?? 0) + 1, 4, '0', STR_PAD_LEFT);


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

    foreach ($request->items as $item) {
        $subtotal = $item['qty'] * $item['tarif'];
        $total += $subtotal;

        PksItem::create([
            'pks_id' => $pks->id,
            'katalog_id' => $item['katalog_id'],
            'waktu' => $item['waktu'],
            'channel' => $item['channel'],
            'qty' => $item['qty'],
            'tarif' => $item['tarif'],
            'subtotal' => $subtotal,
        ]);
    }

    
    $pks->update(['total' => $total]);

    return redirect()->back()->with('success', 'PKS berhasil dibuat');
}
}