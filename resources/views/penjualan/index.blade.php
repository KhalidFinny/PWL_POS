@extends('Layouts.template')
@section('content')
    <!-- Kontainer utama untuk card -->
    <div class="card">
        <!-- Header card dengan judul dan tombol aksi -->
        <div class="card-header">
            <h3 class="card-title">Daftar Penjualan</h3>
            <div class="card-tools">
                <!-- Tombol untuk membuka modal import penjualan -->
                <button onclick="modalAction('{{ url('/penjualan/import') }}')" class="btn btn-info">Import Penjualan</button>
                <!-- Tombol untuk mengunduh data penjualan dalam format Excel -->
                <a href="{{ url('/penjualan/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i> Export Penjualan</a>
                <!-- Tombol untuk membuka modal tambah penjualan -->
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modal-penjualan">
                    <i class="fas fa-plus"></i> Tambah Penjualan
                </button>
                <!-- Tombol untuk mengunduh data penjualan dalam format PDF -->
                <a href="{{ url('/penjualan/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file-pdf"></i> Export PDF</a>
            </div>
        </div>
        <!-- Body card dengan filter dan tabel -->
        <div class="card-body">
            <!-- Form filter untuk user penjualan -->
            <div id="filter" class="form-horizontal dilter-date p-2 border-bottom mb-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group form-group-sm row text-sm mb-0">
                            <label for="filter_user" class="col-md-1 col-form-label">Filter</label>
                            <div class="col-md-3">
                                <!-- Dropdown untuk memfilter berdasarkan user -->
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
            <!-- Menampilkan pesan sukses jika ada -->
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            <!-- Menampilkan pesan error jika ada -->
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <!-- Tabel untuk menampilkan data penjualan -->
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

    <!-- Modal untuk membuat penjualan baru -->
    <div class="modal fade" id="modal-penjualan" tabindex="-1" role="dialog" aria-labelledby="modalPenjualanLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('penjualan.store_ajax') }}" method="POST" id="form-penjualan">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalPenjualanLabel">Tambah Penjualan Baru</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kode Penjualan</label>
                                    <!-- Input kode penjualan yang otomatis diisi berdasarkan timestamp -->
                                    <input type="text" class="form-control" name="penjualan_kode"
                                           value="PJ-{{ date('YmdHis') }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Pembeli</label>
                                    <!-- Input untuk nama pembeli -->
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
                                    <!-- Tabel untuk daftar barang yang akan dijual -->
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
                                            <!-- Baris akan ditambahkan secara dinamis -->
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
                                <!-- Tombol untuk menambah baris barang -->
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

    <!-- Template untuk baris barang dinamis -->
    <template id="template-barang-row">
        <tr>
            <td>
                <!-- Dropdown untuk memilih barang -->
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
                <!-- Input untuk jumlah barang -->
                <input type="number" class="form-control jumlah" name="jumlah[]" min="1" value="1" required>
                <small class="error-jumlah text-danger"></small>
            </td>
            <td class="subtotal">Rp 0</td>
            <td>
                <!-- Tombol untuk menghapus baris -->
                <button type="button" class="btn btn-danger btn-sm hapus-barang">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <!-- Modal untuk aksi lainnya (seperti import) -->
    <div id="myModal" class="modal fade animate shake" tabindex="-1" data-backdrop="static" data-keyboard="false" data-width="75%">
    </div>
@endsection
@push('js')
    <script>
        /**
         * Membuka modal dengan memuat konten dari URL tertentu
         * @param {string} url - URL untuk mengambil konten modal
         */
        function modalAction(url = '') {
            $('#myModal').load(url, function() {
                $('#myModal').modal('show'); // Menampilkan modal setelah konten dimuat
            });
        }

        var tablePenjualan;
        $(document).ready(function() {
            console.log('Document ready, initializing scripts'); // Log untuk debugging

            // Inisialisasi DataTable untuk tabel penjualan
            tablePenjualan = $('#table-penjualan').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    "url": "{{ url('penjualan/list') }}", // URL untuk mengambil data penjualan
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d) {
                        d._token = '{{ csrf_token() }}'; // Menambahkan CSRF token
                        d.filter_user = $('.filter_user').val(); // Menambahkan filter user
                    }
                },
                columns: [
                    {
                        data: "DT_RowIndex",
                        className: "text-center",
                        width: "5%",
                        orderable: false,
                        searchable: false
                    }, // Kolom nomor urut
                    {
                        data: "penjualan_kode",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: true
                    }, // Kolom kode penjualan
                    {
                        data: "penjualan_tanggal",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: false,
                        render: function(data) {
                            // Format tanggal ke format Indonesia
                            return new Date(data).toLocaleString('id-ID', {
                                day: '2-digit',
                                month: '2-digit',
                                year: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            });
                        }
                    }, // Kolom tanggal penjualan
                    {
                        data: "pembeli",
                        className: "",
                        width: "20%",
                        orderable: true,
                        searchable: true
                    }, // Kolom nama pembeli
                    {
                        data: "user.nama",
                        className: "",
                        width: "15%",
                        orderable: true,
                        searchable: false
                    }, // Kolom nama user
                    {
                        data: "aksi",
                        className: "text-center",
                        width: "20%",
                        orderable: false,
                        searchable: false
                    } // Kolom aksi
                ]
            });

            // Event handler untuk pencarian menggunakan input DataTable
            $('#table-penjualan_filter input').unbind().bind().on('keyup', function(e) {
                if (e.keyCode == 13) { // Hanya cari saat tombol Enter ditekan
                    tablePenjualan.search(this.value).draw();
                }
            });

            // Event handler untuk filter user
            $('.filter_user').change(function() {
                tablePenjualan.draw(); // Memperbarui tabel saat filter user berubah
            });

            // Event handler saat modal penjualan ditampilkan
            $('#modal-penjualan').on('show.bs.modal', function() {
                console.log('Modal opened, adding initial row'); // Log untuk debugging
                $('#form-penjualan')[0].reset(); // Mengosongkan form
                $('#tabel-barang tbody').empty(); // Mengosongkan tabel barang
                $('#total-harga').text('Rp 0'); // Mengatur total harga ke 0
                tambahBarangRow(); // Menambahkan baris barang awal
            });

            // Event handler untuk tombol tambah barang
            $('#tambah-barang').click(function() {
                console.log('Tambah Barang clicked'); // Log untuk debugging
                if (validateRows()) {
                    console.log('Validation passed, adding new row'); // Log untuk debugging
                    tambahBarangRow(); // Menambahkan baris baru
                } else {
                    console.log('Validation failed for Tambah Barang'); // Log untuk debugging
                    Swal.fire('Error', 'Harap pilih barang di semua baris sebelum menambah baris baru.', 'error');
                }
            });

            // Event handler untuk tombol hapus baris
            $(document).on('click', '.hapus-barang', function() {
                $(this).closest('tr').remove(); // Menghapus baris
                hitungTotal(); // Memperbarui total harga
            });

            // Event handler saat barang atau jumlah berubah
            $(document).on('change', '.barang-select, .jumlah', function() {
                updateRowCalculations($(this).closest('tr')); // Memperbarui perhitungan baris
            });

            // Event handler untuk submit form penjualan
            $('#form-penjualan').submit(function(e) {
                e.preventDefault();
                console.log('Form submitted'); // Log untuk debugging

                // Validasi minimal satu barang
                if ($('.barang-select').length === 0) {
                    console.log('Validation failed: No items added'); // Log untuk debugging
                    Swal.fire('Error', 'Minimal tambahkan satu barang', 'error');
                    return;
                }

                // Validasi semua baris
                if (!validateRows()) {
                    console.log('Validation failed, stopping submission'); // Log untuk debugging
                    return;
                }

                console.log('Validation passed, sending AJAX request'); // Log untuk debugging
                console.log('Form data:', $(this).serialize()); // Log data form
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        console.log('AJAX success', response); // Log respons
                        if (response.status) {
                            $('#modal-penjualan').modal('hide'); // Menutup modal
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                if (typeof tablePenjualan !== 'undefined') {
                                    tablePenjualan.ajax.reload(null, false); // Memperbarui tabel
                                } else {
                                    window.location.href = response.redirect || '/penjualan'; // Redirect jika DataTable tidak ada
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
                                    $('#error-' + field).text(errors[0]); // Menampilkan pesan error per field
                                });
                            }
                        }
                    },
                    error: function(xhr) {
                        console.log('AJAX error', xhr); // Log error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan pada server: ' + (xhr.responseJSON?.message || xhr.statusText)
                        });
                    }
                });
            });

            // Event handler untuk tombol hapus penjualan
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
                                    tablePenjualan.ajax.reload(null, false); // Memperbarui tabel
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
                                console.error(xhr.responseJSON?.error); // Log error
                            }
                        });
                    }
                });
            });

            /**
             * Menambahkan baris barang baru ke tabel
             */
            function tambahBarangRow() {
                const template = $('#template-barang-row').html();
                console.log('Adding row, template:', template); // Log untuk debugging
                $('#tabel-barang tbody').append(template); // Menambahkan baris
                $('#tabel-barang tbody tr:last .barang-select').trigger('change'); // Memicu perhitungan baris
                console.log('Row added, table contents:', $('#tabel-barang tbody').html()); // Log untuk debugging
            }

            /**
             * Memvalidasi semua baris barang
             * @returns {boolean} - True jika valid, false jika tidak
             */
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
                    }); // Log untuk debugging

                    if (parseInt(jumlah) > parseInt(stok)) {
                        console.log('Validation failed: Quantity exceeds stock', { jumlah, stok }); // Log untuk debugging
                        row.find('.error-jumlah').text('Jumlah melebihi stok');
                        valid = false;
                    } else {
                        row.find('.error-jumlah').text('');
                    }

                    if (!selectedValue) {
                        console.log('Validation failed: No barang selected'); // Log untuk debugging
                        row.find('.error-barang').text('Pilih barang');
                        valid = false;
                    } else {
                        row.find('.error-barang').text('');
                    }
                });
                return valid;
            }

            /**
             * Memperbarui perhitungan untuk baris tertentu
             * @param {jQuery} row - Elemen baris tabel
             */
            function updateRowCalculations(row) {
                const select = row.find('select.barang-select');
                const harga = select.find('option:selected').data('harga') || 0;
                const stok = select.find('option:selected').data('stok') || 0;
                const jumlah = row.find('input.jumlah').val() || 0;

                // Memperbarui tampilan harga satuan dan stok
                row.find('.harga-satuan').text('Rp ' + harga.toLocaleString());
                row.find('.stok-tersedia').text(stok);

                // Menghitung subtotal
                const subtotal = harga * jumlah;
                row.find('.subtotal').text('Rp ' + subtotal.toLocaleString());

                hitungTotal(); // Memperbarui total harga
            }

            /**
             * Menghitung total harga dari semua baris
             */
            function hitungTotal() {
                let total = 0;
                $('.subtotal').each(function() {
                    const subtotalText = $(this).text().replace('Rp ', '').replace(/,/g, '');
                    total += parseInt(subtotalText) || 0;
                });
                $('#total-harga').text('Rp ' + total.toLocaleString());
            }
        });
    </script>
@endpush
