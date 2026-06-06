@extends('layouts.app')

@section('title', 'Pengaturan Profil - PNBP RRI')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 text-dark">Pengaturan Profil</h3>
            <p class="text-muted mb-0">Kelola informasi akun dan password yang digunakan untuk masuk aplikasi.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Profil berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            Password berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-person-lines-fill text-primary me-2"></i>
                        Informasi Profil
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Nama</label>
                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $user->email) }}"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nip" class="form-label fw-semibold">NIP</label>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   class="form-control @error('nip') is-invalid @enderror"
                                   value="{{ old('nip', $user->nip) }}"
                                   inputmode="numeric"
                                   pattern="[0-9]{18}"
                                   minlength="18"
                                   maxlength="18"
                                   placeholder="18 digit angka">
                            @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="alert alert-warning py-2">
                                Email belum diverifikasi.
                                <button form="send-verification" class="btn btn-link btn-sm p-0 align-baseline">
                                    Kirim ulang email verifikasi
                                </button>
                            </div>
                        @endif

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Profil
                            </button>
                        </div>
                    </form>

                    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-lock text-primary me-2"></i>
                        Ubah Password
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="update_password_current_password" class="form-label fw-semibold">Password Saat Ini</label>
                            <input type="password"
                                   name="current_password"
                                   id="update_password_current_password"
                                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="current-password">
                            @error('current_password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password" class="form-label fw-semibold">Password Baru</label>
                            <input type="password"
                                   name="password"
                                   id="update_password_password"
                                   class="form-control @error('password', 'updatePassword') is-invalid @enderror"
                                   autocomplete="new-password">
                            @error('password', 'updatePassword')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="update_password_password_confirmation" class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password"
                                   name="password_confirmation"
                                   id="update_password_password_confirmation"
                                   class="form-control"
                                   autocomplete="new-password">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-key me-1"></i> Simpan Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            @if(auth()->user()->isLpu())
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Hapus Akun
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Akun yang dihapus tidak bisa digunakan lagi untuk masuk ke aplikasi.
                        </p>
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteProfileModal">
                            <i class="bi bi-trash me-1"></i> Hapus Akun Saya
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if(auth()->user()->isLpu())
    <div class="modal fade" id="deleteProfileModal" tabindex="-1" aria-labelledby="deleteProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form method="POST" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('DELETE')

                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProfileModalLabel">Konfirmasi Hapus Akun</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted">
                            Masukkan password untuk menghapus akun ini secara permanen.
                        </p>

                        <label for="delete_password" class="form-label fw-semibold">Password</label>
                        <input type="password"
                               name="password"
                               id="delete_password"
                               class="form-control @error('password', 'userDeletion') is-invalid @enderror"
                               autocomplete="current-password">
                        @error('password', 'userDeletion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i> Hapus Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
