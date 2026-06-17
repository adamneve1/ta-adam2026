<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ config('app.name', 'PNBP RRI') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-rri.png') }}">

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            letter-spacing: 0;
            color: #263244;
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            text-rendering: optimizeLegibility;
        }

        .login-visual {
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 24% 18%, rgba(255, 255, 255, .24), transparent 18%),
                linear-gradient(160deg, #3d8bfd, #0d6efd 48%, #084298);
        }

        .login-visual::before,
        .login-visual::after {
            content: "";
            position: absolute;
            left: -8%;
            right: -8%;
            height: 42%;
            background-repeat: no-repeat;
            background-size: 100% 100%;
            opacity: .54;
        }

        .login-visual::before {
            bottom: 28%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 900 260' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 188L84 139L151 160L246 88L355 162L450 104L563 149L653 72L761 155L900 88V260H0Z' fill='%23084298'/%3E%3Cpath d='M0 182L84 132L151 153L246 80L355 153L450 96L563 141L653 64L761 147L900 80' fill='none' stroke='white' stroke-width='9' stroke-linecap='round' stroke-linejoin='round' opacity='.86'/%3E%3C/svg%3E");
        }

        .login-visual::after {
            bottom: -4%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 900 260' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0 118L89 77L169 111L265 46L378 127L500 72L608 125L726 58L900 118V260H0Z' fill='%23052c65'/%3E%3Cpath d='M0 113L89 72L169 105L265 40L378 119L500 66L608 118L726 52L900 112' fill='none' stroke='white' stroke-width='8' stroke-linecap='round' stroke-linejoin='round' opacity='.78'/%3E%3C/svg%3E");
            opacity: .72;
        }

        .visual-content {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px;
            color: #ffffff;
        }

        .brand-logo {
            width: 118px;
            height: auto;
            filter: brightness(0) invert(1);
        }

        .login-panel {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
        }

        .login-form {
            width: 100%;
            max-width: 360px;
        }

        .form-control,
        .btn {
            min-height: 44px;
            border-radius: 3px;
        }

        .form-control {
            border-color: #e5e7eb;
        }

        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 .18rem rgba(13, 110, 253, .12);
        }

        .btn-primary {
            background: #0d6efd;
            border-color: #0d6efd;
            border-radius: 22px;
        }

        .btn-primary:hover,
        .btn-primary:focus {
            background: #0b5ed7;
            border-color: #0a58ca;
        }

        @media (max-width: 991.98px) {
            .login-visual,
            .visual-content {
                min-height: 320px;
            }

            .visual-content {
                padding: 32px 24px;
            }

            .login-panel {
                min-height: auto;
                padding-top: 48px;
                padding-bottom: 48px;
            }
        }

        @media (max-width: 575.98px) {
            .login-visual,
            .visual-content {
                min-height: 220px;
            }

            .brand-logo {
                width: 96px;
            }
        }
    </style>
</head>
<body>
    <main class="container-fluid p-0">
        <div class="row g-0 min-vh-100">
            <section class="col-lg-6 login-visual" aria-label="Sistem PNBP RRI">
                <div class="visual-content">
                    <div>
                        <img src="{{ asset('images/RRI_Logo.png') }}" alt="Logo RRI" class="brand-logo mb-4">
                        <h1 class="h3 fw-semibold mb-2">Sistem PNBP RRI</h1>
                        <p class="mb-0 text-white-50">Pengelolaan PKS, invoice, dan pembayaran.</p>
                    </div>

                    <div class="small text-white-50">
                        &copy; {{ date('Y') }} PNBP RRI
                    </div>
                </div>
            </section>

            <section class="col-lg-6 login-panel">
                <div class="login-form">
                    <div class="mb-4">
                        <h2 class="h4 fw-semibold mb-3">Masuk ke sistem</h2>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success py-2" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger py-2" role="alert">
                            Email atau password belum sesuai.
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="visually-hidden">Email</label>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="Email"
                                   required
                                   autofocus
                                   autocomplete="username">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-2">
                            <label for="password" class="visually-hidden">Kata sandi</label>
                            <input id="password"
                                   type="password"
                                   name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="Kata sandi"
                                   required
                                   autocomplete="current-password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check small">
                                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                                <label class="form-check-label" for="remember_me">Ingat saya</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            Masuk
                        </button>

                        <p class="small text-secondary text-center mt-3 mb-0">
                            Jika akun bermasalah, hubungi admin.
                        </p>
                    </form>
                </div>
            </section>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
