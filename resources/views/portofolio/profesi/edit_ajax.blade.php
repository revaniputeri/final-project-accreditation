<form id="formEditProfesi" method="POST"
    action="{{ route('portofolio.profesi.update_ajax', ['id' => $profesi->id_profesi]) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Data Profesi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>

    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn"
                    value="{{ optional($profesi->user->profile)->nidn }}" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif

        <div class="mb-3">
            <label for="perguruan_tinggi" class="form-label">Perguruan Tinggi</label>
            <input type="text" class="form-control" id="perguruan_tinggi" name="perguruan_tinggi"
                value="{{ $profesi->perguruan_tinggi ?? '' }}" required>
            <div class="invalid-feedback" id="error_perguruan_tinggi"></div>
        </div>

        <div class="mb-3">
            <label for="kurun_waktu" class="form-label">Kurun Waktu</label>
            <input type="text" class="form-control" id="kurun_waktu" name="kurun_waktu"
                value="{{ $profesi->kurun_waktu ?? '' }}" required>
            <div class="invalid-feedback" id="error_kurun_waktu"></div>
        </div>

        <div class="mb-3">
            <label for="gelar" class="form-label">Gelar</label>
            <input type="text" class="form-control" id="gelar" name="gelar"
                value="{{ $profesi->gelar ?? '' }}" required>
            <div class="invalid-feedback" id="error_gelar"></div>
        </div>

        <div class="mb-3">
            <label for="bukti" class="form-label">Bukti (PDF, JPG, PNG, max 2MB)</label>
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
            @if ($profesi->bukti)
                <small class="form-text text-muted mt-1">File saat ini:
                    <a href="{{ asset('storage/portofolio/profesi/' . $profesi->bukti) }}" target="_blank">{{ $profesi->bukti }}</a>
                </small>
            @endif
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
