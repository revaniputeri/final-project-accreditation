<form id="formDeleteKegiatan" method="POST" action="{{ route('portofolio.kegiatan.delete_ajax', ['id' => $kegiatan->id_kegiatan]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Kegiatan</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus kegiatan dengan jenis kegiatan <strong>{{ $kegiatan->jenis_kegiatan }}</strong> yang dilaksanakan di <strong>{{ $kegiatan->tempat }}</strong>?</p>
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
