<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private array $roles = [
        'Kepala Stasiun' => 'Kepala Stasiun',
        'lpu' => 'LPU',
        'penyetor' => 'Penyetor',
    ];

    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', [
            'users' => $users,
            'roles' => $this->roles,
        ]);
    }

    public function create()
    {
        return view('users.create', [
            'roles' => $this->roles,
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

        if ($user->is(auth()->user()) && $validated['role'] !== $user->role) {
            return back()
                ->withErrors(['role' => 'Role akun yang sedang login tidak bisa diubah.'])
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
}
