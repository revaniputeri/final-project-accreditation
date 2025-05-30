<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Publikasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama Dosen</th>
                <td>{{ $publikasi->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($publikasi->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tahun Publikasi</th>
                <td>{{ $publikasi->tahun_publikasi }}</td>
            </tr>
            <tr>
                <th>Judul</th>
                <td>{{ $publikasi->judul }}</td>
            </tr>
            <tr>
                <th>Tempat Publikasi</th>
                <td>{{ $publikasi->tempat_publikasi }}</td>
            </tr>
            <tr>
                <th>Jenis Publikasi</th>
                <td>{{ ucfirst($publikasi->jenis_publikasi) }}</td>
            </tr>
            <tr>
                <th>Dana</th>
                <td>Rp {{ number_format($publikasi->dana, 0, ',', '.') }}</td>
            </tr>
            </tr>
            <tr>
                <th>Melibatkan Mahasiswa</th>
                <td>
                    <span class="badge p-2 {{ $publikasi->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                        {{ $publikasi->melibatkan_mahasiswa_s2 ? 'YA' : 'TIDAK' }}
                    </span>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'perlu validasi' => 'badge-warning',
                            'tidak valid' => 'badge-danger',
                        ][$publikasi->status] ?? 'badge-secondary' }}">
                        {{ strtoupper($publikasi->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Sumber Data</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'p3m' => 'badge-primary',
                            'dosen' => 'badge-secondary',
                        ][$publikasi->sumber_data] ?? 'badge-dark' }}">
                        {{ strtoupper($publikasi->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($publikasi->bukti)
                        <a href="{{ asset('storage/p_publikasi/' . $publikasi->bukti) }}" target="_blank">Lihat
                            File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $publikasi->created_at ? $publikasi->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $publikasi->updated_at ? $publikasi->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
        Tutup</button>
</div>
