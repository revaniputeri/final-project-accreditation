<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Organisasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama User</th>
                <td>{{ $organisasi->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($organisasi->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Nama Organisasi</th>
                <td>{{ $organisasi->nama_organisasi }}</td>
            </tr>
            <tr>
                <th>Kurun Waktu</th>
                <td>{{ $organisasi->kurun_waktu }}</td>
            </tr>
            <tr>
                <th>Tingkat</th>
                <td>{{ $organisasi->tingkat }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'tidak valid' => 'badge-danger',
                            'perlu validasi' => 'badge-warning',
                        ][$organisasi->status] ?? 'badge-success' }}">
                        {{ strtoupper($organisasi->status) }}
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
                        ][$organisasi->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($organisasi->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($organisasi->bukti)
                        <a href="{{ asset('storage/p_organisasi/' . $organisasi->bukti) }}" target="_blank">Lihat
                            File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $organisasi->created_at ? $organisasi->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $organisasi->updated_at ? $organisasi->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>

        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>