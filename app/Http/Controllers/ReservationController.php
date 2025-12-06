<?php

namespace App\Http\Controllers;

use App\Models\GameRoom;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    /**
     * Display a listing of reservations
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isCustomer()) {
            $reservations = Reservation::where('user_id', $user->id)
                ->with('gameRoom')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $reservations = Reservation::with(['user', 'gameRoom'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('reservations.index', compact('reservations'));
    }

    /**
     * Show the form for creating a new reservation
     */
    public function create(Request $request)
    {
        $gameRoomId = $request->get('game_room_id');
        $gameRoom = null;

        if ($gameRoomId) {
            $gameRoom = GameRoom::findOrFail($gameRoomId);
        }

        $gameRooms = GameRoom::where('status', 'available')->get();
        
        return view('reservations.create', compact('gameRooms', 'gameRoom'));
    }
    
    /**
     * Store a newly created reservation
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'game_room_id' => 'required|exists:game_rooms,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string',
        ], [
            'game_room_id.required' => 'Pilih ruangan game',
            'reservation_date.required' => 'Tanggal reservasi wajib diisi',
            'reservation_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'duration_hours.required' => 'Durasi wajib diisi',
            'duration_hours.min' => 'Durasi minimal 1 jam',
            'duration_hours.max' => 'Durasi maksimal 12 jam',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $gameRoom = GameRoom::findOrFail($request->game_room_id);

            // Check if room is available for the selected time
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
            $durationHours = (int) $request->duration_hours;
            $endTime = $startTime->copy()->addHours($durationHours);

            $existingReservation = Reservation::where('game_room_id', $request->game_room_id)
                ->where('reservation_date', $request->reservation_date)
                ->where(function ($query) use ($request, $startTime, $endTime) {
                    $query->where(function ($q) use ($request, $startTime, $endTime) {
                        $q->where('start_time', '<', $endTime->format('H:i:s'))
                          ->where('end_time', '>', $startTime->format('H:i:s'));
                    });
                })
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingReservation) {
                return back()->withErrors(['error' => 'Ruangan ini sudah dipesan pada waktu yang dipilih. Silakan pilih waktu lain.'])->withInput();
            }

            // Calculate total price
            $totalPrice = $gameRoom->price_per_hour * $durationHours;

            // Create reservation
            $reservation = Reservation::create([
                'booking_code' => Reservation::generateBookingCode(),
                'user_id' => Auth::id(),
                'game_room_id' => $request->game_room_id,
                'reservation_date' => $request->reservation_date,
                'start_time' => $request->start_time,
                'end_time' => $endTime->format('H:i:s'),
                'duration_hours' => $durationHours,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'notes' => $request->notes,
            ]);

            // Send WhatsApp notification
            $this->sendWhatsAppNotification($reservation);

            // Redirect to payment page
            return redirect()->route('payments.create', $reservation->id)
                ->with('success', 'Reservasi berhasil dibuat! Silakan lakukan pembayaran.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified reservation
     */
    public function show(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing reservation
     */
    public function edit(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Only allow edit for pending and confirmed status
        if (!in_array($reservation->status, ['pending', 'confirmed'])) {
            return redirect()->route('reservations.show', $reservation->id)
                ->withErrors(['error' => 'Hanya reservasi dengan status Pending atau Confirmed yang bisa diedit']);
        }

        $gameRooms = GameRoom::where('status', 'available')->get();
        
        return view('reservations.edit', compact('reservation', 'gameRooms'));
    }

    /**
     * Update the specified reservation
     */
    public function update(Request $request, Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'game_room_id' => 'required|exists:game_rooms,id',
            'reservation_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'duration_hours' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string',
        ], [
            'game_room_id.required' => 'Pilih ruangan game',
            'reservation_date.required' => 'Tanggal reservasi wajib diisi',
            'reservation_date.after_or_equal' => 'Tanggal tidak boleh sebelum hari ini',
            'start_time.required' => 'Waktu mulai wajib diisi',
            'duration_hours.required' => 'Durasi wajib diisi',
            'duration_hours.min' => 'Durasi minimal 1 jam',
            'duration_hours.max' => 'Durasi maksimal 12 jam',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $gameRoom = GameRoom::findOrFail($request->game_room_id);

            // Check if room is available for the selected time
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $request->start_time);
            $durationHours = (int) $request->duration_hours;
            $endTime = $startTime->copy()->addHours($durationHours);

            $existingReservation = Reservation::where('game_room_id', $request->game_room_id)
                ->where('reservation_date', $request->reservation_date)
                ->where(function ($query) use ($request, $startTime, $endTime) {
                    $query->where(function ($q) use ($request, $startTime, $endTime) {
                        $q->where('start_time', '<', $endTime->format('H:i:s'))
                          ->where('end_time', '>', $startTime->format('H:i:s'));
                    });
                })
                ->where('status', '!=', 'cancelled')
                ->where('id', '!=', $reservation->id) // Exclude current reservation
                ->first();

            if ($existingReservation) {
                return back()->withErrors(['error' => 'Ruangan ini sudah dipesan pada waktu yang dipilih. Silakan pilih waktu lain.'])->withInput();
            }

            // Calculate total price
            $totalPrice = $gameRoom->price_per_hour * $durationHours;

            // Update reservation
            $reservation->update([
                'game_room_id' => $request->game_room_id,
                'reservation_date' => $request->reservation_date,
                'start_time' => $request->start_time,
                'end_time' => $endTime->format('H:i:s'),
                'duration_hours' => $durationHours,
                'total_price' => $totalPrice,
                'notes' => $request->notes,
            ]);

            return redirect()->route('reservations.show', $reservation->id)
                ->with('success', 'Reservasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified reservation
     */
    public function destroy(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        try {
            // Only allow delete for pending and cancelled status
            if (!in_array($reservation->status, ['pending', 'cancelled'])) {
                return back()->withErrors(['error' => 'Hanya reservasi dengan status Pending atau Cancelled yang bisa dihapus']);
            }

            $reservation->delete();

            return redirect()->route('reservations.index')
                ->with('success', 'Reservasi berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel reservation
     */
    public function cancel(Reservation $reservation)
    {
        try {
            if ($reservation->status === 'completed') {
                return back()->withErrors(['error' => 'Tidak dapat membatalkan reservasi yang sudah selesai']);
            }

            $reservation->update(['status' => 'cancelled']);

            return back()->with('success', 'Reservasi berhasil dibatalkan!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update reservation status
     */
    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        try {
            $reservation->update(['status' => $request->status]);

            // Send notification for status change
            if ($request->status === 'confirmed') {
                $this->sendConfirmationNotification($reservation);
            }

            return back()->with('success', 'Status reservasi berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Send WhatsApp notification when reservation created
     */
    private function sendWhatsAppNotification($reservation)
    {
        try {
            $user = $reservation->user;
            $gameRoom = $reservation->gameRoom;
            
            // Generate payment link (FULL URL)
            $paymentUrl = url('/payments/' . $reservation->id . '/create');
            
            $message = "🎮 *LOUNGE GAME ROOM*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "Halo *{$user->name}*,\n\n";
            $message .= "Reservasi Anda telah berhasil dibuat! ✅\n\n";
            
            $message .= "📋 *DETAIL RESERVASI*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🎫 Kode Booking: *{$reservation->booking_code}*\n";
            $message .= "🏢 Ruangan: *{$gameRoom->name}*\n";
            $message .= "👥 Kapasitas: *{$gameRoom->capacity} orang*\n";
            $message .= "📅 Tanggal: *" . \Carbon\Carbon::parse($reservation->reservation_date)->isoFormat('D MMMM YYYY') . "*\n";
            $message .= "⏰ Waktu: *" . substr($reservation->start_time, 0, 5) . " - " . substr($reservation->end_time, 0, 5) . " WITA*\n";
            $message .= "⏱️ Durasi: *{$reservation->duration_hours} jam*\n";
            $message .= "💰 Total: *{$reservation->formatted_total_price}*\n";
            $message .= "📊 Status: *{$reservation->status_label}*\n\n";
            
            $message .= "⚠️ *SEGERA LAKUKAN PEMBAYARAN!*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🔗 Link Pembayaran:\n";
            $message .= "{$paymentUrl}\n\n";
            $message .= "💳 Metode Pembayaran:\n";
            $message .= "• Tunai (di kasir)\n";
            $message .= "• Transfer Bank\n";
            $message .= "• E-Wallet (QRIS/GoPay/OVO/Dana)\n\n";
            
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "Terima kasih telah memesan! 🙏\n\n";
            $message .= "_Simpan pesan ini sebagai bukti reservasi_";

            // Format phone number
            $phone = $user->phone;
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }

            // Send via Fonnte API
            $token = env('WHATSAPP_TOKEN');
            $apiUrl = env('WHATSAPP_API_URL');
            
            if ($token && $apiUrl) {
                $response = Http::withHeaders([
                    'Authorization' => $token
                ])->post($apiUrl, [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                \Log::info('Reservation WhatsApp Notification');
                \Log::info('Phone: ' . $phone);
                \Log::info('Booking Code: ' . $reservation->booking_code);
                \Log::info('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp notification failed: ' . $e->getMessage());
        }
    }

    /**
     * Send confirmation notification
     */
    private function sendConfirmationNotification($reservation)
    {
        try {
            $user = $reservation->user;
            $gameRoom = $reservation->gameRoom;
            
            // Generate receipt link if paid (FULL URL)
            $receiptUrl = url('/receipts/' . $reservation->id);
            
            $message = "✅ *RESERVASI DIKONFIRMASI*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "Halo *{$user->name}*,\n\n";
            $message .= "Reservasi Anda telah dikonfirmasi oleh resepsionis! 🎉\n\n";
            
            $message .= "📋 *DETAIL RESERVASI*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🎫 Kode Booking: *{$reservation->booking_code}*\n";
            $message .= "🏢 Ruangan: *{$gameRoom->name}*\n";
            $message .= "📅 Tanggal: *" . \Carbon\Carbon::parse($reservation->reservation_date)->isoFormat('D MMMM YYYY') . "*\n";
            $message .= "⏰ Waktu: *" . substr($reservation->start_time, 0, 5) . " - " . substr($reservation->end_time, 0, 5) . " WITA*\n";
            $message .= "⏱️ Durasi: *{$reservation->duration_hours} jam*\n\n";
            
            if ($reservation->payment_status === 'paid') {
                $message .= "💳 Status Pembayaran: *LUNAS* ✅\n\n";
                $message .= "📄 *STRUK PEMBAYARAN*\n";
                $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
                $message .= "🔗 Lihat Struk:\n";
                $message .= "{$receiptUrl}\n\n";
            }
            
            $message .= "📍 *INFORMASI CHECK-IN*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "Alamat: Jl. Perintis Kemerdekaan KM.10\n";
            $message .= "Makassar, Sulawesi Selatan\n";
            $message .= "Telp: (0411) 123456\n\n";
            
            $message .= "⚠️ *CATATAN PENTING:*\n";
            $message .= "• Datang 10 menit sebelum waktu reservasi\n";
            $message .= "• Tunjukkan kode booking saat check-in\n";
            $message .= "• Bawa identitas diri (KTP/SIM)\n\n";
            
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "Sampai jumpa! 🎮\n\n";
            $message .= "_Tunjukkan pesan ini saat check-in_";

            $phone = $user->phone;
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }

            $token = env('WHATSAPP_TOKEN');
            $apiUrl = env('WHATSAPP_API_URL');
            
            if ($token && $apiUrl) {
                Http::withHeaders([
                    'Authorization' => $token
                ])->post($apiUrl, [
                    'target' => $phone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);
                
                \Log::info('Confirmation WhatsApp sent to: ' . $phone);
            }
        } catch (\Exception $e) {
            \Log::error('WhatsApp confirmation failed: ' . $e->getMessage());
        }
    }
}