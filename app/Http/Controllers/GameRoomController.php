<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GameRoomController extends Controller
{
    /**
     * Display a listing of game rooms
     */
    public function index()
    {
        $gameRooms = GameRoom::orderBy('created_at', 'desc')->get();
        return view('game-rooms.index', compact('gameRooms'));
    }

    /**
     * Show the form for creating a new game room
     */
    public function create()
    {
        return view('game-rooms.create');
    }

    /**
     * Store a newly created game room
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:available,maintenance',
        ], [
            'name.required' => 'Nama ruangan wajib diisi',
            'description.required' => 'Deskripsi wajib diisi',
            'capacity.required' => 'Kapasitas wajib diisi',
            'capacity.min' => 'Kapasitas minimal 1 orang',
            'price_per_hour.required' => 'Harga per jam wajib diisi',
            'image.image' => 'File harus berupa gambar',
            'image.max' => 'Ukuran gambar maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('game-rooms', 'public');
                $data['image'] = $imagePath;
            }

            GameRoom::create($data);

            return redirect()->route('game-rooms.index')->with('success', 'Ruangan game berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified game room
     */
    public function show(GameRoom $gameRoom)
    {
        return view('game-rooms.show', compact('gameRoom'));
    }

    /**
     * Show the form for editing the specified game room
     */
    public function edit(GameRoom $gameRoom)
    {
        return view('game-rooms.edit', compact('gameRoom'));
    }

    /**
     * Update the specified game room
     */
    public function update(Request $request, GameRoom $gameRoom)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'price_per_hour' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:available,maintenance',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $data = $request->except('image');

            if ($request->hasFile('image')) {
                // Delete old image
                if ($gameRoom->image) {
                    Storage::disk('public')->delete($gameRoom->image);
                }
                $imagePath = $request->file('image')->store('game-rooms', 'public');
                $data['image'] = $imagePath;
            }

            $gameRoom->update($data);

            return redirect()->route('game-rooms.index')->with('success', 'Ruangan game berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified game room
     */
    public function destroy(GameRoom $gameRoom)
    {
        try {
            // Delete image if exists
            if ($gameRoom->image) {
                Storage::disk('public')->delete($gameRoom->image);
            }

            $gameRoom->delete();

            return redirect()->route('game-rooms.index')->with('success', 'Ruangan game berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}