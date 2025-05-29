<form id="formEditProfesi" method="POST"
    action="{{ route('p_profesi.update_ajax', ['id' => $profesi->id_profesi]) }}"
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
            <label for="nama_kegiatan" class="form-label">Nama Kegiatan</label>
            <input type="text" class="form-control" id="nama_kegiatan" name="nama_kegiatan"
                value="{{ $profesi->nama_kegiatan }}" required>
            <div class="invalid-feedback" id="error_nama_kegiatan"></div>
        </div>

        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" class="form-control" id="tahun" name="tahun"
                value="{{ $profesi->tahun }}" required>
            <div class="invalid-feedback" id="error_tahun"></div>
        </div>

        <div class="mb-3">
            <label for="peran" class="form-label">Peran</label>
            <input type="text" class="form-control" id="peran" name="peran"
                value="{{ $profesi->peran }}" required>
            <div class="invalid-feedback" id="error_peran"></div>
        </div>

        {{-- <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="">-- Pilih Status --</option>
                <option value="VERIFIED" {{ $profesi->status == 'VERIFIED' ? 'selected' : '' }}>VERIFIED</option>
                <option value="UNVERIFIED" {{ $profesi->status == 'UNVERIFIED' ? 'selected' : '' }}>UNVERIFIED</option>
            </select>
            <div class="invalid-feedback" id="error_status"></div>
        </div> --}}

        <div class="mb-3">
            <label for="sumber_data" class="form-label">Sumber Data</label>
            <input type="text" class="form-control" id="sumber_data" name="sumber_data"
                value="{{ $profesi->sumber_data }}" required>
            <div class="invalid-feedback" id="error_sumber_data"></div>
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
        </div>

    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
    </div>
</form>