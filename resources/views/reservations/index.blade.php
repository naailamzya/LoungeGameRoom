@extends('layouts.app')

@section('title', 'Daftar Reservasi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-calendar-check"></i> Daftar Reservasi</h5>
                @if(auth()->user()->isCustomer())
                <a href="{{ route('reservations.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Buat Reservasi Baru
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Booking</th>
                                @if(!auth()->user()->isCustomer())
                                <th>Customer</th>
                                <th>Telepon</th>
                                @endif
                                <th>Ruangan</th>
                                <th>Tanggal</th>
                                <th>Waktu</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                            <tr>
                                <td><strong>{{ $reservation->booking_code }}</strong></td>
                                @if(!auth()->user()->isCustomer())
                                <td>{{ $reservation->user->name }}</td>
                                <td>{{ $reservation->user->phone }}</td>
                                @endif
                                <td>{{ $reservation->gameRoom->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d/m/Y') }}</td>
                                <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</td>
                                <td>{{ $reservation->duration_hours }} jam</td>
                                <td>{{ $reservation->formatted_total_price }}</td>
                                <td>
                                    <span class="badge bg-{{ $reservation->status_badge }}">
                                        {{ $reservation->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if(auth()->user()->isCustomer())
                                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                                            <a href="{{ route('reservations.edit', $reservation->id) }}" class="btn btn-sm btn-warning" title="Edit/Reschedule">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            @if($reservation->status === 'pending')
                                            <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-danger" title="Batalkan">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endif

                                            @if(in_array($reservation->status, ['pending', 'cancelled']))
                                            <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus reservasi ini? Data tidak dapat dikembalikan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-dark" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        @endif

                                        @if(!auth()->user()->isCustomer() && $reservation->status === 'pending')
                                        <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="confirmed">
                                            <button type="submit" class="btn btn-sm btn-success" title="Konfirmasi" onclick="return confirm('Konfirmasi reservasi ini?')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endif

                                        @if(!auth()->user()->isCustomer() && $reservation->status === 'confirmed')
                                        <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="status" value="completed">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Selesai" onclick="return confirm('Tandai selesai?')">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center mb-0">
                    Belum ada reservasi. 
                    @if(auth()->user()->isCustomer())
                    <a href="{{ route('reservations.create') }}">Buat reservasi sekarang</a>
                    @endif
                </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection