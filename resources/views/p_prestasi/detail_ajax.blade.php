<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Prestasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama User</th>
                <td>{{ $prestasi->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($prestasi->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Prestasi Yang Dicapai</th>
                <td>{{ $prestasi->prestasi_yang_dicapai }}</td>
            </tr>
            <tr>
                <th>Waktu Pencapaian</th>
                <td>{{ date('d-m-Y', strtotime($prestasi->waktu_pencapaian)) }}</td>
            </tr>
            <tr>
                <th>Tingkat</th>
                <td>{{ $prestasi->tingkat }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'tidak valid' => 'badge-danger',
                            'perlu validasi' => 'badge-warning',
                        ][$prestasi->status] ?? 'badge-success' }}">
                        {{ strtoupper($prestasi->status) }}
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
                        ][$prestasi->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($prestasi->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($prestasi->bukti)
                        <a href="{{ asset('storage/p_prestasi/' . $prestasi->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $prestasi->created_at ? $prestasi->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $prestasi->updated_at ? $prestasi->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
