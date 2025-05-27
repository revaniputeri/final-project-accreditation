<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Kegiatan</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama Dosen</th>
                <td>{{ $kegiatan->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($kegiatan->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Kegiatan</th>
                <td>{{ ucfirst(str_replace('_', ' ', $kegiatan->jenis_kegiatan)) }}</td>
            </tr>
            <tr>
                <th>Tempat</th>
                <td>{{ $kegiatan->tempat }}</td>
            </tr>
            <tr>
                <th>Waktu</th>
                <td>{{ $kegiatan->waktu ? date('d-m-Y', strtotime($kegiatan->waktu)) : '-' }}</td>
            </tr>
            <tr>
                <th>Peran</th>
                <td>{{ ucfirst(str_replace('_', ' dan ', $kegiatan->peran)) }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge p-2 {{ [
                        'tervalidasi' => 'badge-success',
                        'tidak valid' => 'badge-danger',
                        'perlu validasi' => 'badge-warning',
                    ][$kegiatan->status] ?? 'badge-success' }}">
                        {{ strtoupper($kegiatan->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Sumber Data</th>
                <td>
                    <span class="badge p-2 {{ [
                        'p3m' => 'badge-primary',
                        'dosen' => 'badge-secondary',
                    ][$kegiatan->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($kegiatan->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($kegiatan->bukti)
                        <a href="{{ asset('storage/p_kegiatan/' . $kegiatan->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $kegiatan->created_at ? $kegiatan->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $kegiatan->updated_at ? $kegiatan->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
