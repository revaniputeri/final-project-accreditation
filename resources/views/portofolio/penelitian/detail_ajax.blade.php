<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Penelitian</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th width="30%">Nama Dosen</th>
                <td>{{ $penelitian->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($penelitian->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Judul Penelitian</th>
                <td>{{ $penelitian->judul_penelitian }}</td>
            </tr>
            <tr>
                <th>Skema</th>
                <td>{{ $penelitian->skema }}</td>
            </tr>
            <tr>
                <th>Tahun</th>
                <td>{{ $penelitian->tahun }}</td>
            </tr>
            <tr>
                <th>Dana</th>
                <td>Rp {{ number_format($penelitian->dana, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Peran</th>
                <td>{{ ucfirst($penelitian->peran) }}</td>
            </tr>
            <tr>
                <th>Melibatkan Mahasiswa</th>
                <td>
                    <span
                        class="badge p-2 {{ $penelitian->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                        {{ $penelitian->melibatkan_mahasiswa_s2 ? 'YA' : 'TIDAK' }}
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
                        ][$penelitian->status] ?? 'badge-secondary' }}">
                        {{ strtoupper($penelitian->status) }}
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
                        ][$penelitian->sumber_data] ?? 'badge-dark' }}">
                        {{ strtoupper($penelitian->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($penelitian->bukti)
                        <a href="{{ asset('storage/portofolio/penelitian/' . $penelitian->bukti) }}" target="_blank">Lihat
                            File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $penelitian->created_at ? $penelitian->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $penelitian->updated_at ? $penelitian->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
        Tutup</button>
</div>
