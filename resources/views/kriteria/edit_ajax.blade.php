<form id="form-edit" method="POST" action="{{ route('kriteria.update_ajax', ['no_kriteria' => $kriteria->no_kriteria, 'id_user' => $kriteria->id_user]) }}">
    @csrf
    @method('PUT')
    <div class="form-group">
        <label for="no_kriteria">No Kriteria</label>
        <input type="text" class="form-control" id="no_kriteria" name="no_kriteria" value="{{ $kriteria->no_kriteria }}"
            required>
    </div>
    <div class="form-group">
        <label for="id_user">User</label>
        <select class="form-control" id="id_user" name="id_user" required>
            <option value="">Pilih User</option>
            @foreach($users as $user)
                <option value="{{ $user->id_user }}" {{ $kriteria->id_user == $user->id_user ? 'selected' : '' }}>
                    {{ $user->username }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#form-edit').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('kriteria.update_ajax', ['no_kriteria' => $kriteria->no_kriteria, 'id_user' => $kriteria->id_user]) }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    if (response.status) {
                        $('#modal-edit').modal('hide');
                        $('#kriteria-table').DataTable().ajax.reload();
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function (xhr) {
                    toastr.error('Terjadi kesalahan');
                }
            });
        });
    });
</script>
