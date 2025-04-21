@extends('Layouts.template')
@section('content')
    <!-- Mengatur gaya CSS untuk font dan modal -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;700&display=swap');

        body {
            font-family: 'Urbanist', sans-serif; /* Menggunakan font Urbanist untuk seluruh halaman */
        }

        #myModal {
            z-index: 1050 !important; /* Memastikan modal tambah stok berada di atas elemen lain */
        }
        #myModal.show {
            display: block !important;
            opacity: 1 !important; /* Memastikan modal terlihat saat ditampilkan */
        }
        .modal-backdrop {
            z-index: 1040 !important; /* Mengatur z-index backdrop di bawah modal */
        }
        .modal-backdrop.show {
            opacity: 0.5 !important; /* Mengatur opacity backdrop saat modal aktif */
        }
    </style>

    <!-- Kontainer utama untuk card -->
    <div class="card">
        <!-- Header card dengan tombol aksi -->
        <div class="card-header">
            <div class="card-tools">
                <!-- Tombol untuk membuka modal import stok -->
                <button onclick="modalAction('{{ url('/stok/import') }}')" class="btn btn-info">Import Stok</button>
                <!-- Tombol untuk mengunduh data stok dalam format Excel -->
                <a href="{{ url('/stok/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Stok Excel</a>
                <!-- Tombol untuk mengunduh data stok dalam format PDF -->
                <a href="{{ url('/stok/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export Stok PDF</a>
            </div>
        </div>
        <!-- Body card dengan tab dan tabel -->
        <div class="card-body">
            <!-- Navigasi tab untuk stok aktif dan stok kosong -->
            <ul class="nav nav-tabs" id="stockTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="active-tab" data-toggle="tab" href="#active" role="tab">Stok Aktif</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="deleted-tab" data-toggle="tab" href="#deleted" role="tab">Stok Kosong</a>
                </li>
            </ul>

            <!-- Konten tab -->
            <div class="tab-content" id="stockTabsContent">
                <!-- Tab untuk stok aktif -->
                <div class="tab-pane fade show active" id="active" role="tabpanel">
                    <!-- Form filter untuk barang dan supplier -->
                    <div id="filter" class="form-horizontal p-2 border-bottom mb-2">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-sm row text-sm mb-0">
                                    <label for="filter_barang" class="col-md-1 col-form-label text-lg text-blue-500">Filter</label>
                                    <div class="col-md-3">
                                        <!-- Dropdown untuk memfilter berdasarkan barang -->
                                        <select name="filter_barang" class="form-control form-control-sm filter_barang border-2 border-blue-300 rounded-lg">
                                            <option value="">- Semua Barang -</option>
                                            @foreach ($barang as $b)
                                                <option value="{{ $b->barang_id }}">{{ $b->barang_nama }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">Barang</small>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- Dropdown untuk memfilter berdasarkan supplier -->
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
                    <!-- Menampilkan pesan sukses jika ada -->
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <!-- Menampilkan pesan error jika ada -->
                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif
                    <!-- Tabel untuk menampilkan data stok aktif -->
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

                <!-- Tab untuk stok kosong (dihapus) -->
                <div class="tab-pane fade" id="deleted" role="tabpanel">
                    <!-- Tabel untuk menampilkan data stok yang telah dihapus -->
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

    <!-- Modal untuk restore stok -->
    <div class="modal fade" id="restoreModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Restok Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="restoreForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="restore_jumlah">Jumlah Stok</label>
                            <!-- Input untuk jumlah stok yang akan direstok -->
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

    <!-- Modal untuk menambah stok -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambahkan Stok</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form untuk menambah stok -->
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

    <!-- Modal untuk konfirmasi hapus -->
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
                    <!-- Form untuk menghapus stok -->
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
    // Mengatur header CSRF untuk semua request AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    /**
     * Membuka modal untuk aksi tertentu (hapus atau import)
     * @param {string} url - URL untuk mengambil data modal
     */
    function modalAction(url) {
        console.log('Opening modal for URL:', url); // Log untuk debugging
        try {
            if (url.includes('delete_ajax')) {
                // Mengambil data konfirmasi hapus melalui AJAX
                $.get(url, function(response) {
                    if (response.status) {
                        $('#deleteForm').attr('action', response.data.delete_url); // Mengatur URL hapus pada form
                        $('#deleteModal').modal({
                            backdrop: 'static',
                            keyboard: false
                        });
                        $('#deleteModal').modal('show'); // Menampilkan modal hapus
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: response.message
                        });
                    }
                }).fail(function(xhr) {
                    console.error('Error loading delete confirmation:', xhr); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat konfirmasi hapus'
                    });
                });
            } else if (url.includes('import')) {
                // Mengambil form import melalui AJAX
                $.get(url, function(data) {
                    $('#myModal .modal-content').html(data); // Memuat form import ke modal
                    $('#myModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#myModal').modal('show'); // Menampilkan modal import
                }).fail(function(xhr) {
                    console.error('Error loading import form:', xhr); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: 'Gagal memuat form import'
                    });
                });
            }
        } catch (e) {
            console.error('Error in modalAction:', e); // Log error umum
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal membuka modal'
            });
        }
    }

    /**
     * Membuka modal untuk menambah stok
     * @param {string} stokId - ID stok
     * @param {number} currentStock - Jumlah stok saat ini
     */
    function openIncrementModal(stokId, currentStock) {
        console.log('Opening modal for stok_id:', stokId, 'with current stock:', currentStock); // Log untuk debugging
        try {
            $('#stok_id').val(stokId); // Mengatur stok_id pada form
            $('#current-stock').text('Stok saat ini: ' + (currentStock || 0)); // Menampilkan stok saat ini
            $('#stok_jumlah').val(''); // Mengosongkan input jumlah
            $('#error-stok_jumlah').text(''); // Mengosongkan pesan error
            $('#myModal').modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#myModal').modal('show'); // Menampilkan modal tambah stok
            console.log('Modal show triggered'); // Log untuk debugging
        } catch (e) {
            console.error('Error showing modal:', e); // Log error
        }
    }

    /**
     * Menginisialisasi DataTables dan event handler saat dokumen siap
     */
    $(document).ready(function() {
        // Menghancurkan DataTable jika sudah ada untuk mencegah duplikasi
        if ($.fn.DataTable.isDataTable('#table-stok')) {
            $('#table-stok').DataTable().destroy();
        }

        // Inisialisasi DataTable untuk stok aktif
        var tableStok = $('#table-stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('stok/list') }}", // URL untuk mengambil data stok
                dataType: "json",
                type: "POST",
                data: function(d) {
                    // Menambahkan filter barang dan supplier ke request AJAX
                    d.filter_barang = $('.filter_barang').val();
                    d.filter_supplier = $('.filter_supplier').val();
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX error:', xhr.status, xhr.responseText, error, thrown); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'DataTable Error',
                        text: 'Failed to load table data: ' + xhr.status + ' ' + xhr.statusText
                    });
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false }, // Kolom nomor urut
                { data: "barang_id", className: "", width: "20%", orderable: true, searchable: true }, // Kolom nama barang
                { data: "supplier_id", className: "", width: "20%", orderable: true, searchable: true }, // Kolom supplier
                { data: "user_id", className: "text-center", orderable: true, searchable: false }, // Kolom user
                { data: "stok_tanggal", className: "", width: "15%", orderable: true, searchable: false }, // Kolom tanggal stok
                { data: "stok_jumlah", className: "", width: "10%", orderable: true, searchable: false }, // Kolom jumlah stok
                { data: "aksi", className: "text-center", width: "15%", orderable: false, searchable: false } // Kolom aksi
            ]
        });

        // Inisialisasi DataTable untuk stok yang dihapus
        var tableDeletedStok = $('#table-deleted-stok').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('stok/listDeleted') }}", // URL untuk mengambil data stok yang dihapus
                dataType: "json",
                type: "POST",
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX error (deleted):', xhr.status, xhr.responseText, error, thrown); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'DataTable Error',
                        text: 'Failed to load deleted stock data: ' + xhr.status + ' ' + xhr.statusText
                    });
                }
            },
            columns: [
                { data: "DT_RowIndex", className: "text-center", width: "5%", orderable: false, searchable: false }, // Kolom nomor urut
                { data: "barang_nama", className: "", width: "25%", orderable: true, searchable: true }, // Kolom nama barang
                { data: "supplier_nama", className: "", width: "25%", orderable: true, searchable: true }, // Kolom supplier
                { data: "user_nama", className: "text-center", width: "20%", orderable: true, searchable: false }, // Kolom user
                { data: "deleted_at", className: "", width: "15%", orderable: true, searchable: false }, // Kolom tanggal dihapus
                { data: "aksi", className: "text-center", width: "10%", orderable: false, searchable: false } // Kolom aksi
            ]
        });

        // Event handler untuk filter barang dan supplier
        $('.filter_barang, .filter_supplier').off('change').change(function() {
            tableStok.draw(); // Memperbarui tabel saat filter berubah
        });

        // Validasi form tambah stok
        $("#form-increment-stok").validate({
            rules: {
                stok_jumlah: { required: true, number: true, min: 1 } // Jumlah stok wajib angka dan minimal 1
            },
            submitHandler: function(form) {
                console.log('Submitting form with data:', $(form).serialize()); // Log untuk debugging
                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            $('#myModal').modal('hide'); // Menutup modal
                            tableStok.ajax.reload(); // Memperbarui tabel
                            Swal.fire({ icon: 'success', title: 'Berhasil', text: response.message });
                        } else {
                            $('.error-text').text('');
                            if (response.msgField) {
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]); // Menampilkan pesan error per field
                                });
                            }
                            Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: response.message });
                        }
                    },
                    error: function(xhr) {
                        console.error('Form submission error:', xhr); // Log error
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: xhr.responseJSON?.message || 'Unknown error'
                        });
                    }
                });
                return false; // Mencegah submit default
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').find('.error-text').replaceWith(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid'); // Menandai field yang tidak valid
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid'); // Menghapus tanda tidak valid
            }
        });

        // Event handler untuk form hapus
        $('#deleteForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Submitting delete form to:', $(this).attr('action')); // Log untuk debugging
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST', // Menggunakan POST dengan _method=DELETE
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#deleteModal').modal('hide'); // Menutup modal
                        tableStok.ajax.reload(); // Memperbarui tabel
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
                    console.error('Delete error:', xhr); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal menghapus stok'
                    });
                }
            });
        });

        /**
         * Membuka modal untuk restok barang
         * @param {number} stokId - ID stok
         */
        function restoreStock(stokId) {
            console.log('Opening restore modal for stok_id:', stokId); // Log untuk debugging
            try {
                $('#restoreForm').attr('action', "{{ url('stok/restock') }}"); // Mengatur URL restok
                $('#restoreForm').append('<input type="hidden" name="stok_id" value="' + stokId + '">'); // Menambahkan stok_id
                $('#restore_jumlah').val(''); // Mengosongkan input jumlah
                $('#restoreModal').modal({
                    backdrop: 'static',
                    keyboard: false
                });
                $('#restoreModal').modal('show'); // Menampilkan modal restok
            } catch (e) {
                console.error('Error showing restore modal:', e); // Log error
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan',
                    text: 'Gagal membuka modal restok'
                });
            }
        }

        // Event handler untuk form restok
        $('#restoreForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Submitting restore form to:', $(this).attr('action')); // Log untuk debugging
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        $('#restoreModal').modal('hide'); // Menutup modal
                        tableDeletedStok.ajax.reload(); // Memperbarui tabel stok yang dihapus
                        tableStok.ajax.reload(); // Memperbarui tabel stok aktif
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
                    console.error('Restore error:', xhr); // Log error
                    Swal.fire({
                        icon: 'error',
                        title: 'Terjadi Kesalahan',
                        text: xhr.responseJSON?.message || 'Gagal restok'
                    });
                }
            });
        });

        // Event handler untuk navigasi tab
        $('#active-tab').click(function() {
            $(this).addClass('active');
            $('#deleted-tab').removeClass('active');
            tableStok.ajax.url("{{ url('stok/list') }}").load(); // Memuat data stok aktif
        });

        $('#deleted-tab').click(function() {
            $(this).addClass('active');
            $('#active-tab').removeClass('active');
            tableStok.ajax.url("{{ url('stok/listDeleted') }}").load(); // Memuat data stok yang dihapus
        });

        // Menangani error global
        window.onerror = function(message, source, lineno, colno, error) {
            console.error('Global error:', message, 'at', source, lineno, colno, error); // Log error global
        };
    });
</script>
@endpush
