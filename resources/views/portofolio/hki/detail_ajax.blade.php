<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail HKI</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama Dosen</th>
                <td>{{ $hki->dosen->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($hki->dosen->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Judul</th>
                <td>{{ $hki->judul }}</td>
            </tr>
            <tr>
                <th>Tahun</th>
                <td>{{ $hki->tahun }}</td>
            </tr>
            <tr>
                <th>Skema</th>
                <td>{{ $hki->skema }}</td>
            </tr>
            <tr>
                <th>Nomor HKI</th>
                <td>{{ $hki->nomor }}</td>
            </tr>
            <tr>
                <th>Melibatkan Mahasiswa</th>
                <td>
                    <span
                        class="badge p-2 {{ $hki->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                        {{ $hki->melibatkan_mahasiswa_s2 ? 'YA' : 'TIDAK' }}
                    </span>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'tidak valid' => 'badge-danger',
                            'perlu validasi' => 'badge-warning',
                        ][$hki->status] ?? 'badge-secondary' }}">
                        {{ strtoupper($hki->status) }}
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
                        ][$hki->sumber_data] ?? 'badge-dark' }}">
                        {{ strtoupper($hki->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($hki->bukti)
                        <a href="{{ asset('storage/portofolio/hki/' . $hki->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $hki->created_at ? $hki->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $hki->updated_at ? $hki->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
        Tutup</button>
</div>
