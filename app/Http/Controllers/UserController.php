<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private array $roles = [
        'admin' => 'Admin Sistem',
        'kepala_stasiun' => 'Kepala Stasiun',
        'lpu' => 'LPU',
        'penyetor' => 'Penyetor',
    ];

    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', [
            'users' => $users,
            'roles' => $this->roles,
            'adminCount' => $this->adminCount(),
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => $this->roles,
            'kepalaStasiunExists' => $this->hasKepalaStasiun(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'nip' => ['nullable', 'regex:/^\d{18}$/'],
            'role' => ['required', Rule::in(array_keys($this->roles))],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], $this->validationMessages());

        if ($validated['role'] === 'kepala_stasiun' && $this->hasKepalaStasiun()) {
            return back()
                ->withErrors(['role' => 'Akun Kepala Stasiun sudah ada. Role ini hanya boleh dimiliki satu akun.'])
                ->withInput();
        }

        User::create($validated + [
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Akun berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user,
            'roles' => $this->roles,
            'kepalaStasiunTakenByOther' => $this->hasKepalaStasiun($user),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nip' => ['nullable', 'regex:/^\d{18}$/'],
            'role' => ['required', Rule::in(array_keys($this->roles))],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], $this->validationMessages());

        if ($user->is(auth()->user()) && $validated['role'] !== $user->normalizedRole()) {
            return back()
                ->withErrors(['role' => 'Role akun yang sedang login tidak bisa diubah.'])
                ->withInput();
        }

        if ($validated['role'] === 'kepala_stasiun' && $this->hasKepalaStasiun($user)) {
            return back()
                ->withErrors(['role' => 'Akun Kepala Stasiun sudah ada. Role ini hanya boleh dimiliki satu akun.'])
                ->withInput();
        }

        if ($user->isKepsta() && $validated['role'] !== 'kepala_stasiun' && ! $this->hasKepalaStasiun($user)) {
            return back()
                ->withErrors(['role' => 'Role Kepala Stasiun tidak bisa dilepas karena harus selalu ada satu akun Kepala Stasiun.'])
                ->withInput();
        }

        if (blank($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->isKepsta()) {
            return back()->with('error', 'Akun Kepala Stasiun tidak bisa dihapus.');
        }

        if ($user->isAdmin() && ! $this->hasAdmin($user)) {
            return back()->with('error', 'Admin Sistem terakhir tidak bisa dihapus.');
        }

        if ($user->is(auth()->user())) {
            return back()->with('error', 'Akun yang sedang login tidak bisa dihapus.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Akun berhasil dihapus.');
    }

    private function validationMessages(): array
    {
        return [
            'nip.regex' => 'NIP harus berisi tepat 18 digit angka.',
        ];
    }

    private function hasKepalaStasiun(?User $exceptUser = null): bool
    {
        return User::query()
            ->whereIn('role', ['kepala_stasiun', 'Kepala Stasiun', 'atasan', 'kepsta'])
            ->when($exceptUser, fn ($query) => $query->whereKeyNot($exceptUser->getKey()))
            ->exists();
    }

    private function hasAdmin(?User $exceptUser = null): bool
    {
        return User::query()
            ->where('role', 'admin')
            ->when($exceptUser, fn ($query) => $query->whereKeyNot($exceptUser->getKey()))
            ->exists();
    }

    private function adminCount(): int
    {
        return User::where('role', 'admin')->count();
    }
}
