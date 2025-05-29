<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Profesi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama Dosen</th>
                <td>{{ $profesi->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($profesi->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Perguruan Tinggi</th>
                <td>{{ $profesi->perguruan_tinggi ?? '-' }}</td>
            </tr>
            <tr>
                <th>Kurun Waktu</th>
                <td>{{ $profesi->kurun_waktu ?? '-' }}</td>
            </tr>
            <tr>
                <th>Gelar</th>
                <td>{{ $profesi->gelar ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge p-2 {{ [
                        'tervalidasi' => 'badge-success',
                        'tidak valid' => 'badge-danger',
                        'perlu validasi' => 'badge-warning',
                    ][$profesi->status] ?? 'badge-secondary' }}">
                        {{ strtoupper($profesi->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Sumber Data</th>
                <td>
                    <span class="badge p-2 {{ [
                        'p3m' => 'badge-primary',
                        'dosen' => 'badge-secondary',
                    ][$profesi->sumber_data] ?? 'badge-dark' }}">
                        {{ strtoupper($profesi->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($profesi->bukti)
                        <a href="{{ asset('storage/p_profesi/' . $profesi->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $profesi->created_at ? $profesi->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $profesi->updated_at ? $profesi->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>

        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>