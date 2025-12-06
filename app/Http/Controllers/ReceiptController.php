<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    /**
     * Show receipt page
     */
    public function show(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('receipts.show', compact('reservation'));
    }

    /**
     * Download receipt as PDF
     */
    public function download(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        try {
            $pdf = Pdf::loadView('receipts.pdf', compact('reservation'))
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif',
                    'dpi' => 96, 
                    'margin-top' => 10,
                    'margin-right' => 10,
                    'margin-bottom' => 10,
                    'margin-left' => 10,
                ]);
            
            return $pdf->download('struk-' . $reservation->booking_code . '.pdf');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengunduh struk: ' . $e->getMessage()]);
        }
    }

    /**
     * Print receipt
     */
    public function print(Reservation $reservation)
    {
        // Check authorization
        if (Auth::user()->isCustomer() && $reservation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('receipts.print', compact('reservation'));
    }
}