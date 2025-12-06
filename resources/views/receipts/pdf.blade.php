<!DOCTYPE html>
<html>
<head>
    <title>Struk Pembayaran - {{ $reservation->booking_code }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 10mm; /* Margin lebih kecil untuk maksimalisasi ruang konten */
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            font-size: 8pt; /* Ukuran font utama lebih kecil */
            line-height: 1.3;
            color: #2c3e50;
            background: white;
        }

        .page {
            width: 100%;
            max-width: 210mm;
            margin: 0 auto;
            padding: 0; /* Padding di dalam page dihilangkan, hanya gunakan margin tabel dan elemen lain */
            background: white;
            /* Pastikan tidak ada elemen yang dipisahkan ke halaman baru jika bisa dihindari */
            page-break-inside: avoid;
        }

        /* Header */
        .header {
            text-align: center;
            padding: 6px 0;
            background: #2c3e50; /* Warna solid untuk header */
            color: white;
            margin-bottom: 8px;
            border-radius: 4px 4px 0 0;
        }

        .header h2 {
            font-size: 12pt;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }

        .header p {
            font-size: 6pt; /* Ukuran font kontak lebih kecil */
            margin: 1px 0;
        }

        /* Kode Booking */
        .booking-section {
            text-align: center;
            padding: 6px;
            background: #3498db; /* Warna biru untuk kode booking */
            color: white;
            margin: 6px 10px; /* Margin kiri dan kanan untuk ruang putih */
            border-radius: 4px;
            font-weight: bold;
            font-size: 11pt;
        }

        /* Status Badges */
        .status-container {
            text-align: center;
            margin: 6px 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 15px;
            font-size: 7pt; /* Ukuran font badge lebih kecil */
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 2px;
            color: white;
        }
        .badge-pending { background-color: #f39c12; }
        .badge-confirmed { background-color: #3498db; }
        .badge-completed { background-color: #27ae60; }
        .badge-cancelled { background-color: #e74c3c; }
        .badge-paid { background-color: #27ae60; }
        .badge-unpaid { background-color: #e74c3c; }

        /* Section */
        .section {
            margin: 8px 10px; /* Margin kiri dan kanan untuk ruang putih */
            page-break-inside: avoid; /* Cegah pemisahan bagian */
        }

        .section-title {
            font-size: 9pt;
            font-weight: bold;
            color: white;
            padding: 4px 8px;
            background: #34495e; /* Warna abu gelap untuk judul section */
            border-radius: 3px;
            margin-bottom: 4px;
            text-align: center;
            letter-spacing: 0.3px;
        }

        /* Tabel Info */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt; /* Ukuran font tabel lebih kecil lagi */
        }

        .info-table td {
            padding: 3px 5px; /* Padding tabel lebih kecil */
            vertical-align: top;
            border-bottom: 1px solid #ecf0f1;
        }

        .info-table td:first-child {
            width: 42%; /* Lebar kolom label */
            font-weight: 600;
            color: #7f8c8d;
        }

        .info-table td:last-child {
            width: 58%; /* Lebar kolom value */
            color: #2c3e50;
        }

        .info-table tr:last-child td {
            border-bottom: none;
        }

        /* Ringkasan Pembayaran */
        .payment-summary {
            background: #f39c12; /* Warna oranye untuk ringkasan */
            padding: 8px 10px; /* Padding lebih kecil */
            margin: 10px 10px; /* Margin kiri dan kanan */
            border-radius: 4px;
            border: 1px dashed #e67e22;
            page-break-inside: avoid;
        }

        .payment-summary .title {
            font-size: 9pt;
            font-weight: bold;
            color: white;
            text-align: center;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 4px; /* Margin antar baris lebih kecil */
            font-size: 7.5pt;
        }

        .payment-row .label {
            display: table-cell;
            width: 60%;
            color: white;
            font-weight: 500;
        }

        .payment-row .value {
            display: table-cell;
            width: 40%;
            text-align: right;
            color: white;
            font-weight: 600;
        }

        .payment-total {
            border-top: 2px solid white; /* Border atas total lebih tebal */
            padding-top: 6px;
            margin-top: 6px;
        }

        .payment-total .label {
            font-size: 9pt;
            font-weight: bold;
        }

        .payment-total .value {
            font-size: 12pt; /* Ukuran font total sedikit lebih besar */
            font-weight: bold;
            color: #d63031; /* Merah untuk total */
        }

        /* Footer */
        .footer {
            text-align: center;
            margin: 10px 10px 5px 10px; /* Margin bawah lebih kecil */
            padding: 8px 0;
            background: #34495e; /* Warna abu gelap untuk footer */
            color: white;
            border-radius: 0 0 4px 4px;
            font-size: 7pt; /* Ukuran font footer lebih kecil */
            page-break-inside: avoid;
        }

        .footer .main-text {
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 8pt;
        }

        .footer .sub-text {
            line-height: 1.3;
        }

        /* Highlight */
        .highlight {
            background: #fff3cd;
            padding: 1px 4px;
            border-radius: 3px;
            font-weight: bold;
            color: #856404;
        }

        /* Info Tambahan */
        .additional-info {
            font-size: 6pt; /* Ukuran font info tambahan paling kecil */
            color: #7f8c8d;
            text-align: center;
            margin: 5px 10px 10px 10px; /* Margin atas dan bawah lebih kecil */
            padding-top: 5px;
            border-top: 1px dashed #bdc3c7;
        }
    </style>
</head>
<body>
    <div class="page">
        <!-- Header -->
        <div class="header">
            <h2>LOUNGE GAME ROOM</h2>
            <p>Your Gaming Paradise</p>
            <p>Jl. Perintis Kemerdekaan KM.10, Makassar, Sulawesi Selatan</p>
            <p>Telepon: (0411) 123456 | Email: info@loungegameroom.com</p>
        </div>

        <!-- Booking Code -->
        <div class="booking-section">
            {{ $reservation->booking_code }}
        </div>

        <!-- Status Badges -->
        <div class="status-container">
            <span class="status-badge badge-{{ $reservation->status }}">
                {{ $reservation->status_label }}
            </span>
            <span class="status-badge badge-{{ $reservation->payment_status }}">
                {{ $reservation->payment_status_label }}
            </span>
        </div>

        <!-- Customer Info -->
        <div class="section">
            <div class="section-title">Informasi Customer</div>
            <table class="info-table">
                <tr>
                    <td>Nama</td>
                    <td>{{ $reservation->user->name }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $reservation->user->email }}</td>
                </tr>
                <tr>
                    <td>Telepon</td>
                    <td>{{ $reservation->user->phone }}</td>
                </tr>
            </table>
        </div>

        <!-- Reservation Details -->
        <div class="section">
            <div class="section-title">Detail Reservasi</div>
            <table class="info-table">
                <tr>
                    <td>Ruangan</td>
                    <td>{{ $reservation->gameRoom->name }}</td>
                </tr>
                <tr>
                    <td>Kapasitas</td>
                    <td>{{ $reservation->gameRoom->capacity }} orang</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ \Carbon\Carbon::parse($reservation->reservation_date)->isoFormat('dddd, D MMMM YYYY') }}</td>
                </tr>
                <tr>
                    <td>Waktu</td>
                    <td>{{ substr($reservation->start_time, 0, 5) }} - {{ substr($reservation->end_time, 0, 5) }} WITA</td>
                </tr>
                <tr>
                    <td>Durasi</td>
                    <td>{{ $reservation->duration_hours }} jam</td>
                </tr>
                @if($reservation->notes)
                <tr>
                    <td>Catatan</td>
                    <td>{{ $reservation->notes }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Payment Info -->
        @if($reservation->payment)
        <div class="section">
            <div class="section-title">Informasi Pembayaran</div>
            <table class="info-table">
                <tr>
                    <td>Kode Pembayaran</td>
                    <td>{{ $reservation->payment->payment_code }}</td>
                </tr>
                <tr>
                    <td>Metode Pembayaran</td>
                    <td><span class="highlight">{{ $reservation->payment->payment_method_label }}</span></td>
                </tr>
                <tr>
                    <td>Status Pembayaran</td>
                    <td><strong style="color: #27ae60;">{{ $reservation->payment->status_label }}</strong></td>
                </tr>
                @if($reservation->payment->paid_at)
                <tr>
                    <td>Tanggal Pembayaran</td>
                    <td>{{ $reservation->payment->paid_at->isoFormat('dddd, D MMMM YYYY, HH:mm') }} WITA</td>
                </tr>
                @endif
            </table>
        </div>
        @endif

        <!-- Payment Summary -->
        <div class="payment-summary">
            <div class="title">Rincian Pembayaran</div>
            <div class="payment-row">
                <div class="label">Harga per Jam</div>
                <div class="value">{{ $reservation->gameRoom->formatted_price }}</div>
            </div>
            <div class="payment-row">
                <div class="label">Durasi</div>
                <div class="value">{{ $reservation->duration_hours }} jam</div>
            </div>
            <div class="payment-row payment-total">
                <div class="label">TOTAL PEMBAYARAN</div>
                <div class="value">{{ $reservation->formatted_total_price }}</div>
            </div>
        </div>

        <!-- Additional Info -->
        <div class="section">
            <div class="section-title">Informasi Tambahan</div>
            <table class="info-table">
                <tr>
                    <td>Tanggal Dibuat</td>
                    <td>{{ $reservation->created_at->isoFormat('D MMMM YYYY, HH:mm') }} WITA</td>
                </tr>
                <tr>
                    <td>Terakhir Diupdate</td>
                    <td>{{ $reservation->updated_at->isoFormat('D MMMM YYYY, HH:mm') }} WITA</td>
                </tr>
                <tr>
                    <td>Tanggal Cetak</td>
                    <td>{{ now()->isoFormat('D MMMM YYYY, HH:mm') }} WITA</td>
                </tr>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="main-text">Terima kasih atas reservasi Anda!</p>
            <p class="sub-text">
                Simpan struk ini sebagai bukti pembayaran yang sah.<br>
                Tunjukkan kode booking saat check-in.
            </p>
        </div>

        <!-- Print Info -->
        <div class="additional-info">
            Dokumen ini dicetak secara otomatis pada {{ now()->isoFormat('dddd, D MMMM YYYY, HH:mm:ss') }} WITA<br>
            © {{ date('Y') }} Lounge Game Room - All Rights Reserved
        </div>
    </div>
</body>
</html>