<form id="formDeleteProfesi" method="POST" action="{{ route('p_profesi.delete_ajax', ['id' => $profesi->id_profesi]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Profesi</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus data profesi <strong>{{ $profesi->jenis_profesi }}</strong> di <strong>{{ $profesi->nama_lembaga }}</strong> tahun <strong>{{ $profesi->tahun }}</strong>?</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
    </div>
</form>