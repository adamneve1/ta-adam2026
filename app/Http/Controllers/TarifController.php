<?php

namespace App\Http\Controllers;

use App\Models\Tarif;
use App\Models\Katalog;
use Illuminate\Http\Request;

class TarifController extends Controller
{
    public function index()
    {
        $tarifs = Tarif::with('katalog')->latest()->get();
        return view('tarif.index', compact('tarifs'));
    }

    public function create()
    {
        $katalogs = Katalog::all();
        return view('tarif.create', compact('katalogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'katalog_id' => 'required|exists:katalogs,id',
            'waktu' => 'required|in:regular,prime',
            'tarif' => 'required|numeric|min:0'
        ]);

        Tarif::create($request->all());

        return redirect()->route('tarif.index')
            ->with('success', 'Tarif berhasil ditambahkan');
    }

    public function edit($id)
    {
        $tarif = Tarif::findOrFail($id);
        $katalogs = Katalog::all();

        return view('tarif.edit', compact('tarif','katalogs'));
    }

    public function update(Request $request, $id)
    {
        $tarif = Tarif::findOrFail($id);

        $request->validate([
            'katalog_id' => 'required',
            'waktu' => 'required',
            'tarif' => 'required|numeric'
        ]);

        $tarif->update($request->all());

        return redirect()->route('tarif.index');
    }

    public function destroy($id)
    {
        Tarif::findOrFail($id)->delete();
        return back();
    }
}