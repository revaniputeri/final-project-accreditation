<form id="formCreateKriteria" method="POST" action="{{ route('kriteria.store_ajax') }}">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Kriteria</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="no_kriteria" class="form-label">No Kriteria</label>
            <input type="text" class="form-control" id="no_kriteria" name="no_kriteria" readonly>
            <div class="invalid-feedback" id="error_no_kriteria"></div>
        </div>
        <div class="mb-3">
            <label for="id_user" class="form-label">Nama User</label>
            <input type="text" class="form-control" id="id_user" name="id_user" required>
            <div class="invalid-feedback" id="error_id_user"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>

// ... existing code ...
</form>

<script>
    $(document).ready(function() {
        // Fetch the last kriteria number when modal opens
        $.ajax({
            url: '{{ route("kriteria.get_last_number") }}',
            type: 'GET',
            success: function(response) {
                if (response.last_number) {
                    // Extract the number from the last kriteria
                    const lastNumber = parseInt(response.last_number.replace('kriteria', ''));
                    // Set the new kriteria number
                    $('#no_kriteria').val('kriteria' + (lastNumber + 1));
                } else {
                    // If no kriteria exists yet, start with kriteria1
                    $('#no_kriteria').val('kriteria1');
                }
            }
        });
    });
</script>