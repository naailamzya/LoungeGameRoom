@extends('layouts.app')

@section('title', 'Detail Reservasi')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> Detail Reservasi</h5>
                <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <!-- Booking Code and Status -->
                <div class="text-center mb-4">
                    <h3 class="mb-2">{{ $reservation->booking_code }}</h3>
                    <span class="badge bg-{{ $reservation->status_badge }} fs-6">
                        {{ $reservation->status_label }}
                    </span>
                </div>

                <hr>

                <!-- Customer Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user"></i> Informasi Customer</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="100">Nama</td>
                                <td>: <strong>{{ $reservation->user->name }}</strong></td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td>: {{ $reservation->user->email }}</td>
                            </tr>
                            <tr>
                                <td>Telepon</td>
                                <td>: {{ $reservation->user->phone }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-door-open"></i> Informasi Ruangan</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="100">Ruangan</td>
                                <td>: <strong>{{ $reservation->gameRoom->name }}</strong></td>
                            </tr>
                            <tr>
                                <td>Kapasitas</td>
                                <td>: {{ $reservation->gameRoom->capacity }} orang</td>
                            </tr>
                            <tr>
                                <td>Harga/Jam</td>
                                <td>: {{ $reservation->gameRoom->formatted_price }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Reservation Details -->
                <h6><i class="fas fa-calendar-alt"></i> Detail Reservasi</h6>
                <table class="table table-sm table-borderless mb-3">
                    <tr>
                        <td width="200">Tanggal Reservasi</td>
                        <td>: <strong>{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d F Y') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Waktu</td>
                        <td>: <strong>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WIB</strong></td>
                    </tr>
                    <tr>
                        <td>Durasi</td>
                        <td>: <strong>{{ $reservation->duration_hours }} jam</strong></td>
                    </tr>
                    @if($reservation->notes)
                    <tr>
                        <td>Catatan</td>
                        <td>: {{ $reservation->notes }}</td>
                    </tr>
                    @endif
                </table>

                <hr>

                <!-- Payment Summary -->
                <h6><i class="fas fa-money-bill-wave"></i> Ringkasan Pembayaran</h6>
                <table class="table table-sm table-borderless mb-3">
                    <tr>
                        <td width="200">Harga per Jam</td>
                        <td>: {{ $reservation->gameRoom->formatted_price }}</td>
                    </tr>
                    <tr>
                        <td>Durasi</td>
                        <td>: {{ $reservation->duration_hours }} jam</td>
                    </tr>
                    <tr class="border-top">
                        <td><strong>Total Harga</strong></td>
                        <td>: <strong class="text-primary fs-5">{{ $reservation->formatted_total_price }}</strong></td>
                    </tr>
                </table>

                <hr>

                <!-- Timestamps -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Dibuat: {{ $reservation->created_at->format('d M Y, H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-end">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Update: {{ $reservation->updated_at->format('d M Y, H:i') }}
                        </small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        @if(auth()->user()->isCustomer())
                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <a href="{{ route('reservations.edit', $reservation->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit/Reschedule
                            </a>
                            @endif

                            @if($reservation->status === 'pending')
                            <form action="{{ route('reservations.cancel', $reservation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan reservasi ini?')">
                                @csrf
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-times"></i> Batalkan Reservasi
                                </button>
                            </form>
                            @endif

                            @if(in_array($reservation->status, ['pending', 'cancelled']))
                            <form action="{{ route('reservations.destroy', $reservation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus reservasi ini? Data tidak dapat dikembalikan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-dark">
                                    <i class="fas fa-trash"></i> Hapus Reservasi
                                </button>
                            </form>
                            @endif
                        @endif

                        @if(!auth()->user()->isCustomer())
                            @if($reservation->status === 'pending')
                            <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="btn btn-success" onclick="return confirm('Konfirmasi reservasi ini?')">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                            </form>
                            <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="cancelled">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Batalkan reservasi ini?')">
                                    <i class="fas fa-times"></i> Batalkan
                                </button>
                            </form>
                            @elseif($reservation->status === 'confirmed')
                            <form action="{{ route('reservations.update-status', $reservation->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="btn btn-primary" onclick="return confirm('Tandai selesai?')">
                                    <i class="fas fa-check-double"></i> Tandai Selesai
                                </button>
                            </form>
                            @endif
                        @endif
                    </div>

                    <div>
                        <a href="{{ route('receipts.show', $reservation->id) }}" class="btn btn-info">
                            <i class="fas fa-receipt"></i> Lihat Struk
                        </a>
                        <a href="{{ route('receipts.download', $reservation->id) }}" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection