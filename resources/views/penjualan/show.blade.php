@extends('Layouts.template')

@section('content')
<div class="container">
    <div class="card invoice-card">
        <div class="card-header bg-white">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-0">INVOICE</h3>
                    <p class="mb-0 text-muted">#{{ $penjualan->penjualan_kode }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <p class="mb-0"><strong>Date:</strong> {{ date('d/m/Y', strtotime($penjualan->penjualan_tanggal)) }}</p>
                    <p class="mb-0"><strong>Cashier:</strong> {{ $penjualan->user->nama }}</p>
                </div>
            </div>
        </div>

        <div class="card-body">
            <!-- Customer Information -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5>Customer</h5>
                    <p class="mb-1"><strong>Name:</strong> {{ $penjualan->pembeli }}</p>
                </div>
                <div class="col-md-6 text-right">
                    <h5>Payment Method</h5>
                    <p class="mb-1">Cash</p> <!-- You can make this dynamic if needed -->
                </div>
            </div>

            <!-- Items Table -->
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th class="text-right">Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($penjualan->details as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->barang ? $detail->barang->brang_kode : '-' }}</td>
                            <td>{{ $detail->barang ? $detail->barang->barang_nama : '-' }}</td>
                            <td class="text-right">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detail->jumlah }}</td>
                            <td class="text-right">{{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right"><strong>Total:</strong></td>
                            <td class="text-right"><strong>{{ number_format($penjualan->details->sum(function($detail) { return $detail->harga * $detail->jumlah; }), 0, ',', '.') }}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer Notes -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="border-top pt-3">
                        <p class="text-muted mb-1"><small>Thank you for your purchase!</small></p>
                        <p class="text-muted mb-0"><small>This is an automated receipt. No signature required.</small></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-md-6">
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print"></i> Print Receipt
                    </button>
                    <a href="{{ url('/penjualan') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Sales List
                    </a>
                </div>
                <div class="col-md-6 text-right">
                    <span class="text-muted">Generated on: {{ date('d/m/Y H:i:s') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .invoice-card {
        max-width: 800px;
        margin: 0 auto;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    @media print {
        body * {
            visibility: hidden;
        }
        .invoice-card, .invoice-card * {
            visibility: visible;
        }
        .invoice-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            box-shadow: none;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush
