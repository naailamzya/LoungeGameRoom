@extends('layouts.app')

@section('title', 'Kelola Ruangan Game')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-door-open"></i> Kelola Ruangan Game</h5>
                <a href="{{ route('game-rooms.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Tambah Ruangan
                </a>
            </div>
            <div class="card-body">
                @if($gameRooms->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="60">#</th>
                                <th>Gambar</th>
                                <th>Nama Ruangan</th>
                                <th>Deskripsi</th>
                                <th>Kapasitas</th>
                                <th>Harga/Jam</th>
                                <th>Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gameRooms as $index => $room)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($room->image)
                                        <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="fas fa-gamepad text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td><strong>{{ $room->name }}</strong></td>
                                <td>{{ Str::limit($room->description, 80) }}</td>
                                <td>{{ $room->capacity }} orang</td>
                                <td>{{ $room->formatted_price }}</td>
                                <td>
                                    @if($room->status === 'available')
                                    <span class="badge bg-success">Tersedia</span>
                                    @else
                                    <span class="badge bg-danger">Maintenance</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('game-rooms.show', $room->id) }}" class="btn btn-sm btn-info" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('game-rooms.edit', $room->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('game-rooms.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ruangan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                <i class="fas fa-trash"></i>
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
                <p class="text-center mb-0">Belum ada ruangan game. <a href="{{ route('game-rooms.create') }}">Tambah sekarang</a></p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection