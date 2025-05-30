<form id="formDeletePenelitian" method="POST" action="{{ route('p_penelitian.delete_ajax', $penelitian->id_penelitian) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Penelitian</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus penelitian dengan judul <strong>{{ $penelitian->judul_penelitian }}</strong> tahun <strong>{{ $penelitian->tahun }}</strong>?</p>
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
