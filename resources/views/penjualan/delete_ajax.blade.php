<div class="modal fade" id="modal-penjualan-delete-{{ $penjualan->penjualan_id }}" tabindex="-1" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus penjualan dengan kode <strong>{{ $penjualan->penjualan_kode }}</strong>?</p>
                <p>Penghapusan akan mengembalikan stok barang yang terkait.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirm-delete-{{ $penjualan->penjualan_id }}">Hapus</button>
            </div>
        </div>
    </div>
</div>

<script>
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
