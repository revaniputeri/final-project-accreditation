<form id="formCreateKegiatan" method="POST" action="{{ route('p_kegiatan.store_ajax') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Kegiatan</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif
        <div class="mb-3">
            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan" required>
            <div class="invalid-feedback" id="error_nama_kegiatan"></div>
        </div>
        <div class="mb-3">
            <label for="jenis_kegiatan" class="form-label">Jenis Kegiatan</label>
            <input type="text" class="form-control" id="jenis_kegiatan" name="jenis_kegiatan" required>
            <div class="invalid-feedback" id="error_jenis_kegiatan"></div>
        </div>
        <div class="mb-3">
            <label for="waktu" class="form-label">Waktu</label>
            <input type="date" class="form-control" id="waktu" name="waktu" required>
            <div class="invalid-feedback" id="error_waktu"></div>
        </div>
        <div class="mb-3">
            <label for="tempat" class="form-label">Tempat</label>
            <input type="text" class="form-control" id="tempat" name="tempat" required>
            <div class="invalid-feedback" id="error_tempat"></div>
        </div>
        <div class="mb-3">
            <label for="peran" class="form-label">Peran</label>
            <input type="text" class="form-control" id="peran" name="peran" required>
            <div class="invalid-feedback" id="error_peran"></div>
        </div>
        <div class="mb-3">
            <label for="bukti" class="form-label">Bukti (PDF, JPG, PNG)</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <label for="bukti" class="btn btn-info mb-0">Choose File</label>
                </div>
                <input type="file" class="form-control d-none" id="bukti" name="bukti"
                    accept=".pdf,.jpg,.jpeg,.png"
                    onchange="document.getElementById('bukti_text').value = this.files[0]?.name || 'No file chosen'">
                <input type="text" class="form-control" id="bukti_text" placeholder="No file chosen" readonly>
                <div id="error_bukti" class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
