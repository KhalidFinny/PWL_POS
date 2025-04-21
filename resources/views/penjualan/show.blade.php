@extends('Layouts.template')

@section('content')
    <style>
        :root {
            --primary-color: #3b82f6;
            --secondary-color: #475569;
            --light-gray: #f8fafc;
            --border-color: #e2e8f0;
            --success-color: #10b981;
        }

        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            @if(isset($print) || request()->has('print'))
                margin: 0;
                padding: 0;
            @else
                background-color: #f1f5f9;
                padding: 20px;
            @endif
        }

        .invoice-container {
            max-width: 850px;
            margin: auto;
            background: white;
            @if(!isset($print) && !request()->has('print'))
                padding: 40px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
                border-radius: 8px;
            @endif
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .company-info {
            display: flex;
            align-items: flex-start;
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background-color: var(--light-gray);
            border-radius: 6px;
            margin-right: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .company-logo img {
            max-width: 100%;
            max-height: 100%;
        }

        .company-details h2 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
            font-size: 22px;
        }

        .company-details p {
            margin: 5px 0;
            color: var(--secondary-color);
            font-size: 14px;
        }

        .invoice-info {
            text-align: right;
        }

        .invoice-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0 0 15px 0;
        }

        .invoice-metadata {
            background-color: var(--light-gray);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 30px;
        }

        .invoice-metadata-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .invoice-metadata-label {
            font-weight: 600;
            color: var(--secondary-color);
        }

        .invoice-metadata-value {
            font-weight: 500;
            text-align: right;
        }

        .invoice-status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: var(--success-color);
            color: white;
            margin-bottom: 10px;
        }

        .customer-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .customer-info, .payment-info {
            flex: 1;
            padding: 20px;
            background-color: var(--light-gray);
            border-radius: 6px;
        }

        .customer-info {
            margin-right: 10px;
        }

        .payment-info {
            margin-left: 10px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 15px 0;
            color: var(--primary-color);
            position: relative;
            padding-bottom: 10px;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        .table th {
            background-color: var(--light-gray);
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 14px;
        }

        .table tbody tr:hover {
            background-color: var(--light-gray);
        }

        .text-right {
            text-align: right;
        }

        .table tfoot td {
            padding-top: 15px;
            font-size: 15px;
        }

        .total-row {
            font-weight: 700;
            color: var(--primary-color);
            font-size: 16px;
            border-top: 2px solid var(--primary-color);
        }

        .notes-section {
            background-color: var(--light-gray);
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 30px;
        }

        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
            text-align: center;
            font-size: 14px;
            color: var(--secondary-color);
        }

        .footer-signature {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            margin: 40px 0 10px 0;
            border-top: 1px solid var(--secondary-color);
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.2s ease;
        }

        .btn i {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background-color: #2563eb;
        }

        .btn-secondary {
            background-color: white;
            color: var(--secondary-color);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: var(--light-gray);
        }

        @media print {
            body {
                padding: 0;
                font-size: 12pt;
            }

            .invoice-container {
                padding: 10mm;
                box-shadow: none;
                max-width: 100%;
            }

            .no-print, .action-buttons {
                display: none !important;
            }

            .table th, .table td {
                padding: 8px;
            }
        }

        @media (max-width: 768px) {
            .invoice-header {
                flex-direction: column;
            }

            .invoice-info {
                text-align: left;
                margin-top: 20px;
            }

            .customer-section {
                flex-direction: column;
            }

            .customer-info, .payment-info {
                margin: 0 0 20px 0;
            }

            .footer-signature {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }
        }
    </style>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-logo">
                    <!-- Replace with actual logo -->
                    <img src="{{ asset('images/logo.png') }}" alt="Dealer ABC Logo" onerror="this.src='data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 100 100\'%3E%3Ctext x=\'50\' y=\'50\' font-size=\'20\' text-anchor=\'middle\' alignment-baseline=\'middle\' font-family=\'sans-serif\' fill=\'%23718096\'%3EABC%3C/text%3E%3C/svg%3E'">
                </div>
                <div class="company-details">
                    <h2>Dealer ABC</h2>
                    <p>Jl. Contoh No. 123</p>
                    <p>Kota, Provinsi</p>
                    <p>Telp: 08123456789</p>
                    <p>Email: info@dealerabc.com</p>
                </div>
            </div>
            <div class="invoice-info">
                <h1 class="invoice-title">INVOICE</h1>
                <div class="invoice-status">LUNAS</div>
                <div class="invoice-metadata">
                    <div class="invoice-metadata-row">
                        <span class="invoice-metadata-label">No. Invoice:</span>
                        <span class="invoice-metadata-value">{{ $penjualan->penjualan_kode }}</span>
                    </div>
                    <div class="invoice-metadata-row">
                        <span class="invoice-metadata-label">Tanggal:</span>
                        <span class="invoice-metadata-value">{{ $penjualan->created_at->format('d-m-Y') }}</span>
                    </div>
                    <div class="invoice-metadata-row">
                        <span class="invoice-metadata-label">Kasir:</span>
                        <span class="invoice-metadata-value">{{ $penjualan->user->nama }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-info">
                <h3 class="section-title">Informasi Pembeli</h3>
                <p><strong>Nama:</strong> {{ $penjualan->pembeli }}</p>
                @if(isset($penjualan->alamat))
                <p><strong>Alamat:</strong> {{ $penjualan->alamat }}</p>
                @endif
                @if(isset($penjualan->telepon))
                <p><strong>Telepon:</strong> {{ $penjualan->telepon }}</p>
                @endif
            </div>

            <div class="payment-info">
                <h3 class="section-title">Informasi Pembayaran</h3>
                <p><strong>Metode Pembayaran:</strong> {{ $penjualan->metode_pembayaran ?? 'Tunai' }}</p>
                <p><strong>Tanggal Pembayaran:</strong> {{ $penjualan->created_at->format('d-m-Y') }}</p>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="40%">Nama Barang</th>
                    <th class="text-right" width="20%">Harga Satuan</th>
                    <th class="text-right" width="15%">Jumlah</th>
                    <th class="text-right" width="20%">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div><strong>{{ $detail->barang->barang_nama }}</strong></div>
                        @if(isset($detail->barang->kode))
                        <div style="font-size: 0.85em; color: #64748b;">{{ $detail->barang->kode }}</div>
                        @endif
                    </td>
                    <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $detail->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                @if(isset($penjualan->diskon) && $penjualan->diskon > 0)
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal</strong></td>
                    <td class="text-right">Rp {{ number_format($penjualan->details->sum(function($item) {
                        return $item->harga * $item->jumlah;
                    }), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Diskon</strong></td>
                    <td class="text-right">Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right">Rp {{ number_format($penjualan->details->sum(function($item) {
                        return $item->harga * $item->jumlah;
                    }) - ($penjualan->diskon ?? 0), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="notes-section">
            <h3 class="section-title">Catatan</h3>
            <p>{{ $penjualan->catatan ?? 'Terima kasih atas pembelian Anda. Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.' }}</p>
        </div>

        <div class="footer-signature">
            <div class="signature-box">
                <p>Diterima oleh</p>
                <div class="signature-line"></div>
                <p>{{ $penjualan->pembeli }}</p>
            </div>

            <div class="signature-box">
                <p>Hormat kami</p>
                <div class="signature-line"></div>
                <p>{{ $penjualan->user->nama }}</p>
            </div>
        </div>

        <div class="invoice-footer">
            <p>Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.</p>
            <p>© {{ date('Y') }} Dealer ABC. Semua hak dilindungi.</p>
        </div>

        @if(!isset($print) && !request()->has('print'))
        <div class="action-buttons no-print">
            <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('penjualan.pdf', $penjualan->id) }}" class="btn btn-primary">
                <i class="fas fa-file-pdf"></i> PDF
            </a>
            <a href="#" onclick="window.print(); return false;" class="btn btn-secondary">
                <i class="fas fa-print"></i> Print Langsung
            </a>
        </div>
        @endif
    </div>
@endsection

@push('css')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
@endpush

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Optional: Add any JavaScript functionality here
    });
</script>
@endpush
