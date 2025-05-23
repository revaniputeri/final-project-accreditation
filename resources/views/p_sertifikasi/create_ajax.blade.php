<form id="formCreateSertifikasi" method="POST" action="{{ route('p_sertifikasi.store_ajax') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Sertifikasi</h5>
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
            <label for="tahun_diperoleh" class="form-label">Tahun Diperoleh</label>
            <input type="number" class="form-control" id="tahun_diperoleh" name="tahun_diperoleh" required>
            <div class="invalid-feedback" id="error_tahun_diperoleh"></div>
        </div>
        <div class="mb-3">
            <label for="penerbit" class="form-label">Penerbit</label>
            <input type="text" class="form-control" id="penerbit" name="penerbit" required>
            <div class="invalid-feedback" id="error_penerbit"></div>
        </div>
        <div class="mb-3">
            <label for="nama_sertifikasi" class="form-label">Nama Sertifikasi</label>
            <input type="text" class="form-control" id="nama_sertifikasi" name="nama_sertifikasi" required>
            <div class="invalid-feedback" id="error_nama_sertifikasi"></div>
        </div>
        <div class="mb-3">
            <label for="nomor_sertifikat" class="form-label">Nomor Sertifikat</label>
            <input type="text" class="form-control" id="nomor_sertifikat" name="nomor_sertifikat" required>
            <div class="invalid-feedback" id="error_nomor_sertifikat"></div>
        </div>
        <div class="mb-3">
            <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
            <input type="text" class="form-control" id="masa_berlaku" name="masa_berlaku">
            <div class="invalid-feedback" id="error_masa_berlaku"></div>
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
