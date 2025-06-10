<form id="formEditPengabdian" method="POST"
    action="{{ route('portofolio.pengabdian.update_ajax', ['id' => $pengabdian->id_pengabdian]) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Data Pengabdian</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn"
                    value="{{ optional($pengabdian->user->profile)->nidn }}" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif
        <div class="mb-3">
            <label for="judul_pengabdian" class="form-label">Judul Pengabdian</label>
            <input type="text" class="form-control" id="judul_pengabdian" name="judul_pengabdian"
                value="{{ $pengabdian->judul_pengabdian }}" required>
            <div class="invalid-feedback" id="error_judul_pengabdian"></div>
        </div>
        <div class="mb-3">
            <label for="skema" class="form-label">Skema</label>
            <input type="text" class="form-control" id="skema" name="skema"
                value="{{ $pengabdian->skema }}" required>
            <div class="invalid-feedback" id="error_skema"></div>
        </div>
        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="text" class="form-control" id="tahun" name="tahun"
                value="{{ $pengabdian->tahun }}" required>
            <div class="invalid-feedback" id="error_tahun"></div>
        </div>
        <div class="mb-3">
            <label for="dana" class="form-label">Dana</label>
            <input type="text" class="form-control" id="dana" name="dana"
                value="{{ $pengabdian->dana }}" required>
            <div class="invalid-feedback" id="error_dana"></div>
        </div>
        <div class="mb-3">
            <label for="peran" class="form-label">Peran</label>
            <input type="text" class="form-control" id="peran" name="peran"
                value="{{ $pengabdian->peran }}" required>
            <div class="invalid-feedback" id="error_peran"></div>
        </div>
        <div class="mb-3">
            <label for="melibatkan_mahasiswa_s2" class="form-label">Melibatkan Mahasiswa S2</label>
            <select class="form-control" id="melibatkan_mahasiswa_s2" name="melibatkan_mahasiswa_s2" required>
                <option value="">-- Pilih Status --</option>
                <option value="1" {{ $pengabdian->melibatkan_mahasiswa_s2 == 1 ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ $pengabdian->melibatkan_mahasiswa_s2 == 0 ? 'selected' : '' }}>Tidak</option>
            </select>
            <div class="invalid-feedback" id="error_melibatkan_mahasiswa_s2"></div>
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
                <input type="text" class="form-control" id="bukti_text"
                    value="{{ $pengabdian->nama_file ?? 'No file chosen' }}" readonly>
                <div id="error_bukti" class="invalid-feedback"></div>
            </div>
            @if ($pengabdian->bukti)
                <small class="form-text text-muted mt-1">File saat ini:
                    <a href="{{ asset('storage/portofolio/pengabdian/' . $pengabdian->bukti) }}" target="_blank">{{ $pengabdian->bukti }}</a>
                </small>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
