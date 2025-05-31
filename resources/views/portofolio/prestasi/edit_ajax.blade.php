<form id="formEditPrestasi" method="POST"
    action="{{ route('portofolio.prestasi.update_ajax', ['id' => $prestasi->id_prestasi]) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Prestasi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn"
                    value="{{ optional($prestasi->user->profile)->nidn }}" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif
        <div class="mb-3">
            <label for="prestasi_yang_dicapai" class="form-label">Prestasi Yang Dicapai</label>
            <input type="text" class="form-control" id="prestasi_yang_dicapai" name="prestasi_yang_dicapai"
                value="{{ $prestasi->prestasi_yang_dicapai }}" required>
            <div class="invalid-feedback" id="error_prestasi_yang_dicapai"></div>
        </div>
        <div class="mb-3">
            <label for="waktu_pencapaian" class="form-label">Waktu Pencapaian</label>
            <input type="date" class="form-control" id="waktu_pencapaian" name="waktu_pencapaian"
                value="{{ date('Y-m-d', strtotime($prestasi->waktu_pencapaian)) }}" required>
            <div class="invalid-feedback" id="error_waktu_pencapaian"></div>
        </div>
        <div class="mb-3">
            <label for="tingkat" class="form-label">Tingkat</label>
            <select class="form-control" id="tingkat" name="tingkat" required>
                <option value="">-- Pilih Tingkat --</option>
                <option value="Lokal" {{ $prestasi->tingkat == 'Lokal' ? 'selected' : '' }}>Lokal</option>
                <option value="Nasional" {{ $prestasi->tingkat == 'Nasional' ? 'selected' : '' }}>Nasional</option>
                <option value="Internasional" {{ $prestasi->tingkat == 'Internasional' ? 'selected' : '' }}>Internasional</option>
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
