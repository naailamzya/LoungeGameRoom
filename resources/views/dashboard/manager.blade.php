@extends('layouts.app')

@section('title', 'Dashboard Manager')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> Dashboard Manager</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Kelola semua aspek lounge game room.</p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Ruangan</p>
                    <h3 class="mb-0">{{ $totalRooms }}</h3>
                </div>
                <i class="fas fa-door-open"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Reservasi</p>
                    <h3 class="mb-0">{{ $totalReservations }}</h3>
                </div>
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Pendapatan</p>
                    <h3 class="mb-0" style="font-size: 1.5rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="mb-0">Total Customer</p>
                    <h3 class="mb-0">{{ $totalCustomers }}</h3>
                </div>
                <i class="fas fa-users"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Aksi Cepat</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('game-rooms.create') }}" class="btn btn-primary w-100">
                            <i class="fas fa-plus"></i> Tambah Ruangan Baru
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('game-rooms.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-door-open"></i> Kelola Ruangan
                        </a>
                    </div>
                    <div class="col-md-4 mb-2">
                        <a href="{{ route('reservations.index') }}" class="btn btn-success w-100">
                            <i class="fas fa-list"></i> Lihat Semua Reservasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Game Rooms Overview -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistik Ruangan</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama Ruangan</th>
                                <th>Kapasitas</th>
                                <th>Harga/Jam</th>
                                <th>Total Reservasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gameRooms as $room)
                            <tr>
                                <td><strong>{{ $room->name }}</strong></td>
                                <td>{{ $room->capacity }} orang</td>
                                <td>{{ $room->formatted_price }}</td>
                                <td>{{ $room->reservations_count }} kali</td>
                                <td>
                                    @if($room->status === 'available')
                                    <span class="badge bg-success">Tersedia</span>
                                    @else
                                    <span class="badge bg-danger">Maintenance</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Reservations -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Reservasi Terbaru</h5>
            </div>
            <div class="card-body">
                @if($recentReservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Customer</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentReservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong></td>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->gameRoom->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                                <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                                <td>{{ $reservation->formatted_total_price }}</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status_badge }}">
                                        {{ $reservation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center mb-0">Belum ada reservasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection