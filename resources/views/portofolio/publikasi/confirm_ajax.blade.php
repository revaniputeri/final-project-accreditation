<form id="formDeletePublikasi" method="POST" action="{{ route('portofolio.publikasi.delete_ajax', ['id' => $publikasi->id_publikasi]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Publikasi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus publikasi dengan judul <strong>{{ $publikasi->judul }}</strong> dan tahun <strong>{{ $publikasi->tahun_publikasi }}</strong>?</p>
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
