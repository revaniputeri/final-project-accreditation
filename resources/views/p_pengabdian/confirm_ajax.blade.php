<form id="formDeletePengabdian" method="POST" action="{{ route('p_pengabdian.delete_ajax', ['id' => $pengabdian->id_pengabdian]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Pengabdian</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data pengabdian berjudul <strong>{{ $pengabdian->judul_pengabdian }}</strong> yang dilaksanakan pada <strong>{{ $pengabdian->tahun_pengabdian }}</strong>?</p>
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
