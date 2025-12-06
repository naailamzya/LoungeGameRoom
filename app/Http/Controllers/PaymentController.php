<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Show payment form
     */
    public function create(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if already paid
        if ($reservation->payment_status === 'paid') {
            return redirect()->route('reservations.show', $reservation->id)
                ->with('info', 'Reservasi ini sudah dibayar.');
        }

        return view('payments.create', compact('reservation'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:cash,transfer,ewallet',
        ], [
            'payment_method.required' => 'Pilih metode pembayaran',
            'payment_method.in' => 'Metode pembayaran tidak valid',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'payment_code' => Payment::generatePaymentCode(),
                'payment_method' => $request->payment_method,
                'amount' => $reservation->total_price,
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            // Update reservation payment status
            $reservation->update([
                'payment_status' => 'paid',
            ]);

            // Reload relationship
            $reservation->load('payment');

            // Send WhatsApp notification with receipt link
            $this->sendPaymentSuccessNotification($reservation);

            DB::commit();

            return redirect()->route('reservations.show', $reservation->id)
                ->with('payment_success', 'Pembayaran berhasil! Notifikasi telah dikirim ke WhatsApp Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Send WhatsApp notification when payment is successful
     */
    private function sendPaymentSuccessNotification($reservation)
    {
        try {
            $user = $reservation->user;
            $gameRoom = $reservation->gameRoom;
            $payment = $reservation->payment;
            
            // Generate receipt link (FULL URL)
            $receiptUrl = url('/receipts/' . $reservation->id);
            $downloadUrl = url('/receipts/' . $reservation->id . '/download');
            
            $message = "✅ *PEMBAYARAN BERHASIL!*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "Halo *{$user->name}*,\n\n";
            $message .= "Pembayaran reservasi Anda telah berhasil diproses! 🎉\n\n";
            
            $message .= "📋 *DETAIL RESERVASI*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🎫 Kode Booking: *{$reservation->booking_code}*\n";
            $message .= "🏢 Ruangan: *{$gameRoom->name}*\n";
            $message .= "👥 Kapasitas: *{$gameRoom->capacity} orang*\n";
            $message .= "📅 Tanggal: *" . \Carbon\Carbon::parse($reservation->reservation_date)->isoFormat('D MMMM YYYY') . "*\n";
            $message .= "⏰ Waktu: *" . substr($reservation->start_time, 0, 5) . " - " . substr($reservation->end_time, 0, 5) . " WITA*\n";
            $message .= "⏱️ Durasi: *{$reservation->duration_hours} jam*\n\n";
            
            $message .= "💳 *DETAIL PEMBAYARAN*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "💰 Total: *{$reservation->formatted_total_price}*\n";
            $message .= "💵 Metode: *{$payment->payment_method_label}*\n";
            $message .= "📝 Kode: *{$payment->payment_code}*\n";
            $message .= "⏰ Waktu: *" . $payment->paid_at->isoFormat('D MMM YYYY, HH:mm') . " WITA*\n";
            $message .= "✅ Status: *LUNAS*\n\n";
            
            $message .= "📄 *STRUK PEMBAYARAN*\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🔗 Lihat Struk Online:\n";
            $message .= "{$receiptUrl}\n\n";
            $message .= "📥 Download PDF:\n";
            $message .= "{$downloadUrl}\n\n";
            
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📍 *INFORMASI CHECK-IN*\n";
            $message .= "Alamat: Jl. Perintis Kemerdekaan KM.10\n";
            $message .= "Makassar, Sulawesi Selatan\n";
            $message .= "Telp: (0411) 123456\n\n";
            
            $message .= "⚠️ *PENTING:*\n";
            $message .= "• Simpan kode booking Anda\n";
            $message .= "• Datang 10 menit sebelum waktu reservasi\n";
            $message .= "• Tunjukkan struk ini saat check-in\n";
            $message .= "• Bawa identitas diri (KTP/SIM)\n\n";
            
            $message .= "━━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "Terima kasih! 🙏\n\n";
            $message .= "_LOUNGE GAME ROOM - Your Gaming Paradise_";

            // Format phone number (remove leading 0, add 62)
            $phone = $user->phone;
            if (substr($phone, 0, 1) === '0') {
                $phone = '62' . substr($phone, 1);
            }

            // Get Fonnte credentials from .env
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

                // Log response for debugging
                \Log::info('Payment WhatsApp Notification');
                \Log::info('Phone: ' . $phone);
                \Log::info('Booking Code: ' . $reservation->booking_code);
                \Log::info('Payment Code: ' . $payment->payment_code);
                \Log::info('Response Status: ' . $response->status());
                \Log::info('Response Body: ' . $response->body());

                if ($response->successful()) {
                    \Log::info('✅ WhatsApp notification sent successfully');
                } else {
                    \Log::error('❌ WhatsApp notification failed');
                    \Log::error('Error: ' . $response->body());
                }
            } else {
                \Log::warning('⚠️ WhatsApp credentials not configured in .env');
                \Log::warning('WHATSAPP_TOKEN: ' . ($token ? 'SET' : 'NOT SET'));
                \Log::warning('WHATSAPP_API_URL: ' . ($apiUrl ? 'SET' : 'NOT SET'));
            }
        } catch (\Exception $e) {
            // Log error but don't stop the payment process
            \Log::error('❌ WhatsApp payment notification exception');
            \Log::error('Error Message: ' . $e->getMessage());
            \Log::error('Stack Trace: ' . $e->getTraceAsString());
        }
    }
}