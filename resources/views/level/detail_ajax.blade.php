{{-- resources/views/level/create_ajax.blade.php --}}
<form id="formCreateLevel" method="POST" action="{{ route('level.store_ajax') }}">
    @csrf
    <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Detail</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <table class="table table-bordered">
            <tr>
                <th>ID</th>
                <td>{{ $level->id_level }}</td>
            </tr>
            <tr>
                <th>Kode Level</th>
                <td><b>{{ $level->kode_level }}</b></td>
            </tr>
            <tr>
                <th>Nama Level</th>
                <td>{{ $level->nama_level }}</td>
            </tr>
            <tr>
                <th>Created At</th>
                <td>{{ $level->created_at ? $level->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $level->updated_at ? $level->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>
</form>
