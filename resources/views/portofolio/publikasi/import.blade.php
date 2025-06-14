<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Import Data Publikasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="form-import" action="{{ route('portofolio.publikasi.import_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Pastikan file Excel mengikuti format template. Perhatikan bahwa kolom
            <b>Jenis Publikasi</b> harus mengikuti pilihan berikut:
            <div class="mt-2">
                <strong>Jenis Publikasi:</strong>
                <ul class="mb-1">
                    <li>Artikel Ilmiah</li>
                    <li>Karya Ilmiah</li>
                    <li>Karya Seni</li>
                    <li>Lainnya</li>
                </ul>
            </div>
            Dan kolom <b>Melibatkan Mahasiswa S2</b> hanya boleh diisi dengan 'Ya' atau 'Tidak'.
        </div>

        <div class="mb-3">
            <label class="form-label">Download Template</label>
            <div>
                <a href="{{ asset('template/template_p_publikasi.xlsx') }}" class="btn btn-success btn-sm" download>
                    <i class="fas fa-file-excel me-1"></i> Download Template
                </a>
            </div>
            <small class="text-muted">Format: .xlsx (Excel)</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <label for="file_p_publikasi" class="btn btn-info">Choose File</label>
            </div>
            <input type="file" class="form-control d-none" id="file_p_publikasi" name="file_p_publikasi" required accept=".xlsx,.xls"
                onchange="document.getElementById('file_p_publikasi_text').value = this.files[0]?.name || 'No file chosen'">
            <input type="text" class="form-control" id="file_p_publikasi_text" placeholder="No file chosen" readonly>
            <div id="error-file_p_publikasi" class="invalid-feedback"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload me-1"></i> Upload
        </button>
    </div>
</form>
