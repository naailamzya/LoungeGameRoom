@extends('layouts.app')

@section('title', 'Detail Ruangan Game')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-door-open"></i> Detail Ruangan Game</h5>
                <div>
                    <a href="{{ route('game-rooms.edit', $gameRoom->id) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('game-rooms.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-5">
                        @if($gameRoom->image)
                        <img src="{{ asset('storage/' . $gameRoom->image) }}" alt="{{ $gameRoom->name }}" class="img-fluid rounded">
                        @else
                        <div class="bg-secondary d-flex align-items-center justify-content-center rounded" style="height: 300px;">
                            <i class="fas fa-gamepad fa-5x text-white"></i>
                        </div>
                        @endif
                    </div>
                    <div class="col-md-7">
                        <h3>{{ $gameRoom->name }}</h3>
                        <hr>
                        
                        <div class="mb-3">
                            <strong><i class="fas fa-align-left"></i> Deskripsi:</strong>
                            <p class="mb-0 mt-2">{{ $gameRoom->description }}</p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="fas fa-users"></i> Kapasitas:</strong>
                            <p class="mb-0 mt-1">{{ $gameRoom->capacity }} orang</p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="fas fa-money-bill-wave"></i> Harga per Jam:</strong>
                            <p class="mb-0 mt-1"><span class="h4 text-primary">{{ $gameRoom->formatted_price }}</span></p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="fas fa-info-circle"></i> Status:</strong>
                            <p class="mb-0 mt-1">
                                @if($gameRoom->status === 'available')
                                <span class="badge bg-success fs-6">Tersedia</span>
                                @else
                                <span class="badge bg-danger fs-6">Maintenance</span>
                                @endif
                            </p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="fas fa-calendar"></i> Dibuat:</strong>
                            <p class="mb-0 mt-1">{{ $gameRoom->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        <div class="mb-3">
                            <strong><i class="fas fa-clock"></i> Terakhir Diupdate:</strong>
                            <p class="mb-0 mt-1">{{ $gameRoom->updated_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection