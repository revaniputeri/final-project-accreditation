{{-- resources/views/level/create_ajax.blade.php --}}
<form id="formCreateLevel" method="POST" action="{{ route('level.store_ajax') }}">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Level</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="kode_level" class="form-label">Kode Level</label>
            <input type="text" class="form-control" id="kode_level" name="kode_level" required>
            <div class="invalid-feedback" id="error_kode_level"></div>
        </div>
        <div class="mb-3">
            <label for="nama_level" class="form-label">Nama Level</label>
            <input type="text" class="form-control" id="nama_level" name="nama_level" required>
            <div class="invalid-feedback" id="error_nama_level"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
