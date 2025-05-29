<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Pengabdian</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th>Nama User</th>
                <td>{{ $pengabdian->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($pengabdian->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Judul Pengabdian</th>
                <td>{{ $pengabdian->judul_pengabdian }}</td>
            </tr>
            <tr>
                <th>Tahun Pengabdian</th>
                <td>{{ $pengabdian->tahun }}</td>
            </tr>
            <tr>
                <th>Skema</th>
                <td>{{ $pengabdian->skema }}</td>
            <tr>
            <tr>
                <th>Peran</th>
                <td>{{ $pengabdian->peran }}</td>   
            <tr>
            <tr>
                <th>Dana</th>
                <td>{{ $pengabdian->dana }}</td>   
            <tr>
            <tr>
                <th>Melibatkan Mahasiswa</th>
                <td>
                    <span class="badge p-2 {{ $pengabdian->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                        {{ $pengabdian->melibatkan_mahasiswa_s2 ? 'Ya' : 'Tidak' }}
                    </span>
            <tr>
                <th>Status</th>
                <td>
                    <span
                        class="badge p-2 {{ [
                            'tervalidasi' => 'badge-success',
                            'tidak valid' => 'badge-danger',
                            'perlu validasi' => 'badge-warning',
                        ][$pengabdian->status] ?? 'badge-success' }}">
                        {{ strtoupper($pengabdian->status) }}
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
                        ][$pengabdian->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($pengabdian->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($pengabdian->bukti)
                        <a href="{{ asset('storage/p_pengabdian/' . $pengabdian->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $pengabdian->created_at ? $pengabdian->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $pengabdian->updated_at ? $pengabdian->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
</div>