@extends('Layouts.template')
@section('content')
    <style>
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
            <h3 class="card-title">Daftar Stok Barang</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok</button>
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok Excel</a>
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div id="filter" class="form-horizontal p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_barang" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_barang" class="form-control form-control-sm filter_barang">
                                    <option value="">- Semua Barang -</option>
                                    @foreach ($barang as $b)
                                        <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Barang</small>
                            </div>
                            <div class="col-md-3">
                                <select name="filter_supplier" class="form-control form-control-sm filter_supplier">
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
    </div>

    <!-- Modal for Increment Stock -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Increment Stock Quantity</h5>
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
@endsection
@push('js')
    <script>
        // Setup CSRF token for AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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
    </script>
@endpush
