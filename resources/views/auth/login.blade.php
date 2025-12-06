<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lounge Game Room</title>
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
        }

        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }

        .btn-login {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            border: none;
            color: white;
            padding: 12px;
            font-weight: 600;
        }

        .btn-login:hover {
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
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="login-header">
                        <i class="fas fa-gamepad"></i>
                        <h3 class="mb-0">Lounge Game Room</h3>
                        <p class="mb-0">Silakan login untuk melanjutkan</p>
                    </div>
                    <div class="card-body p-4">
                        @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i> Password
                                </label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                <label class="form-check-label" for="remember">
                                    Ingat saya
                                </label>
                            </div>

                            <button type="submit" class="btn btn-login w-100 mb-3">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>

                            <div class="text-center">
                                <p class="mb-0">Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-none">Daftar disini</a></p>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <small class="text-dark">
                        <strong>Demo Accounts:</strong><br>
                        Customer: customer@test.com / password<br>
                        Receptionist: receptionist@test.com / password<br>
                        Manager: manager@test.com / password
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>