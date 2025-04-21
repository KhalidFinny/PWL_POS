<!-- Mengecek apakah data barang kosong -->
@empty($barang)
    <!-- Modal untuk menampilkan pesan error jika data barang tidak ditemukan -->
    <div id="modal-master" class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Kesalahan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h5><i class="icon fas fa-ban"></i> Kesalahan!!!</h5>
                    Data yang anda cari tidak ditemukan
                </div>
                <!-- Tombol kembali ke halaman daftar barang -->
                <a href="{{ url('/barang') }}" class="btn btn-warning">Kembali</a>
            </div>
        </div>
    </div>
@else
    <!-- Form untuk mengedit data barang melalui AJAX -->
    <form action="{{ url('/barang/' . $barang->barang_id . '/update_ajax') }}" method="POST" id="form-edit">
        <!-- Token CSRF untuk keamanan -->
        @csrf
        <!-- Method spoofing untuk HTTP PUT -->
        @method('PUT')
        <!-- Modal dialog untuk form edit barang -->
        <div id="modal-master" class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Data Barang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <!-- Body modal berisi input form -->
                <div class="modal-body">
                    <!-- Input untuk memilih kategori -->
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="kategori_id" id="kategori_id" class="form-control" required>
                            <option value="">- Pilih Kategori -</option>
                            @foreach ($kategori as $item)
                                <option value="{{ $item->kategori_id }}" {{ $barang->kategori_id == $item->kategori_id ? 'selected' : '' }}>
                                    {{ $item->kategori_nama }}
                                </option>
                            @endforeach
                        </select>
                        <small id="error-kategori_id" class="error-text form-text text-danger"></small>
                    </div>
                    <!-- Input untuk kode barang -->
                    <div class="form-group">
                        <label>Kode Barang</label>
                        <input value="{{ $barang->brang_kode }}" type="text" name="brang_kode" id="brang_kode"
                            class="form-control" required>
                        <small id="error-brang_kode" class="error-text form-text text-danger"></small>
                    </div>
                    <!-- Input untuk nama barang -->
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input value="{{ $barang->barang_nama }}" type="text" name="barang_nama" id="barang_nama"
                            class="form-control" required>
                        <small id="error-barang_nama" class="error-text form-text text-danger"></small>
                    </div>
                    <!-- Input untuk harga beli -->
                    <div class="form-group">
                        <label>Harga Beli</label>
                        <input value="{{ $barang->harga_beli }}" type="number" step="0.01" name="harga_beli" id="harga_beli"
                            class="form-control" required>
                        <small id="error-harga_beli" class="error-text form-text text-danger"></small>
                    </div>
                    <!-- Input untuk harga jual -->
                    <div class="form-group">
                        <label>Harga Jual</label>
                        <input value="{{ $barang->harga_jual }}" type="number" step="0.01" name="harga_jual" id="harga_jual"
                            class="form-control" required>
                        <small id="error-harga_jual" class="error-text form-text text-danger"></small>
                    </div>
                </div>
                <!-- Footer modal dengan tombol aksi -->
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </div>
    </form>
    <!-- Script untuk validasi form dan pengiriman data via AJAX -->
    <script>
        $(document).ready(function() {
            // Menginisialisasi validasi form menggunakan jQuery Validate
            $("#form-edit").validate({
                // Aturan validasi untuk setiap input
                rules: {
                    kategori_id: {
                        required: true,
                        number: true
                    },
                    brang_kode: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    barang_nama: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    harga_beli: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    harga_jual: {
                        required: true,
                        number: true,
                        min: 0
                    }
                },
                // Pesan error untuk setiap input
                messages: {
                    kategori_id: {
                        required: "Kategori wajib dipilih",
                        number: "Kategori harus berupa angka"
                    },
                    brang_kode: {
                        required: "Kode barang wajib diisi",
                        minlength: "Kode barang minimal 3 karakter",
                        maxlength: "Kode barang maksimal 20 karakter"
                    },
                    barang_nama: {
                        required: "Nama barang wajib diisi",
                        minlength: "Nama barang minimal 3 karakter",
                        maxlength: "Nama barang maksimal 100 karakter"
                    },
                    harga_beli: {
                        required: "Harga beli wajib diisi",
                        number: "Harga beli harus berupa angka",
                        min: "Harga beli tidak boleh negatif"
                    },
                    harga_jual: {
                        required: "Harga jual wajib diisi",
                        number: "Harga jual harus berupa angka",
                        min: "Harga jual tidak boleh negatif"
                    }
                },
                // Fungsi yang dijalankan saat form valid dan disubmit
                submitHandler: function(form) {
                    // Mengirim data form via AJAX
                    $.ajax({
                        url: form.action,
                        type: form.method,
                        data: $(form).serialize(),
                        success: function(response) {
                            // Jika pembaruan berhasil
                            if (response.status) {
                                $('#myModal').modal('hide'); // Menutup modal
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message
                                });
                                // Memperbarui tabel DataTable
                                dataBarang.ajax.reload();
                            } else {
                                // Menghapus pesan error sebelumnya
                                $('.error-text').text('');
                                // Menampilkan pesan error per field
                                $.each(response.msgField, function(prefix, val) {
                                    $('#error-' + prefix).text(val[0]);
                                });
                                // Menampilkan notifikasi error
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Terjadi Kesalahan',
                                    text: response.message
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Menangani error server
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Terjadi kesalahan pada server: ' + error
                            });
                        }
                    });
                    return false; // Mencegah submit form default
                },
                // Konfigurasi penempatan pesan error
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                // Menandai input yang tidak valid
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                // Menghapus tanda input tidak valid
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endempty
