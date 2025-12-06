@extends('layouts.app')

@section('title', 'Buat Reservasi')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar-plus"></i> Buat Reservasi Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('reservations.store') }}" method="POST" id="reservationForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="game_room_id" class="form-label">Pilih Ruangan Game <span class="text-danger">*</span></label>
                        <select class="form-select @error('game_room_id') is-invalid @enderror" id="game_room_id" name="game_room_id" required>
                            <option value="">-- Pilih Ruangan --</option>
                            @foreach($gameRooms as $room)
                            <option value="{{ $room->id }}" 
                                data-price="{{ $room->price_per_hour }}"
                                {{ old('game_room_id', $gameRoom?->id) == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} - {{ $room->formatted_price }}/jam (Kapasitas: {{ $room->capacity }} orang)
                            </option>
                            @endforeach
                        </select>
                        @error('game_room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reservation_date" class="form-label">Tanggal Reservasi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" id="reservation_date" name="reservation_date" value="{{ old('reservation_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        @error('reservation_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_time" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="time" class="form-control @error('start_time') is-invalid @enderror" id="start_time" name="start_time" value="{{ old('start_time', '09:00') }}" required>
                                @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duration_hours" class="form-label">Durasi (Jam) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" id="duration_hours" name="duration_hours" value="{{ old('duration_hours', 2) }}" min="1" max="12" required>
                                @error('duration_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Maksimal 12 jam</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Tambahkan catatan atau permintaan khusus...">{{ old('notes') }}</textarea>
                        @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="alert alert-info" id="priceEstimate" style="display: none;">
                        <h6 class="mb-2"><i class="fas fa-calculator"></i> Estimasi Biaya:</h6>
                        <p class="mb-1">Harga per Jam: <strong id="pricePerHour">-</strong></p>
                        <p class="mb-1">Durasi: <strong id="estimateDuration">-</strong> jam</p>
                        <hr>
                        <h5 class="mb-0">Total: <strong id="totalPrice" class="text-primary">-</strong></h5>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Buat Reservasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Calculate price estimate
    function calculatePrice() {
        const gameRoomSelect = document.getElementById('game_room_id');
        const durationInput = document.getElementById('duration_hours');
        const priceEstimate = document.getElementById('priceEstimate');
        
        if (gameRoomSelect.value && durationInput.value) {
            const selectedOption = gameRoomSelect.options[gameRoomSelect.selectedIndex];
            const pricePerHour = parseFloat(selectedOption.dataset.price);
            const duration = parseInt(durationInput.value);
            const totalPrice = pricePerHour * duration;
            
            document.getElementById('pricePerHour').textContent = 'Rp ' + pricePerHour.toLocaleString('id-ID');
            document.getElementById('estimateDuration').textContent = duration;
            document.getElementById('totalPrice').textContent = 'Rp ' + totalPrice.toLocaleString('id-ID');
            
            priceEstimate.style.display = 'block';
        } else {
            priceEstimate.style.display = 'none';
        }
    }
    
    document.getElementById('game_room_id').addEventListener('change', calculatePrice);
    document.getElementById('duration_hours').addEventListener('input', calculatePrice);
    
    // Initial calculation if values are pre-filled
    document.addEventListener('DOMContentLoaded', calculatePrice);
</script>
@endpush
@endsection