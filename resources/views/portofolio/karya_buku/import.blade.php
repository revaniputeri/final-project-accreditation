<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Import Data Karya Buku</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="form-import" action="{{ route('portofolio.karya_buku.import_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Pastikan file Excel mengikuti format template.
        </div>

        <div class="mb-3">
            <label class="form-label">Download Template</label>
            <div>
                <a href="{{ asset('template/template_p_karya_buku.xlsx') }}" class="btn btn-success btn-sm" download>
                    <i class="fas fa-file-excel me-1"></i> Download Template
                </a>
            </div>
            <small class="text-muted">Format: .xlsx (Excel)</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <label for="file_p_karya_buku" class="btn btn-info">Choose File</label>
            </div>
            <input type="file" class="form-control d-none" id="file_p_karya_buku" name="file_p_karya_buku" required
                accept=".xlsx,.xls"
                onchange="document.getElementById('file_p_karya_buku_text').value = this.files[0]?.name || 'No file chosen'">
            <input type="text" class="form-control" id="file_p_karya_buku_text" placeholder="No file chosen" readonly>
            <div id="error-file_p_karya_buku" class="invalid-feedback"></div>
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
