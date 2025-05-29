<form id="formEditPublikasi" method="POST" action="{{ route('p_publikasi.update_ajax', ['id' => $publikasi->id_publikasi]) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Publikasi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input type="text" class="form-control" id="judul" name="judul" value="{{ $publikasi->judul }}" required>
            <div class="invalid-feedback" id="error_judul"></div>
        </div>
        <div class="mb-3">
            <label for="tempat_publikasi" class="form-label">Tempat Publikasi</label>
            <input type="text" class="form-control" id="tempat_publikasi" name="tempat_publikasi" value="{{ $publikasi->tempat_publikasi }}" required>
            <div class="invalid-feedback" id="error_tempat_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="tahun_publikasi" class="form-label">Tahun Publikasi</label>
            <input type="number" class="form-control" id="tahun_publikasi" name="tahun_publikasi" value="{{ $publikasi->tahun_publikasi }}" required>
            <div class="invalid-feedback" id="error_tahun_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="jenis_publikasi" class="form-label">Jenis Publikasi</label>
            <select class="form-control" id="jenis_publikasi" name="jenis_publikasi" required>
                <option value="jurnal" {{ $publikasi->jenis_publikasi == 'jurnal' ? 'selected' : '' }}>Jurnal</option>
                <option value="prosiding" {{ $publikasi->jenis_publikasi == 'prosiding' ? 'selected' : '' }}>Prosiding</option>
                <option value="poster" {{ $publikasi->jenis_publikasi == 'poster' ? 'selected' : '' }}>Poster</option>
            </select>
            <div class="invalid-feedback" id="error_jenis_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="dana" class="form-label">Dana</label>
            <input type="number" class="form-control" id="dana" name="dana" value="{{ $publikasi->dana }}" required>
            <div class="invalid-feedback" id="error_dana"></div>
        </div>
        <div class="mb-3">
            <label for="melibatkan_mahasiswa_s2" class="form-label">Melibatkan Mahasiswa S2</label>
            <select class="form-control" id="melibatkan_mahasiswa_s2" name="melibatkan_mahasiswa_s2" required>
                <option value="1" {{ $publikasi->melibatkan_mahasiswa_s2 ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ !$publikasi->melibatkan_mahasiswa_s2 ? 'selected' : '' }}>Tidak</option>
            </select>
            <div class="invalid-feedback" id="error_melibatkan_mahasiswa_s2"></div>
        </div>
        <div class="mb-3">
            <label for="bukti" class="form-label">Bukti (PDF, JPG, PNG)</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <label for="bukti" class="btn btn-info mb-0">Choose File</label>
                </div>
                <input type="file" class="form-control d-none" id="bukti" name="bukti" accept=".pdf,.jpg,.jpeg,.png"
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