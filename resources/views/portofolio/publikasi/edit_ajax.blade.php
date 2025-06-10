<form id="formEditPublikasi" method="POST"
    action="{{ route('portofolio.publikasi.update_ajax', ['id' => $publikasi->id_publikasi]) }}"
    enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Publikasi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn"
                    value="{{ optional($publikasi->user->profile)->nidn }}" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif
        <div class="mb-3">
            <label for="judul" class="form-label">Judul</label>
            <input type="text" class="form-control" id="judul" name="judul" value="{{ $publikasi->judul }}"
                required>
            <div class="invalid-feedback" id="error_judul"></div>
        </div>
        <div class="mb-3">
            <label for="tempat_publikasi" class="form-label">Tempat Publikasi</label>
            <input type="text" class="form-control" id="tempat_publikasi" name="tempat_publikasi"
                value="{{ $publikasi->tempat_publikasi }}" required>
            <div class="invalid-feedback" id="error_tempat_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="tahun_publikasi" class="form-label">Tahun Publikasi</label>
            <input type="number" class="form-control" id="tahun_publikasi" name="tahun_publikasi"
                value="{{ $publikasi->tahun_publikasi }}" required>
            <div class="invalid-feedback" id="error_tahun_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="jenis_publikasi" class="form-label">Jenis Publikasi</label>
            <select class="form-control" id="jenis_publikasi" name="jenis_publikasi" required>
                <option value="artikel ilmiah" {{ $publikasi->jenis_publikasi == 'artikel ilmiah' ? 'selected' : '' }}>
                    Artikel Ilmiah</option>
                <option value="karya ilmiah" {{ $publikasi->jenis_publikasi == 'karya ilmiah' ? 'selected' : '' }}>Karya
                    Ilmiah</option>
                <option value="karya seni" {{ $publikasi->jenis_publikasi == 'karya seni' ? 'selected' : '' }}>Karya
                    Seni</option>
                <option value="lainnya" {{ $publikasi->jenis_publikasi == 'lainnya' ? 'selected' : '' }}>Lainnya
                </option>
            </select>
            <div class="invalid-feedback" id="error_jenis_publikasi"></div>
        </div>
        <div class="mb-3">
            <label for="dana" class="form-label">Dana (Rp)</label>
            <label for="dana" class="form-label" style="color: red;">Pastikan inputan tidak memiliki tanda titik (.)
                atau koma (,)</label>
            <input type="number" class="form-control" id="dana" name="dana" value="{{ $publikasi->dana }}"
                required>
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
                <input type="file" class="form-control d-none" id="bukti" name="bukti"
                    accept=".pdf,.jpg,.jpeg,.png"
                    onchange="document.getElementById('bukti_text').value = this.files[0]?.name || 'No file chosen'">
                <input type="text" class="form-control" id="bukti_text" placeholder="No file chosen" readonly>
                <div id="error_bukti" class="invalid-feedback"></div>
            </div>
            @if ($publikasi->bukti)
                <small class="form-text text-muted mt-1">File saat ini:
                    <a href="{{ asset('storage/portofolio/publikasi/' . $publikasi->bukti) }}"
                        target="_blank">{{ $publikasi->bukti }}</a>
                </small>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
            Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
