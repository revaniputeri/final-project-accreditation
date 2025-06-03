<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Dokumen Pendukung</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>ID Dokumen Pendukung</th>
                <td>{{ $dokumen_pendukung->id_dokumen_pendukung }}</td>
            </tr>
            <tr>
                <th>No Kriteria</th>
                <td>{{ $dokumen_pendukung->no_kriteria }}</td>
            </tr>
            <tr>
                <th>Diunggah Oleh</th>
                <td>{{ $dokumen_pendukung->user && $dokumen_pendukung->user->profile ? $dokumen_pendukung->user->profile->nama_lengkap : '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ $dokumen_pendukung->user && $dokumen_pendukung->user->profile ? $dokumen_pendukung->user->profile->nidn : '-' }}</td>
            </tr>
            <tr>
                <th>Nama File</th>
                <td>{{ $dokumen_pendukung->nama_file }}</td>
            </tr>
            <tr>
                <th>Keterangan</th>
                <td>{{ $dokumen_pendukung->keterangan }}</td>
            </tr>
            <tr>
                <th>Dokumen Pendukung</th>
                <td>
                    @if ($dokumen_pendukung->path_file)
                        <a href="{{ asset('storage/dokumen_pendukung/' . $dokumen_pendukung->path_file) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $dokumen_pendukung->created_at ? $dokumen_pendukung->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $dokumen_pendukung->updated_at ? $dokumen_pendukung->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Tutup</button>
</div>
