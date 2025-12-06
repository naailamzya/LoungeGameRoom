<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GameRoom;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo users
        User::create([
            'name' => 'Customer Demo',
            'email' => 'customer@test.com',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Receptionist Demo',
            'email' => 'receptionist@test.com',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
            'role' => 'receptionist',
        ]);

        User::create([
            'name' => 'Manager Demo',
            'email' => 'manager@test.com',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        // Create game rooms
        GameRoom::create([
            'name' => 'VIP Gaming Room',
            'description' => 'Ruangan gaming premium dengan fasilitas lengkap termasuk PC gaming high-end, kursi gaming, dan sound system berkualitas tinggi.',
            'capacity' => 8,
            'price_per_hour' => 150000,
            'status' => 'available',
        ]);

        GameRoom::create([
            'name' => 'Standard Gaming Room',
            'description' => 'Ruangan gaming standar dengan PC gaming yang nyaman untuk bermain game favorit Anda bersama teman.',
            'capacity' => 6,
            'price_per_hour' => 100000,
            'status' => 'available',
        ]);

        GameRoom::create([
            'name' => 'Party Gaming Room',
            'description' => 'Ruangan gaming besar cocok untuk party gaming, tournament, atau acara khusus dengan kapasitas hingga 12 orang.',
            'capacity' => 12,
            'price_per_hour' => 200000,
            'status' => 'available',
        ]);

        GameRoom::create([
            'name' => 'Console Gaming Room',
            'description' => 'Ruangan khusus console gaming dengan PS5, Xbox Series X, dan Nintendo Switch lengkap dengan layar besar.',
            'capacity' => 4,
            'price_per_hour' => 80000,
            'status' => 'available',
        ]);
    }
}