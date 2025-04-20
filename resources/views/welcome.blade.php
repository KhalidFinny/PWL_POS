@extends('Layouts.template')
@section('content')
<div class="container-fluid py-4">
    <!-- Ikhtisar Inventaris -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-bottom-0 position-relative" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); padding: 1.5rem;">
                    <h3 class="card-title m-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-tachometer-alt mr-2" style="color: #3490dc;"></i>Dashboard Inventaris
                    </h3>
                    <div class="position-absolute" style="bottom: 0; left: 0; width: 100px; height: 4px; background: linear-gradient(90deg, #3490dc, #6574cd);"></div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Ringkasan Stok -->
                        <div class="col-md-4">
                            <div class="position-relative p-4 mb-4" style="border: 2px solid #3490dc; border-radius: 12px; box-shadow: 0 4px 6px rgba(52, 144, 220, 0.1); transition: all 0.3s;">
                                <div class="position-absolute" style="top: -2px; left: 20px; right: 20px; height: 4px; background: #3490dc; border-radius: 0 0 4px 4px;"></div>
                                <div class="inner">
                                    <h1 style="font-size: 2.5rem; font-weight: 700; color: #3490dc;">{{ $totalStockItems }}</h1>
                                    <p style="color: #718096; font-weight: 500; margin-bottom: 1.5rem;">Total Item Stok</p>
                                </div>
                                <div class="position-absolute" style="top: 15px; right: 15px; opacity: 0.2;">
                                    <i class="fas fa-cubes fa-3x" style="color: #3490dc;"></i>
                                </div>
                                <a href="{{ url('/stok') }}" class="d-block text-center p-2 mt-3" style="color: #3490dc; border: 1px solid #3490dc; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                                    Lihat Stok <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Ringkasan Produk -->
                        <div class="col-md-4">
                            <div class="position-relative p-4 mb-4" style="border: 2px solid #38c172; border-radius: 12px; box-shadow: 0 4px 6px rgba(56, 193, 114, 0.1); transition: all 0.3s;">
                                <div class="position-absolute" style="top: -2px; left: 20px; right: 20px; height: 4px; background: #38c172; border-radius: 0 0 4px 4px;"></div>
                                <div class="inner">
                                    <h1 style="font-size: 2.5rem; font-weight: 700; color: #38c172;">{{ $totalProducts }}</h1>
                                    <p style="color: #718096; font-weight: 500; margin-bottom: 1.5rem;">Total Produk</p>
                                </div>
                                <div class="position-absolute" style="top: 15px; right: 15px; opacity: 0.2;">
                                    <i class="fas fa-car fa-3x" style="color: #38c172;"></i>
                                </div>
                                <a href="{{ url('/barang') }}" class="d-block text-center p-2 mt-3" style="color: #38c172; border: 1px solid #38c172; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                                    Lihat Produk <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Peringatan Stok Rendah -->
                        <div class="col-md-4">
                            <div class="position-relative p-4 mb-4" style="border: 2px solid #e3342f; border-radius: 12px; box-shadow: 0 4px 6px rgba(227, 52, 47, 0.1); transition: all 0.3s;">
                                <div class="position-absolute" style="top: -2px; left: 20px; right: 20px; height: 4px; background: #e3342f; border-radius: 0 0 4px 4px;"></div>
                                <div class="inner">
                                    <h1 style="font-size: 2.5rem; font-weight: 700; color: #e3342f;">{{ $lowStockCount }}</h1>
                                    <p style="color: #718096; font-weight: 500; margin-bottom: 1.5rem;">Item Stok Rendah</p>
                                </div>
                                <div class="position-absolute" style="top: 15px; right: 15px; opacity: 0.2;">
                                    <i class="fas fa-exclamation-triangle fa-3x" style="color: #e3342f;"></i>
                                </div>
                                <a href="#lowStockTable" class="d-block text-center p-2 mt-3" style="color: #e3342f; border: 1px solid #e3342f; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                                    Lihat Detail <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Aktivitas Stok Terbaru -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-bottom-0 position-relative" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); padding: 1.5rem;">
                    <h3 class="card-title m-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-history mr-2" style="color: #38c172;"></i>Aktivitas Terbaru
                    </h3>
                    <div class="position-absolute" style="bottom: 0; left: 0; width: 100px; height: 4px; background: linear-gradient(90deg, #38c172, #4dc0b5);"></div>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled">
                        @foreach($recentActivities as $activity)
                        <li class="d-flex align-items-center p-3 mb-3" style="border-left: 4px solid #38c172; border-radius: 8px; background-color: rgba(56, 193, 114, 0.05);">
                            <div class="activity-icon mr-3">
                                <i class="fas fa-car-side" style="font-size: 1.5rem; color: #38c172;"></i>
                            </div>
                            <div class="activity-info flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <strong style="color: #2d3748;">{{ $activity->barang->barang_nama }}</strong>
                                    <span style="border: 1px solid #38c172; color: #38c172; border-radius: 15px; padding: 0 8px; font-size: 0.8rem;">{{ number_format($activity->stok_jumlah) }}</span>
                                </div>
                                <div style="color: #718096; font-size: 0.85rem;">
                                    Ditambahkan oleh {{ $activity->user->nama }} pada {{ \Carbon\Carbon::parse($activity->stok_tanggal)->format('d M, Y') }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center" style="background: transparent; border-top: 1px dashed #d2d6dc;">
                    <a href="{{ url('/stok') }}" style="color: #38c172; text-decoration: none; font-weight: 500;">Lihat Semua Aktivitas Stok <i class="fas fa-arrow-right ml-1"></i></a>
                </div>
            </div>
        </div>

        <!-- Produk Teratas -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-bottom-0 position-relative" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); padding: 1.5rem;">
                    <h3 class="card-title m-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-trophy mr-2" style="color: #f6ad55;"></i>Produk Teratas (Berdasarkan Stok)
                    </h3>
                    <div class="position-absolute" style="bottom: 0; left: 0; width: 100px; height: 4px; background: linear-gradient(90deg, #f6ad55, #ed8936);"></div>
                </div>
                <div class="card-body p-3">
                    @foreach($topProducts as $product)
                    <div class="d-flex align-items-center p-3 mb-3" style="border: 1px solid #f6ad55; border-radius: 12px; transition: all 0.2s;">
                        <div class="product-icon mr-3">
                            <div style="width: 50px; height: 50px; background-color: rgba(246, 173, 85, 0.1); border-radius: 10px; display: flex; justify-content: center; align-items: center;">
                                <i class="fas fa-car" style="font-size: 1.5rem; color: #f6ad55;"></i>
                            </div>
                        </div>
                        <div class="product-info flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <strong style="color: #2d3748;">{{ $product->barang_nama }}</strong>
                                <span style="border: 1px solid #f6ad55; color: #f6ad55; border-radius: 15px; padding: 0 8px; font-size: 0.8rem;">Stok: {{ $product->total_stock }}</span>
                            </div>
                            <div style="color: #718096; font-size: 0.85rem;">
                                <span>Harga: Rp {{ number_format($product->harga_jual) }}</span>
                                <span class="ml-3">Modal: Rp {{ number_format($product->harga_beli) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Item Stok Rendah -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" id="lowStockTable" style="border-radius: 15px; overflow: hidden;">
                <div class="card-header border-bottom-0 position-relative" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); padding: 1.5rem;">
                    <h3 class="card-title m-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-exclamation-circle mr-2" style="color: #e3342f;"></i>Item Stok Rendah
                    </h3>
                    <div class="position-absolute" style="bottom: 0; left: 0; width: 100px; height: 4px; background: linear-gradient(90deg, #e3342f, #f66d9b);"></div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive p-3" style="border: 1px dashed #d2d6dc; border-radius: 10px; margin: 15px;">
                        <table class="table" style="margin-bottom: 0;">
                            <thead>
                                <tr style="border-bottom: 2px solid #f1f5f9;">
                                    <th style="border-top: none; color: #718096;">Nama Produk</th>
                                    <th style="border-top: none; color: #718096;">Kode</th>
                                    <th style="border-top: none; color: #718096;">Kategori</th>
                                    <th style="border-top: none; color: #718096;">Stok Saat Ini</th>
                                    <th style="border-top: none; color: #718096;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockItems as $item)
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="vertical-align: middle;">{{ $item->barang->barang_nama }}</td>
                                    <td style="vertical-align: middle;">{{ $item->barang->brang_kode }}</td>
                                    <td style="vertical-align: middle;">{{ $item->barang->kategori->kategori_nama }}</td>
                                    <td style="vertical-align: middle;">
                                        <span style="border: 1px solid #e3342f; color: #e3342f; border-radius: 15px; padding: 3px 10px; font-size: 0.8rem;">{{ $item->stok_jumlah }}</span>
                                    </td>
                                    <td style="vertical-align: middle;">
                                        <button
                                            onclick="openIncrementModal('{{ $item->stok_id }}', '{{ $item->stok_jumlah }}')"
                                            style="background-color: transparent; border: 1px solid #38c172; color: #38c172; padding: 5px 15px; border-radius: 20px; transition: all 0.2s;">
                                            <i class="fas fa-plus-circle mr-1"></i> Tambah Stok
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Stok -->
<div class="modal fade" id="incrementStockModal" tabindex="-1" role="dialog" aria-labelledby="incrementStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: none; border-radius: 15px; overflow: hidden;">
            <div class="modal-header position-relative" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); border-bottom: none; padding: 1.5rem 1.5rem 1rem 1.5rem;">
                <h5 class="modal-title" id="incrementStockModalLabel" style="color: #38c172; font-weight: 600;">Tambah Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #718096;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="position-absolute" style="bottom: 0; left: 0; width: 80px; height: 3px; background: #38c172;"></div>
            </div>
            <form id="incrementStockForm" action="{{ url('/stok/increment') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <input type="hidden" name="stok_id" id="stok_id">
                    <div class="form-group">
                        <label for="current_stock" style="color: #718096; font-weight: 500;">Stok Saat Ini</label>
                        <input type="text" class="form-control" id="current_stock" readonly style="border: 1px solid #d2d6dc; border-radius: 10px; padding: 10px 15px;">
                    </div>
                    <div class="form-group">
                        <label for="stok_jumlah" style="color: #718096; font-weight: 500;">Tambah Jumlah</label>
                        <input type="number" class="form-control" name="stok_jumlah" id="stok_jumlah" min="1" required style="border: 1px solid #d2d6dc; border-radius: 10px; padding: 10px 15px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px dashed #d2d6dc; padding: 1rem 1.5rem;">
                    <button type="button" style="background-color: transparent; border: 1px solid #718096; color: #718096; padding: 8px 20px; border-radius: 20px;" data-dismiss="modal">Tutup</button>
                    <button type="submit" style="background-color: #38c172; border: 1px solid #38c172; color: white; padding: 8px 20px; border-radius: 20px;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@parent
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.position-relative');
        cards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                if (this.style.transform !== undefined) {
                    this.style.transform = 'translateY(-5px)';
                    this.style.boxShadow = '0 10px 15px rgba(0, 0, 0, 0.1)';
                }
            });

            card.addEventListener('mouseleave', function() {
                if (this.style.transform !== undefined) {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.05)';
                }
            });
        });

        window.openIncrementModal = function(stokId, currentStock) {
            document.getElementById('stok_id').value = stokId;
            document.getElementById('current_stock').value = currentStock;
            $('#incrementStockModal').modal('show');
        };

        $('#incrementStockForm').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            confirmButtonColor: '#38c172'
                        }).then((result) => {
                            $('#incrementStockModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Kesalahan',
                            text: response.message,
                            confirmButtonColor: '#e3342f'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Kesalahan',
                        text: 'Gagal memperbarui stok.',
                        confirmButtonColor: '#e3342f'
                    });
                }
            });
        });

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '{{ session('success') }}',
                confirmButtonColor: '#38c172'
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: '{{ session('error') }}',
                confirmButtonColor: '#e3342f'
            });
        @endif
    });
</script>
@endsection
