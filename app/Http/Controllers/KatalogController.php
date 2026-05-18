<?php

namespace App\Http\Controllers;

use App\Models\Katalog;
use Illuminate\Http\Request;
use App\Models\Tarif;

class KatalogController extends Controller
{
    public function index()
    {
        $katalogs = Katalog::latest()->get();
        return view('katalog.index', compact('katalogs'));
    }

    public function create()
{
    $katalogs = Katalog::all();
    $tarifs = Tarif::all();

    return view('katalog.create', compact('katalogs','tarifs'));
}
    public function store(Request $request)
{
    $request->validate([
        'nama_layanan' => 'required',
        'tarif_regular' => 'required|numeric|min:0',
        'tarif_prime' => 'required|numeric|min:0',
    ]);

    $katalog = Katalog::create([
        'nama_layanan' => $request->nama_layanan,
        'deskripsi' => $request->deskripsi,
    ]);

    Tarif::create([
        'katalog_id' => $katalog->id,
        'waktu' => 'regular',
        'tarif' => $request->tarif_regular,
    ]);

    Tarif::create([
        'katalog_id' => $katalog->id,
        'waktu' => 'prime',
        'tarif' => $request->tarif_prime,
    ]);

    return redirect()->route('katalog.index');
}

   public function edit($id)
{
    $katalog = Katalog::with('tarifs')->findOrFail($id);

    $tarifRegular = $katalog->tarifs->where('waktu','regular')->first();
    $tarifPrime = $katalog->tarifs->where('waktu','prime')->first();

    return view('katalog.edit', compact('katalog','tarifRegular','tarifPrime'));
}

    public function update(Request $request, $id)
{
    $request->validate([
        'nama_layanan' => 'required',
        'tarif_regular' => 'required|numeric|min:0',
        'tarif_prime' => 'required|numeric|min:0',
    ]);

    $katalog = Katalog::findOrFail($id);

    // update katalog
    $katalog->update([
        'nama_layanan' => $request->nama_layanan,
        'deskripsi' => $request->deskripsi,
    ]);

   
    Tarif::updateOrCreate(
        ['katalog_id' => $katalog->id, 'waktu' => 'regular'],
        ['tarif' => $request->tarif_regular]
    );

    Tarif::updateOrCreate(
        ['katalog_id' => $katalog->id, 'waktu' => 'prime'],
        ['tarif' => $request->tarif_prime]
    );

    return redirect()->route('katalog.index')->with('success','Produk diupdate');
}

    public function destroy($id)
    {
        Katalog::findOrFail($id)->delete();

        return back()->with('success', 'Katalog berhasil dihapus');
    }
}