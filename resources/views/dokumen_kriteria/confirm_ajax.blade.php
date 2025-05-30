<form id="formDeleteDokumenPendukung" method="POST" action="{{ route('dokumen_kriteria.delete_ajax', ['id' => $dokumen_pendukung->id_dokumen_pendukung]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Dokumen Pendukung</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus HKI dengan nama <strong>{{ $dokumen_pendukung->nama_file }}</strong> dan nomor kriteria <strong>{{ $dokumen_pendukung->no_kriteria }}</strong>?</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-danger">
            <i class="fas fa-trash me-1"></i> Hapus
        </button>
    </div>
</form>
