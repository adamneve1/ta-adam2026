<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    private array $jenisKlien = [
        'Instansi Pemerintahan',
        'Perusahaan Swasta',
        'BUMN',
        'BUMD',
        'Lembaga',
        'Organisasi Nirlaba',
        'Perorangan',
    ];

    public function index(Request $request)
    {
        $clients = Client::withCount('pks')
            ->when($request->filled('keyword'), function ($query) use ($request) {
                $keyword = $request->keyword;

                $query->where(function ($q) use ($keyword) {
                    $q->where('nama', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%')
                        ->orWhere('nama_narahubung', 'like', '%' . $keyword . '%')
                        ->orWhere('no_narahubung', 'like', '%' . $keyword . '%');
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        $jenisKlien = $this->jenisKlien;

        return view('clients.create', compact('jenisKlien'));
    }

    public function store(Request $request)
    {
        Client::create($this->validateClient($request));

        return redirect()->route('clients.index')->with('success', 'Data client berhasil ditambahkan.');
    }

    public function edit(Client $client)
    {
        $jenisKlien = $this->jenisKlien;

        return view('clients.edit', compact('client', 'jenisKlien'));
    }

    public function update(Request $request, Client $client)
    {
        $client->update($this->validateClient($request));

        return redirect()->route('clients.index')->with('success', 'Data client berhasil diperbarui.');
    }

    public function destroy(Client $client)
    {
        if ($client->pks()->exists()) {
            return back()->with('error', 'Client tidak bisa dihapus karena sudah digunakan pada data PKS.');
        }

        $client->delete();

        return back()->with('success', 'Data client berhasil dihapus.');
    }

    private function validateClient(Request $request): array
    {
        return $request->validate([
            'jenis_klien' => 'required|in:' . implode(',', $this->jenisKlien),
            'nama' => 'required|string|max:255',
            'nama_narahubung' => 'nullable|string|max:255',
            'no_narahubung' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'nama_penanggung_jawab' => 'nullable|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'agen_rri' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'catatan' => 'nullable|string',
        ]);
    }
}
