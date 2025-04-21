<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $penjualan->penjualan_kode }}</title>
    @if(!isset($print) && !request()->has('print'))
    <!-- Bootstrap CSS for non-print views -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            @if(isset($print) || request()->has('print'))
                margin: 0;
                padding: 0;
            @else
                background-color: #f8f9fa;
                padding: 20px;
            @endif
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: white;
            @if(!isset($print) && !request()->has('print'))
                padding: 30px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 5px;
            @endif
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .company-info {
            text-align: left;
        }
        .invoice-info {
            text-align: right;
        }
        .customer-info {
            margin-bottom: 20px;
        }
        .table {
            width: 100%;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .text-right {
            text-align: right;
        }
        .invoice-footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            text-align: center;
            font-size: 0.9em;
            color: #777;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-info">
                <h2 class="invoice-title">Toko ABC</h2>
                <p>Jl. Contoh No. 123<br>
                Kota, Provinsi<br>
                Telp: 08123456789</p>
            </div>
            <div class="invoice-info">
                <h2 class="invoice-title">INVOICE</h2>
                <p><strong>No:</strong> {{ $penjualan->penjualan_kode }}<br>
                <strong>Tanggal:</strong> {{ $penjualan->penjualan_tanggal->format('d/m/Y H:i') }}<br>
                <strong>Kasir:</strong> {{ $penjualan->user->nama }}</p>
            </div>
        </div>

        <div class="customer-info">
            <p><strong>Kepada:</strong> {{ $penjualan->pembeli }}</p>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Jumlah</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penjualan->details as $index => $detail)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $detail->barang->barang_nama }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $detail->jumlah }}</td>
                    <td class="text-right">Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right">Rp {{ number_format($penjualan->details->sum(function($item) {
                        return $item->harga * $item->jumlah;
                    }), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="invoice-footer">
            <p>Terima kasih atas pembelian Anda</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan</p>

            @if(!isset($print) && !request()->has('print'))
            <div class="no-print mt-3">
                <a href="{{ route('penjualan.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> Print PDF
                </a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
