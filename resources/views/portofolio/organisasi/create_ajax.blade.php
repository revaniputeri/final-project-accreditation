<form id="formCreateOrganisasi" method="POST" action="{{ route('portofolio.organisasi.store_ajax') }}"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Organisasi</h5>
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
            <label for="nama_organisasi" class="form-label">Nama Organisasi</label>
            <input type="text" class="form-control" id="nama_organisasi" name="nama_organisasi" required>
            <div class="invalid-feedback" id="error_nama_organisasi"></div>
        </div>
        <div class="mb-3">
            <label for="kurun_waktu" class="form-label">Kurun Waktu</label>
            <input type="text" class="form-control" id="kurun_waktu" name="kurun_waktu"
                placeholder="Contoh: 2015-Sekarang" required>
            <div class="invalid-feedback" id="error_kurun_waktu"></div>
        </div>
        <div class="mb-3">
            <label for="tingkat" class="form-label">Tingkat</label>
            <select class="form-control" id="tingkat" name="tingkat" required>
                <option value="">Pilih Tingkat</option>
                <option value="Nasional">Nasional</option>
                <option value="Internasional">Internasional</option>
            </select>
            <div class="invalid-feedback" id="error_tingkat"></div>
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
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
