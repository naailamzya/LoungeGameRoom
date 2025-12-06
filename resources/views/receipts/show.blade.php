@extends('layouts.app')

@section('title', 'Struk Pembayaran')

@section('content')
<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header text-center">
                <h5 class="mb-0"><i class="fas fa-receipt"></i> STRUK PEMBAYARAN</h5>
            </div>
            <div class="card-body" id="receiptContent">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h3 class="mb-0"><i class="fas fa-gamepad"></i> Lounge Game Room</h3>
                    <p class="mb-0"><small>Jl. Perintis Kemerdekaan KM.10, Makassar</small></p>
                    <p class="mb-0"><small>Telp: (0411) 123456 | Email: info@loungegameroom.com</small></p>
                </div>

                <hr style="border-top: 2px dashed #000;">

                <!-- Booking Info -->
                <div class="mb-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Kode Booking</td>
                            <td>: <strong>{{ $reservation->booking_code }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal Cetak</td>
                            <td>: {{ now()->format('d F Y, H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: <span class="badge bg-{{ $reservation->status_badge }}">{{ $reservation->status_label }}</span></td>
                        </tr>
                    </table>
                </div>

                <hr>

                <!-- Customer Info -->
                <div class="mb-3">
                    <strong>INFORMASI CUSTOMER</strong>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Nama</td>
                            <td>: {{ $reservation->user->name }}</td>
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

                <hr>

                <!-- Reservation Details -->
                <div class="mb-3">
                    <strong>DETAIL RESERVASI</strong>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Ruangan</td>
                            <td>: {{ $reservation->gameRoom->name }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>: {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WIB</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td>: {{ $reservation->duration_hours }} jam</td>
                        </tr>
                    </table>
                </div>

                <hr>

                <!-- Payment Details -->
                <div class="mb-3">
                    <strong>RINCIAN PEMBAYARAN</strong>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="150">Harga per Jam</td>
                            <td class="text-end">{{ $reservation->gameRoom->formatted_price }}</td>
                        </tr>
                        <tr>
                            <td>Durasi</td>
                            <td class="text-end">{{ $reservation->duration_hours }} jam</td>
                        </tr>
                        <tr style="border-top: 2px solid #000;">
                            <td><strong>TOTAL</strong></td>
                            <td class="text-end"><strong class="fs-5">{{ $reservation->formatted_total_price }}</strong></td>
                        </tr>
                    </table>
                </div>

                <hr style="border-top: 2px dashed #000;">

                <!-- Footer -->
                <div class="text-center">
                    <p class="mb-1"><small>Terima kasih atas kunjungan Anda!</small></p>
                    <p class="mb-1"><small>Simpan struk ini sebagai bukti pembayaran</small></p>
                    <p class="mb-0"><small><i>Printed on {{ now()->format('d/m/Y H:i:s') }}</i></small></p>
                </div>
            </div>
            <div class="card-footer text-center">
                <button onclick="window.print()" class="btn btn-primary me-2">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('receipts.download', $reservation->id) }}" class="btn btn-success me-2">
                    <i class="fas fa-download"></i> Download PDF
                </a>
                <a href="{{ route('reservations.show', $reservation->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #receiptContent, #receiptContent * {
            visibility: visible;
        }
        #receiptContent {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .card-footer {
            display: none;
        }
    }
</style>
@endpush
@endsection