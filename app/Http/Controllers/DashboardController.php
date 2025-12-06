<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show dashboard based on user role
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isCustomer()) {
            return $this->customerDashboard();
        } elseif ($user->isReceptionist()) {
            return $this->receptionistDashboard();
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        }

        return redirect()->route('login');
    }

    /**
     * Customer Dashboard
     */
    private function customerDashboard()
    {
        $gameRooms = GameRoom::where('status', 'available')->get();
        $myReservations = Reservation::where('user_id', Auth::id())
            ->with('gameRoom')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.customer', compact('gameRooms', 'myReservations'));
    }

    /**
     * Receptionist Dashboard
     */
    private function receptionistDashboard()
    {
        $pendingReservations = Reservation::where('status', 'pending')
            ->with(['user', 'gameRoom'])
            ->orderBy('created_at', 'desc')
            ->get();

        $todayReservations = Reservation::whereDate('reservation_date', today())
            ->with(['user', 'gameRoom'])
            ->orderBy('start_time')
            ->get();

        return view('dashboard.receptionist', compact('pendingReservations', 'todayReservations'));
    }

    /**
     * Manager Dashboard
     */
    private function managerDashboard()
    {
        $totalRooms = GameRoom::count();
        $totalReservations = Reservation::count();
        $totalRevenue = Reservation::whereIn('status', ['confirmed', 'completed'])->sum('total_price');
        $totalCustomers = User::where('role', 'customer')->count();

        $recentReservations = Reservation::with(['user', 'gameRoom'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $gameRooms = GameRoom::withCount('reservations')->get();

        return view('dashboard.manager', compact(
            'totalRooms',
            'totalReservations',
            'totalRevenue',
            'totalCustomers',
            'recentReservations',
            'gameRooms'
        ));
    }
}