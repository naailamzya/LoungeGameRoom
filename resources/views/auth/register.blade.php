<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Lounge Game Room</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-1: #D0D5EA;
            --color-2: #F0D2DA;
            --color-3: #B8DAED;
            --color-4: #BCC6E0;
            --color-5: #9EACCA;
        }

        body {
            background: linear-gradient(135deg, var(--color-1) 0%, var(--color-3) 50%, var(--color-5) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .register-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .register-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .btn-register {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .form-control:focus {
            border-color: var(--color-5);
            box-shadow: 0 0 0 0.2rem rgba(158, 172, 202, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card register-card">
                    <div class="register-header">
                        <i class="fas fa-user-plus"></i>
                        <h3 class="mb-0">Buat Akun Baru</h3>
                        <p class="mb-0">Daftar untuk mulai reservasi</p>
                    </div>
                    <div class="card-body p-4">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-user"></i> Nama Lengkap
                                </label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-phone"></i> Nomor Telepon/WhatsApp
                                </label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                                <small class="text-muted">Format: 08xxxxxxxxxx (untuk notifikasi WhatsApp)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" name="password" class="form-control" required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Konfirmasi Password
                                </label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>

                            <button type="submit" class="btn btn-register w-100 mb-3">
                                <i class="fas fa-user-plus"></i> Daftar
                            </button>

                            <div class="text-center">
                                <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}" class="text-decoration-none">Login disini</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>