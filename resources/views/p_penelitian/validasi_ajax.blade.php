<div class="modal-header bg-warning text-white">
    <h5 class="modal-title">Validasi Penelitian</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="alert alert-info">
        <strong>Judul Penelitian:</strong> {{ $penelitian->judul_penelitian }}<br>
        <strong>Tahun:</strong> {{ $penelitian->tahun }}<br>
        <strong>Dana:</strong> Rp {{ number_format($penelitian->dana, 0, ',', '.') }}
    </div>

    <form id="formValidasiPenelitian" method="POST" action="{{ route('p_penelitian.validasi_ajax', $penelitian->id_penelitian) }}">
        @csrf
        <div class="form-group">
            <label for="status">Status Validasi</label>
            <select name="status" id="status" class="form-control" required>
                <option value="tervalidasi" {{ $penelitian->status == 'tervalidasi' ? 'selected' : '' }}>Tervalidasi</option>
                <option value="tidak valid" {{ $penelitian->status == 'tidak valid' ? 'selected' : '' }}>Tidak Valid</option>
            </select>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button type="submit" form="formValidasiPenelitian" class="btn btn-warning">Simpan Validasi</button>
</div>

<script>
    $(document).ready(function() {
        $('#formValidasiPenelitian').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#myModal').modal('hide');
                    window.LaravelDataTables["p_penelitian-table"].ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status validasi berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memperbarui status validasi'
                    });
                }
            });
        });
    });
</script>