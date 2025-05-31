<form id="formEditPenelitian" method="POST" action="{{ route('portofolio.penelitian.update_ajax', $penelitian->id_penelitian) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Penelitian</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($role === 'ADM')
            <div class="mb-3">
                <label for="nidn" class="form-label">NIDN</label>
                <input type="text" class="form-control" id="nidn" name="nidn" value="{{ optional($penelitian->user->profile)->nidn }}" required>
                <div class="invalid-feedback" id="error_nidn"></div>
            </div>
        @endif
        <div class="mb-3">
            <label for="judul_penelitian" class="form-label">Judul Penelitian</label>
            <input type="text" class="form-control" id="judul_penelitian" name="judul_penelitian" value="{{ $penelitian->judul_penelitian }}" required>
            <div class="invalid-feedback" id="error_judul_penelitian"></div>
        </div>
        <div class="mb-3">
            <label for="skema" class="form-label">Skema</label>
            <input type="text" class="form-control" id="skema" name="skema" value="{{ $penelitian->skema }}" required>
            <div class="invalid-feedback" id="error_skema"></div>
        </div>
        <div class="mb-3">
            <label for="tahun" class="form-label">Tahun</label>
            <input type="number" class="form-control" id="tahun" name="tahun" min="2000" max="{{ date('Y') + 5 }}" value="{{ $penelitian->tahun }}" required>
            <div class="invalid-feedback" id="error_tahun"></div>
        </div>
        <div class="mb-3">
            <label for="dana" class="form-label">Dana (Rp)</label>
            <input type="number" class="form-control" id="dana" name="dana" min="0" value="{{ $penelitian->dana }}" required>
            <div class="invalid-feedback" id="error_dana"></div>
        </div>
        <div class="mb-3">
            <label for="peran" class="form-label">Peran</label>
            <select class="form-control" id="peran" name="peran" required>
                <option value="ketua" {{ $penelitian->peran == 'ketua' ? 'selected' : '' }}>Ketua</option>
                <option value="anggota" {{ $penelitian->peran == 'anggota' ? 'selected' : '' }}>Anggota</option>
            </select>
            <div class="invalid-feedback" id="error_peran"></div>
        </div>
        <div class="mb-3">
            <label for="melibatkan_mahasiswa_s2" class="form-label">Melibatkan Mahasiswa S2</label>
            <select class="form-control" id="melibatkan_mahasiswa_s2" name="melibatkan_mahasiswa_s2" required>
                <option value="1" {{ $penelitian->melibatkan_mahasiswa_s2 ? 'selected' : '' }}>Ya</option>
                <option value="0" {{ !$penelitian->melibatkan_mahasiswa_s2 ? 'selected' : '' }}>Tidak</option>
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
                    onchange="document.getElementById('bukti_text').value = this.files[0]?.name || '{{ $penelitian->bukti ?: 'No file chosen' }}'">
                <input type="text" class="form-control" id="bukti_text" placeholder="{{ $penelitian->bukti ?: 'No file chosen' }}" readonly>
                <div id="error_bukti" class="invalid-feedback"></div>
            </div>
            @if($penelitian->bukti)
                <small class="text-muted">File saat ini: <a href="{{ asset('storage/portofolio/penelitian/' . $penelitian->bukti) }}" target="_blank">{{ $penelitian->bukti }}</a></small>
            @endif
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
