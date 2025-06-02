<form id="form-create" method="POST">
    @csrf
    <div class="form-group">
        <label for="no_kriteria">No Kriteria</label>
        <input type="text" class="form-control" id="no_kriteria" name="no_kriteria" required>
    </div>
    <div class="form-group">
        <label for="id_user">User</label>
        <select class="form-control" id="id_user" name="id_user" required>
            <option value="">Pilih User</option>
            @foreach($users as $user)
                <option value="{{ $user->id_user }}">{{ $user->username }}</option>
            @endforeach
        </select>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>

<script>
$(document).ready(function() {
    $('#form-create').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('kriteria.store_ajax') }}",
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status) {
                    $('#modal-create').modal('hide');
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
