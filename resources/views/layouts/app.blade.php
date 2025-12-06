<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - Lounge Game Room</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, var(--color-4), var(--color-5)) !important;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--color-4), var(--color-5));
            border: none;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-success {
            background: linear-gradient(90deg, #5cb85c, #4cae4c);
            border: none;
        }

        .btn-danger {
            background: linear-gradient(90deg, #d9534f, #c9302c);
            border: none;
        }

        .btn-warning {
            background: linear-gradient(90deg, #f0ad4e, #ec971f);
            border: none;
        }

        .btn-info {
            background: linear-gradient(90deg, var(--color-3), var(--color-4));
            border: none;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        .badge {
            padding: 0.5em 1em;
            border-radius: 20px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--color-5);
            box-shadow: 0 0 0 0.2rem rgba(158, 172, 202, 0.25);
        }

        .game-room-card {
            transition: all 0.3s;
            cursor: pointer;
        }

        .game-room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--color-1), var(--color-3));
            color: #333;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 20px;
        }

        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.3;
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-gamepad"></i> Lounge Game Room
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('dashboard') }}">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('reservations.index') }}">
                                <i class="fas fa-calendar-check"></i> Reservasi
                            </a>
                        </li>
                        @if(auth()->user()->isManager())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('game-rooms.index') }}">
                                <i class="fas fa-door-open"></i> Kelola Ruangan
                            </a>
                        </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><span class="dropdown-item-text"><small>Role: {{ ucfirst(auth()->user()->role) }}</small></span></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" id="logoutForm">
                                        @csrf
                                        <button type="submit" class="dropdown-item" aria-label="Logout">
                                            <i class="fas fa-sign-out-alt"></i> Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-4">
        <!-- Alert Messages -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" aria-label="Success message">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-label="Error message">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert" aria-label="Info message">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('payment_success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" aria-label="Payment success message">
            <i class="fas fa-check-circle"></i> {{ session('payment_success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-label="Error list">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5">
        <div class="container">
            <p class="text-dark mb-0">&copy; 2025 Lounge Game Room. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>