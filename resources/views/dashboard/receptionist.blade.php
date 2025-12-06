@extends('layouts.app')

@section('title', 'Dashboard Receptionist')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-user-tie"></i> Dashboard Receptionist</h5>
            </div>
            <div class="card-body">
                <p class="mb-0">Selamat datang, <strong>{{ auth()->user()->name }}</strong>. Kelola reservasi pelanggan di sini.</p>
            </div>
        </div>
    </div>
</div>

<!-- Pending Reservations -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Reservasi Menunggu Konfirmasi</h5>
                <span class="badge bg-warning">{{ $pendingReservations->count() }}</span>
            </div>
            <div class="card-body">
                @if($pendingReservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Customer</th>
                                <th>Telepon</th>
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Total</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingReservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong></td>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->user->phone }}</td>
                                <td>{{ $reservation->gameRoom->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                                <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                                <td>{{ $reservation->formatted_total_price }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi" onclick="return confirm('Konfirmasi reservasi ini?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Batalkan" onclick="return confirm('Batalkan reservasi ini?')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center mb-0">Tidak ada reservasi yang menunggu konfirmasi.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Today's Reservations -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-day"></i> Reservasi Hari Ini</h5>
                <span class="badge bg-info">{{ $todayReservations->count() }}</span>
            </div>
            <div class="card-body">
                @if($todayReservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                <th>Customer</th>
                                <th>Ruangan</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($todayReservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong></td>
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->gameRoom->name }}</td>
                                <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                                <td>{{ $reservation->duration_hours }} jam</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status_badge }}">
                                        {{ $reservation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($reservation->status === 'confirmed')
                                    <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="completed">
                                        <button type="submit" class="btn btn-sm btn-success" title="Selesai" onclick="return confirm('Tandai selesai?')">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center mb-0">Tidak ada reservasi untuk hari ini.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection