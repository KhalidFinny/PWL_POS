@extends('Layouts.template')
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Penjualan</h3>
            <div class="card-tools">
                <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info">Import Penjualan</button>
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan</a>
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-penjualan">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </button>
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <div class="card-body">
            <div id="filter" class="form-horizontal dilter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_user" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <select name="filter_user" class="form-control form-control-sm filter_user">
                                    <option value="">- Semua User -</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->user_id }}">{{ $u->nama }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">User Penjualan</small>
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
            <table class="table table-bordered table-sm table-striped table-hover" id="table-penjualan">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Penjualan</th>
                        <th>Tanggal</th>
                        <th>Pembeli</th>
                        <th>User</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <!-- Create Penjualan Modal -->
    <div class="modal fade" id="modal-penjualan" tabindex="-1" role="dialog" aria-labelledby="modalPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('penjualan.store_ajax') }}" method="POST" id="form-penjualan">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPenjualanLabel">Tambah Penjualan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Penjualan</label>
                                    <input type="text" class="form-control" name="penjualan_kode"
                                           value="PJ-{{ date('YmdHis') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pembeli</label>
                                    <input type="text" class="form-control" name="pembeli" required>
                                    <small id="error-pembeli" class="error-text text-danger"></small>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Daftar Barang</h5>
                                <p class="text-muted">Tambahkan barang yang ingin dijual, lalu klik "Simpan Penjualan" untuk menyimpan semua barang sekaligus.</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="tabel-barang">
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th>Harga Satuan</th>
                                                <th>Stok Tersedia</th>
                                                <th>Jumlah</th>
                                                <th>Subtotal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Rows will be added dynamically -->
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="4" class="text-right"><strong>Total</strong></td>
                                                <td id="total-harga">Rp 0</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-primary mt-2" id="tambah-barang">
                                    <i class="fas fa-plus"></i> Tambah Barang
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Penjualan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Template for dynamic row -->
    <template id="template-barang-row">
        <tr>
            <td>
                <select class="form-control barang-select" name="stok_id[]" required>
                    <option value="">Pilih Barang</option>
                    @foreach($stok as $item)
                        <option value="{{ $item->stok_id }}"
                                data-harga="{{ $item->barang->harga_jual }}"
                                data-stok="{{ $item->stok_jumlah }}"
                                data-nama="{{ $item->barang->barang_nama }}">
                            {{ $item->barang->barang_nama }} (Stok: {{ $item->stok_jumlah }})
                        </option>
                    @endforeach
                </select>
                <small class="error-barang text-danger"></small>
            </td>
            <td class="harga-satuan">Rp 0</td>
            <td class="stok-tersedia">0</td>
            <td>
                <input type="number" class="form-control jumlah" name="jumlah[]" min="1" value="1" required>
                <small class="error-jumlah text-danger"></small>
            </td>
            <td class="subtotal">Rp 0</td>
            <td>
                <button type="button" class="btn btn-danger btn-sm hapus-barang">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Existing Modal Container -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%">
    </div>
@endsection
@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show');
            });
        }

        var tablePenjualan;
        $(document).ready(function() {
            console.log('Document ready, initializing scripts');

            // Initialize DataTable
            tablePenjualan = $('#table-penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = '{{ csrf_token() }}';
                        d.filter_user = $('.filter_user').val();
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: "penjualan_kode",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "penjualan_tanggal",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            return new Date(data).toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    },
                    {
                        data: "pembeli",
                        className: "",
                        width: "20%",
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: "user.nama",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: false
                    },
                    {
                        data: "aksi",
                        className: "text-center",
                        width: "20%",
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            // Filter functionality
            $('#table-penjualan_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) { // enter key
                    tablePenjualan.search(this.value).draw();
                }
            });

            $('.filter_user').change(function() {
                tablePenjualan.draw();
            });

            // Penjualan Modal functionality
            $('#modal-penjualan').on('show.bs.modal', function() {
                console.log('Modal opened, adding initial row');
                $('#form-penjualan')[0].reset();
                $('#tabel-barang tbody').empty();
                $('#total-harga').text('Rp 0');
                tambahBarangRow();
            });

            // Add new row only if all existing rows are valid
            $('#tambah-barang').click(function() {
                console.log('Tambah Barang clicked');
                if (validateRows()) {
                    console.log('Validation passed, adding new row');
                    tambahBarangRow();
                } else {
                    console.log('Validation failed for Tambah Barang');
                    Swal.fire('Error', 'Harap pilih barang di semua baris sebelum menambah baris baru.', 'error');
                }
            });

            // Remove row
            $(document).on('click', '.hapus-barang', function() {
                $(this).closest('tr').remove();
                hitungTotal();
            });

            // Calculate when barang or quantity changes
            $(document).on('change', '.barang-select, .jumlah', function() {
                updateRowCalculations($(this).closest('tr'));
            });

            // Form submission
            $('#form-penjualan').submit(function(e) {
                e.preventDefault();

                console.log('Form submitted');

                // Validate at least one item
                if ($('.barang-select').length === 0) {
                    console.log('Validation failed: No items added');
                    Swal.fire('Error', 'Minimal tambahkan satu barang', 'error');
                    return;
                }

                // Validate all rows
                if (!validateRows()) {
                    console.log('Validation failed, stopping submission');
                    return;
                }

                console.log('Validation passed, sending AJAX request');
                console.log('Form data:', $(this).serialize());
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success', response);
                        if (response.status) {
                            $('#modal-penjualan').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                if (typeof tablePenjualan !== 'undefined') {
                                    tablePenjualan.ajax.reload(null, false);
                                } else {
                                    window.location.href = response.redirect || '/penjualan';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message
                            });

                            if (response.errors) {
                                $.each(response.errors, function(field, errors) {
                                    $('#error-' + field).text(errors[0]);
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX error', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server: ' + (xhr.responseJSON?.message || xhr.statusText)
                        });
                    }
                });
            });

            // Helper functions
            function tambahBarangRow() {
                const template = $('#template-barang-row').html();
                console.log('Adding row, template:', template);
                $('#tabel-barang tbody').append(template);
                $('#tabel-barang tbody tr:last .barang-select').trigger('change');
                console.log('Row added, table contents:', $('#tabel-barang tbody').html());
            }

            function validateRows() {
                let valid = true;
                $('#tabel-barang tbody tr').each(function(index) {
                    const row = $(this);
                    const select = row.find('select.barang-select');
                    const selectedValue = select.val() || '';
                    const selectedText = select.find('option:selected').text();
                    const stok = select.find('option:selected').data('stok') || 0;
                    const jumlah = row.find('input.jumlah').val() || 0;

                    console.log(`Row ${index}:`, {
                        selectedValue: selectedValue,
                        selectedText: selectedText,
                        stok: stok,
                        jumlah: jumlah
                    });

                    if (parseInt(jumlah) > parseInt(stok)) {
                        console.log('Validation failed: Quantity exceeds stock', { jumlah, stok });
                        row.find('.error-jumlah').text('Jumlah melebihi stok');
                        valid = false;
                    } else {
                        row.find('.error-jumlah').text('');
                    }

                    if (!selectedValue) {
                        console.log('Validation failed: No barang selected');
                        row.find('.error-barang').text('Pilih barang');
                        valid = false;
                    } else {
                        row.find('.error-barang').text('');
                    }
                });
                return valid;
            }

            function updateRowCalculations(row) {
                const select = row.find('select.barang-select');
                const harga = select.find('option:selected').data('harga') || 0;
                const stok = select.find('option:selected').data('stok') || 0;
                const jumlah = row.find('input.jumlah').val() || 0;

                // Update display
                row.find('.harga-satuan').text('Rp ' + harga.toLocaleString());
                row.find('.stok-tersedia').text(stok);

                // Calculate subtotal
                const subtotal = harga * jumlah;
                row.find('.subtotal').text('Rp ' + subtotal.toLocaleString());

                hitungTotal();
            }

            function hitungTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    const subtotalText = $(this).text().replace('Rp ', '').replace(/,/g, '');
                    total += parseInt(subtotalText) || 0;
                });
                $('#total-harga').text('Rp ' + total.toLocaleString());
            }
        });
        $(document).on('click', '.delete-btn', function() {
    var id = $(this).data('id');
    var url = '{{ url("penjualan") }}/' + id;
    var token = '{{ csrf_token() }}';

    Swal.fire({
        title: 'Anda yakin?',
        text: "Data akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                data: {
                    _token: token
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Terhapus!',
                            response.message,
                            'success'
                        );
                        tablePenjualan.ajax.reload(null, false);
                    } else {
                        Swal.fire(
                            'Gagal!',
                            response.message,
                            'error'
                        );
                    }
                },
                error: function(xhr) {
                    var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan pada server';
                    Swal.fire(
                        'Error!',
                        errorMessage,
                        'error'
                    );
                    console.error(xhr.responseJSON?.error);
                }
            });
        }
    });
});
    </script>
@endpush
