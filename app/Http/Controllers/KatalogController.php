<?php

namespace App\Http\Controllers;

use App\Models\Katalog;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index()
    {
        $katalogs = Katalog::latest()->get();
        return view('katalog.index', compact('katalogs'));
    }

    public function create()
    {
        return view('katalog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        Katalog::create($request->only('nama_layanan','deskripsi'));

        return redirect()->route('katalog.index')
            ->with('success', 'Katalog berhasil ditambahkan');
    }

    public function edit($id)
    {
        $katalog = Katalog::findOrFail($id);
        return view('katalog.edit', compact('katalog'));
    }

    public function update(Request $request, $id)
    {
        $katalog = Katalog::findOrFail($id);

        $request->validate([
            'nama_layanan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string'
        ]);

        $katalog->update($request->only('nama_layanan','deskripsi'));

        return redirect()->route('katalog.index')
            ->with('success', 'Katalog berhasil diupdate');
    }

    public function destroy($id)
    {
        Katalog::findOrFail($id)->delete();

        return back()->with('success', 'Katalog berhasil dihapus');
    }
}