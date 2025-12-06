<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameRoomController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

// Home route
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected routes
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reservations
    Route::get('/reservations', [ReservationController::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [ReservationController::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
    Route::get('/reservations/{reservation}', [ReservationController::class, 'show'])->name('reservations.show');
    Route::get('/reservations/{reservation}/edit', [ReservationController::class, 'edit'])->name('reservations.edit');
    Route::put('/reservations/{reservation}', [ReservationController::class, 'update'])->name('reservations.update');
    Route::delete('/reservations/{reservation}', [ReservationController::class, 'destroy'])->name('reservations.destroy');
    Route::post('/reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

    // Payments
    Route::get('/payments/{reservation}/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments/{reservation}', [PaymentController::class, 'store'])->name('payments.store');

    // Receipts
    Route::get('/receipts/{reservation}', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{reservation}/download', [ReceiptController::class, 'download'])->name('receipts.download');
    Route::get('/receipts/{reservation}/print', [ReceiptController::class, 'print'])->name('receipts.print');

    // Receptionist & Manager only routes
    Route::middleware('role:receptionist,manager')->group(function () {
        Route::post('/reservations/{reservation}/update-status', [ReservationController::class, 'updateStatus'])
            ->name('reservations.update-status');
    });

    // Manager only routes
    Route::middleware('role:manager')->group(function () {
        Route::resource('game-rooms', GameRoomController::class);
    });
});