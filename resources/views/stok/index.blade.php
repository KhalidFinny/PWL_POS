@extends('Layouts.template')
@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;700&display=swap');

        body {
            font-family: 'Urbanist', sans-serif;
        }

        #myModal {
            z-index: 1050 !important;
        }
        #myModal.show {
            display: block !important;
            opacity: 1 !important;
        }
        .modal-backdrop {
            z-index: 1040 !important;
        }
        .modal-backdrop.show {
            opacity: 0.5 !important;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok Excel</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok PDF</a>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" id="stockTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">Stok Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="deleted-tab" data-toggle="tab" href="#deleted" role="tab">Stok Kosong</a>
                </li>
            </ul>

            <div class="tab-content" id="stockTabsContent">
                <div class="tab-pane fade show active" id="active" role="tabpanel">
            <div id="filter" class="form-horizontal p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_barang" class="col-md-1 col-form-label text-lg text-blue-500">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_barang" class="form-control form-control-sm filter_barang border-2 border-blue-300 rounded-lg">
                                    <option value="">- Semua Barang -</option>
                                    @foreach ($barang as $b)
                                        <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Barang</small>
                            </div>
                            <div class="col-md-3">
                                <select name="filter_supplier" class="form-control form-control-sm filter_supplier border-2 border-blue-300 rounded-lg">
                                    <option value="">- Semua Supplier -</option>
                                    @foreach ($supplier as $s)
                                        <option value="{{ $s->supplier_id }}">{{ $s->supplier_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Supplier</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Supplier</th>
                        <th>User</th>
                        <th>Tanggal Stok</th>
                        <th>Jumlah Stok</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <table class="table table-bordered table-sm table-striped table-hover" id="table-stok">
            <!-- ... existing table ... -->
        </table>
    </div>

    <div class="tab-pane fade" id="deleted" role="tabpanel">
        <table class="table table-bordered table-sm table-striped table-hover" id="table-deleted-stok">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Supplier</th>
                    <th>User</th>
                    <th>Tanggal Dihapus</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
</div>
    </div>
    <!-- Modal Restore Stock -->
<div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Restok Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="restoreForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="restore_jumlah">Jumlah Stok</label>
                        <input type="number" class="form-control" id="restore_jumlah" name="stok_jumlah" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Restok</button>
                </div>
            </form>
        </div>
    </div>
</div>

    <!-- Modal for Increment Stock -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambahkan Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="form-increment-stok" action="{{ url('stok/increment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="stok_id" id="stok_id" value="">
                        <div class="form-group">
                            <label for="stok_jumlah">Jumlah Stok untuk Ditambahkan</label>
                            <input type="number" name="stok_jumlah" id="stok_jumlah" class="form-control" min="1" required>
                            <small id="current-stock" class="form-text text-muted">Stok saat ini: -</small>
                            <small id="error-stok_jumlah" class="error-text form-text text-danger"></small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                    <button type="submit" form="form-increment-stok" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <div id="deleteModal" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus stok ini?</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function modalAction(url) {
    console.log('Opening modal for URL:', url);
    try {
        if (url.includes('delete_ajax')) {
            $.get(url, function(response) {
                if (response.status) {
                    $('#deleteForm').attr('action', response.data.delete_url); // Set DELETE route
                    $('#deleteModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#deleteModal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            }).fail(function(xhr) {
                console.error('Error loading delete confirmation:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal memuat konfirmasi hapus'
                });
            });
            } else if (url.includes('import')) {
                // Handle import modal (assuming a separate modal exists)
                $.get(url, function(data) {
                    $('#myModal .modal-content').html(data); // Load import form into modal
                    $('#myModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#myModal').modal('show');
                }).fail(function(xhr) {
                    console.error('Error loading import form:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat form import'
                    });
                });
            }
        } catch (e) {
        console.error('Error in modalAction:', e);
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Gagal membuka modal'
        });
    }
}

        function openIncrementModal(stokId, currentStock) {
            console.log('Opening modal for stok_id:', stokId, 'with current stock:', currentStock);
            try {
                $('#stok_id').val(stokId);
                $('#current-stock').text('Stok saat ini: ' + (currentStock || 0));
                $('#stok_jumlah').val('');
                $('#error-stok_jumlah').text('');
                $('#myModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#myModal').modal('show');
                console.log('Modal show triggered');
            } catch (e) {
                console.error('Error showing modal:', e);
            }
        }

        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#table-stok')) {
                $('#table-stok').DataTable().destroy();
            }

            var tableStok = $('#table-stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ url('stok/list') }}",
                    dataType: "json",
                    type: "POST",
                    data: function(d) {
                        d.filter_barang = $('.filter_barang').val();
                        d.filter_supplier = $('.filter_supplier').val();
                    },
                    error: function(xhr, error, thrown) {
                        console.error('DataTable AJAX error:', xhr.status, xhr.responseText, error, thrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'DataTable Error',
                            text: 'Failed to load table data: ' + xhr.status + ' ' + xhr.statusText
                        });
                    }
                },
                columns: [
                    { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false },
                    { data: "barang_id", className: "", width: "20%", orderable: true, searchable: true },
                    { data: "supplier_id", className: "", width: "20%", orderable: true, searchable: true },
                    { data: "user_id", className: "text-center", orderable: true, searchable: false },
                    { data: "stok_tanggal", className: "", width: "15%", orderable: true, searchable: false },
                    { data: "stok_jumlah", className: "", width: "10%", orderable: true, searchable: false },
                    { data: "aksi", className: "text-center", width: "15%", orderable: false, searchable: false }
                ]
            });

            $('.filter_barang, .filter_supplier').off('change').change(function() {
                tableStok.draw();
            });

            $("#form-increment-stok").validate({
                rules: {
                    stok_jumlah: { required: true, number: true, min: 1 }
                },
                submitHandler: function(form) {
                    console.log('Submitting form with data:', $(form).serialize());
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        dataType: 'json',
                        success: function(response) {
                            if (response.status) {
                                $('#myModal').modal('hide');
                                tableStok.ajax.reload();
                                Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                            } else {
                                $('.error-text').text('');
                                if (response.msgField) {
                                    $.each(response.msgField, function(prefix, val) {
                                        $('#error-' + prefix).text(val[0]);
                                    });
                                }
                                Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: response.message });
                            }
                        },
                        error: function(xhr) {
                            console.error('Form submission error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: xhr.responseJSON?.message || 'Unknown error'
                            });
                        }
                    });
                    return false;
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').find('.error-text').replaceWith(error);
                },
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            window.onerror = function(message, source, lineno, colno, error) {
                console.error('Global error:', message, 'at', source, lineno, colno, error);
            };
        });
        $('#deleteForm').on('submit', function(e) {
        e.preventDefault();
        console.log('Submitting delete form to:', $(this).attr('action'));
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST', // Use POST with _method=DELETE
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#deleteModal').modal('hide');
                    $('#table-stok').DataTable().ajax.reload(); // Reload DataTable
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                console.error('Delete error:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: xhr.responseJSON?.message || 'Gagal menghapus stok'
                });
            }
        });
        // Tab navigation
    $('#active-tab').click(function() {
        $(this).addClass('active');
        $('#deleted-tab').removeClass('active');
        tableStok.ajax.url("{{ url('stok/list') }}?show_deleted=0").load();
    });

    $('#deleted-tab').click(function() {
        $(this).addClass('active');
        $('#active-tab').removeClass('active');
        tableStok.ajax.url("{{ url('stok/list') }}?show_deleted=1").load();
    });

    // Handle restock button
    $(document).on('click', '.restock-btn', function() {
        var stokId = $(this).data('id');
        var isDeleted = $(this).data('deleted') || false;

        $('#restock_stok_id').val(stokId);
        $('#restockForm').attr('action', "{{ url('stok/restock') }}");

        if (isDeleted) {
            $('#restockModalTitle').text('Restok Barang');
            $('#currentStockInfo').text('Stok saat ini: 0 (dihapus)');
        } else {
            $('#restockModalTitle').text('Tambah Stok');
            // Anda bisa menambahkan AJAX untuk mendapatkan jumlah stok saat ini jika diperlukan
            $('#currentStockInfo').text('');
        }

        $('#restockModal').modal('show');
    });

    // Form submission
    $('#restockForm').submit(function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#restockModal').modal('hide');
                    tableStok.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function(xhr) {
                Swal.fire('Error!', xhr.responseJSON.message || 'Terjadi kesalahan', 'error');
            }
        });
    });
});
    </script>
@endpush
