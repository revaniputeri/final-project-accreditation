<form id="formDeleteKaryaBuku" method="POST" action="{{ route('p_karya_buku.delete_ajax', ['id' => $karyaBuku->id_karya_buku]) }}">
    @csrf
    @method('DELETE')
    <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Konfirmasi Hapus Karya Buku</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <p>Apakah Anda yakin ingin menghapus karya buku dengan judul <strong>{{ $karyaBuku->judul_buku }}</strong> dan ISBN <strong>{{ $karyaBuku->isbn }}</strong>?</p>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-danger">Hapus</button>
    </div>
</form>