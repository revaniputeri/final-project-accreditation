<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Sertifikasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama User</th>
                <td>{{ $sertifikasi->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($sertifikasi->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tahun Diperoleh</th>
                <td>{{ $sertifikasi->tahun_diperoleh }}</td>
            </tr>
            <tr>
                <th>Penerbit</th>
                <td>{{ $sertifikasi->penerbit }}</td>
            </tr>
            <tr>
                <th>Nama Sertifikasi</th>
                <td>{{ $sertifikasi->nama_sertifikasi }}</td>
            </tr>
            <tr>
                <th>Nomor Sertifikat</th>
                <td>{{ $sertifikasi->nomor_sertifikat }}</td>
            </tr>
            <tr>
                <th>Masa Berlaku</th>
                <td>{{ $sertifikasi->masa_berlaku }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'tidak valid' => 'badge-danger',
                            'perlu validasi' => 'badge-warning',
                        ][$sertifikasi->status] ?? 'badge-success' }}">
                        {{ strtoupper($sertifikasi->status) }}
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
                        ][$sertifikasi->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($sertifikasi->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($sertifikasi->bukti)
                        <a href="{{ asset('storage/p_sertifikasi/' . $sertifikasi->bukti) }}" target="_blank">Lihat
                            File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $sertifikasi->created_at ? $sertifikasi->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $sertifikasi->updated_at ? $sertifikasi->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>

        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>
