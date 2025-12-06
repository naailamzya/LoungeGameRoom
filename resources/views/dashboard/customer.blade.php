@extends('layouts.app')

@section('title', 'Dashboard Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-home"></i> Selamat Datang, {{ auth()->user()->name }}!</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Anda login sebagai <strong>Customer</strong>. Silakan pilih ruangan game untuk melakukan reservasi.</p>
            </div>
        </div>
    </div>
</div>

<!-- Available Game Rooms -->
<div class="row mb-4">
    <div class="col-12">
        <h4 class="mb-3"><i class="fas fa-door-open"></i> Ruangan Game Tersedia</h4>
    </div>
    @forelse($gameRooms as $room)
    <div class="col-md-4 mb-4">
        <div class="card game-room-card h-100">
            @if($room->image)
            <img src="{{ asset('storage/' . $room->image) }}" class="card-img-top" alt="{{ $room->name }}" style="height: 200px; object-fit: cover;">
            @else
            <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                <i class="fas fa-gamepad fa-4x text-white"></i>
            </div>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $room->name }}</h5>
                <p class="card-text">{{ Str::limit($room->description, 100) }}</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-users"></i> Kapasitas: {{ $room->capacity }} orang</li>
                    <li><i class="fas fa-money-bill"></i> Harga: {{ $room->formatted_price }}/jam</li>
                    <li>
                        @if($room->status === 'available')
                        <span class="badge bg-success">Tersedia</span>
                        @else
                        <span class="badge bg-danger">Maintenance</span>
                        @endif
                    </li>
                </ul>
            </div>
            <div class="card-footer bg-transparent">
                @if($room->status === 'available')
                <a href="{{ route('reservations.create', ['game_room_id' => $room->id]) }}" class="btn btn-primary w-100">
                    <i class="fas fa-calendar-plus"></i> Reservasi Sekarang
                </a>
                @else
                <button class="btn btn-secondary w-100" disabled>
                    <i class="fas fa-tools"></i> Tidak Tersedia
                </button>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada ruangan game tersedia.
        </div>
    </div>
    @endforelse
</div>

<!-- My Recent Reservations -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Reservasi Terakhir Saya</h5>
            </div>
            <div class="card-body">
                @if($myReservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($myReservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong></td>
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
                <div class="text-center mt-3">
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lihat Semua Reservasi
                    </a>
                </div>
                @else
                <p class="text-center mb-0">Anda belum memiliki reservasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection