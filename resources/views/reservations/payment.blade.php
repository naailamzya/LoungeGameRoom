@extends('layouts.app')

@section('title', 'Pembayaran Reservasi')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-credit-card"></i> Pembayaran Reservasi</h5>
            </div>
            <div class="card-body">
                <!-- Booking Information -->
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Informasi Reservasi</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <small>
                                <strong>Kode Booking:</strong> {{ $reservation->booking_code }}<br>
                                <strong>Ruangan:</strong> {{ $reservation->gameRoom->name }}<br>
                                <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d F Y') }}
                            </small>
                        </div>
                        <div class="col-md-6">
                            <small>
                                <strong>Waktu:</strong> {{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WIB<br>
                                <strong>Durasi:</strong> {{ $reservation->duration_hours }} jam<br>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="mb-3"><i class="fas fa-receipt"></i> Rincian Pembayaran</h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td>{{ $reservation->gameRoom->name }}</td>
                                <td class="text-end">{{ $reservation->gameRoom->formatted_price }} x {{ $reservation->duration_hours }} jam</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Total Pembayaran</strong></td>
                                <td class="text-end"><h4 class="text-primary mb-0">{{ $reservation->formatted_total_price }}</h4></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('reservations.process-payment', $reservation->id) }}" method="POST">
                    @csrf
                    
                    <h6 class="mb-3"><i class="fas fa-money-bill-wave"></i> Pilih Metode Pembayaran</h6>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card payment-card" onclick="selectPayment('cash')">
                                <div class="card-body text-center">
                                    <input type="radio" name="payment_method" id="cash" value="cash" class="form-check-input d-none" required>
                                    <i class="fas fa-money-bill-wave fa-3x text-success mb-3"></i>
                                    <h6>Tunai</h6>
                                    <small class="text-muted">Bayar langsung di kasir</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card payment-card" onclick="selectPayment('transfer')">
                                <div class="card-body text-center">
                                    <input type="radio" name="payment_method" id="transfer" value="transfer" class="form-check-input d-none" required>
                                    <i class="fas fa-university fa-3x text-primary mb-3"></i>
                                    <h6>Transfer Bank</h6>
                                    <small class="text-muted">Transfer ke rekening</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <div class="card payment-card" onclick="selectPayment('ewallet')">
                                <div class="card-body text-center">
                                    <input type="radio" name="payment_method" id="ewallet" value="ewallet" class="form-check-input d-none" required>
                                    <i class="fas fa-mobile-alt fa-3x text-info mb-3"></i>
                                    <h6>E-Wallet</h6>
                                    <small class="text-muted">QRIS/GoPay/OVO/Dana</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('payment_method')
                    <div class="alert alert-danger">{{ $message }}</div>
                    @enderror

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Perhatian:</strong> Pastikan Anda melakukan pembayaran sesuai dengan metode yang dipilih. Setelah pembayaran berhasil, silakan tunggu konfirmasi dari resepsionis.
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('reservations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-check"></i> Konfirmasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .payment-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
    }

    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .payment-card.selected {
        border-color: #0d6efd;
        background-color: #e7f1ff;
    }
</style>
@endpush

@push('scripts')
<script>
    function selectPayment(method) {
        // Remove selected class from all cards
        document.querySelectorAll('.payment-card').forEach(card => {
            card.classList.remove('selected');
        });

        // Add selected class to clicked card
        event.currentTarget.classList.add('selected');

        // Check the radio button
        document.getElementById(method).checked = true;
    }
</script>
@endpush
@endsection