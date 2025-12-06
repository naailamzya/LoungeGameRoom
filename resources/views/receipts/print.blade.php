<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Receipt - {{ $reservation->booking_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }
        
        .header h2 {
            font-size: 18px;
            margin-bottom: 5px;
        }
        
        .header p {
            font-size: 10px;
            margin: 2px 0;
        }
        
        .section {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        
        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 11px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }
        
        .info-label {
            width: 40%;
        }
        
        .info-value {
            width: 60%;
            text-align: right;
        }
        
        .total-section {
            border-top: 2px solid #000;
            border-bottom: 2px dashed #000;
            padding: 10px 0;
            margin: 15px 0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 14px;
        }
        
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .badge-pending { background: #ffc107; color: #000; }
        .badge-confirmed { background: #17a2b8; color: #fff; }
        .badge-completed { background: #28a745; color: #fff; }
        .badge-cancelled { background: #dc3545; color: #fff; }
        
        @media print {
            body {
                padding: 10px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>🎮 LOUNGE GAME ROOM</h2>
        <p>Jl. Perintis Kemerdekaan KM.10, Makassar</p>
        <p>Telp: (0411) 123456 | Email: info@loungegameroom.com</p>
    </div>

    <div class="section">
        <div class="info-row">
            <span class="info-label">Kode Booking:</span>
            <span class="info-value"><strong>{{ $reservation->booking_code }}</strong></span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal Cetak:</span>
            <span class="info-value">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Status:</span>
            <span class="info-value">
                <span class="badge badge-{{ $reservation->status_badge }}">{{ $reservation->status_label }}</span>
            </span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">INFORMASI CUSTOMER</div>
        <div class="info-row">
            <span class="info-label">Nama:</span>
            <span class="info-value">{{ $reservation->user->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $reservation->user->email }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Telepon:</span>
            <span class="info-value">{{ $reservation->user->phone }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DETAIL RESERVASI</div>
        <div class="info-row">
            <span class="info-label">Ruangan:</span>
            <span class="info-value">{{ $reservation->gameRoom->name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Tanggal:</span>
            <span class="info-value">{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d F Y') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Waktu:</span>
            <span class="info-value">{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Durasi:</span>
            <span class="info-value">{{ $reservation->duration_hours }} jam</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">RINCIAN PEMBAYARAN</div>
        <div class="info-row">
            <span class="info-label">Harga per Jam:</span>
            <span class="info-value">{{ $reservation->gameRoom->formatted_price }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Durasi:</span>
            <span class="info-value">{{ $reservation->duration_hours }} jam</span>
        </div>
    </div>

    <div class="total-section">
        <div class="total-row">
            <span>TOTAL PEMBAYARAN:</span>
            <span>{{ $reservation->formatted_total_price }}</span>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih atas kunjungan Anda!</p>
        <p>Simpan struk ini sebagai bukti pembayaran</p>
        <p style="margin-top: 10px;">Printed: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Receipt</button>
        <button onclick="window.close()" style="padding: 10px 20px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            // Uncomment to enable auto-print
            // window.print();
        }
    </script>
</body>
</html>