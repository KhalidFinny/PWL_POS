<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $penjualan->first()->penjualan_kode }}</title>
    @if(!isset($print) && !request()->has('print'))
    <!-- Bootstrap CSS for non-print views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
            margin: 0;
            padding: 0;
            font-size: 12pt;
        }

        .invoice-container {
            max-width: 850px;
            margin: auto;
            background: white;
            padding: 10mm;
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
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <div class="company-logo">
                    <!-- Logo placeholder -->
                    <svg width="80" height="80" viewBox="0 0 100 100">
                        <text x="50" y="50" font-size="20" text-anchor="middle" alignment-baseline="middle" font-family="sans-serif" fill="#718096">ABC</text>
                    </svg>
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
                        <span class="invoice-metadata-value">{{ $penjualan->first()->penjualan_kode }}</span>
                    </div>
                    <div class="invoice-metadata-row">
                        <span class="invoice-metadata-label">Tanggal:</span>
                        <span class="invoice-metadata-value">{{ $penjualan->first()->penjualan_tanggal ? $penjualan->first()->penjualan_tanggal->format('d-m-Y') : date('d-m-Y') }}</span>
                    </div>
                    <div class="invoice-metadata-row">
                        <span class="invoice-metadata-label">Kasir:</span>
                        <span class="invoice-metadata-value">{{ $penjualan->first()->user ? $penjualan->first()->user->nama : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-info">
                <h3 class="section-title">Informasi Pembeli</h3>
                <p><strong>Nama:</strong> {{ $penjualan->first()->pembeli }}</p>
                @if(isset($penjualan->first()->alamat))
                <p><strong>Alamat:</strong> {{ $penjualan->first()->alamat }}</p>
                @endif
                @if(isset($penjualan->first()->telepon))
                <p><strong>Telepon:</strong> {{ $penjualan->first()->telepon }}</p>
                @endif
            </div>

            <div class="payment-info">
                <h3 class="section-title">Informasi Pembayaran</h3>
                <p><strong>Metode Pembayaran:</strong> {{ $penjualan->first()->metode_pembayaran ?? 'Tunai' }}</p>
                <p><strong>Tanggal Pembayaran:</strong> {{ $penjualan->first()->penjualan_tanggal ? $penjualan->first()->penjualan_tanggal->format('d-m-Y') : date('d-m-Y') }}</p>
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
                @php $index = 1; @endphp
                @foreach($penjualan as $penjualanItem)
                    @if(isset($penjualanItem->details) && is_iterable($penjualanItem->details))
                        @foreach($penjualanItem->details as $detail)
                            <tr>
                                <td>{{ $index++ }}</td>
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
                    @endif
                @endforeach
            </tbody>
            <tfoot>
                @php
                    $totalBeforeDiscount = $penjualan->sum(function($penjualanItem) {
                        return $penjualanItem->details ? $penjualanItem->details->sum(function($detail) {
                            return $detail->harga * $detail->jumlah;
                        }) : 0;
                    });

                    $discount = $penjualan->first()->diskon ?? 0;
                    $totalAfterDiscount = $totalBeforeDiscount - $discount;
                @endphp

                @if($discount > 0)
                <tr>
                    <td colspan="4" class="text-right"><strong>Subtotal</strong></td>
                    <td class="text-right">Rp {{ number_format($totalBeforeDiscount, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="4" class="text-right"><strong>Diskon</strong></td>
                    <td class="text-right">Rp {{ number_format($discount, 0, ',', '.') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right">Rp {{ number_format($totalAfterDiscount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="notes-section">
            <h3 class="section-title">Catatan</h3>
            <p>{{ $penjualan->first()->catatan ?? 'Terima kasih atas pembelian Anda. Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.' }}</p>
        </div>

        <div class="footer-signature">
            <div class="signature-box">
                <p>Diterima oleh</p>
                <div class="signature-line"></div>
                <p>{{ $penjualan->first()->pembeli }}</p>
            </div>

            <div class="signature-box">
                <p>Hormat kami</p>
                <div class="signature-line"></div>
                <p>{{ $penjualan->first()->user ? $penjualan->first()->user->nama : 'Kasir' }}</p>
            </div>
        </div>

        <div class="invoice-footer">
            <p>Dokumen ini diterbitkan secara elektronik dan sah tanpa tanda tangan basah.</p>
            <p>© {{ date('Y') }} Dealer ABC. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>
