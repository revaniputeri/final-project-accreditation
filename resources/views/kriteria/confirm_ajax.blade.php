<div class="modal-header bg-danger text-white">
    <h5 class="modal-title">Konfirmasi Hapus</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <p>Apakah Anda yakin ingin menghapus kriteria dengan No Kriteria: <strong>{{ $kriteria->no_kriteria }}</strong>?</p>
    <p>Data yang dihapus tidak dapat dikembalikan.</p>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="button" class="btn btn-danger" id="btn-delete">Hapus</button>
</div>

<script>
$(document).ready(function() {
    $('#btn-delete').on('click', function() {
        $.ajax({
            url: "{{ route('kriteria.delete_ajax', ['no_kriteria' => $kriteria->no_kriteria, 'id_user' => $kriteria->id_user]) }}",
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.status) {
                    $('#modal-delete').modal('hide');
                    $('#kriteria-table').DataTable().ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Terjadi kesalahan');
            }
        });
    });
});
</script>
