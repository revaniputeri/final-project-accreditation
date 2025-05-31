<div class="modal-header bg-info text-white">
    <h5 class="modal-title">Detail Karya Buku</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th width="30%">Nama Dosen</th>
                <td>{{ $karyaBuku->user->profile->nama_lengkap ?? '-' }}</td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ optional($karyaBuku->user->profile)->nidn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Judul Buku</th>
                <td>{{ $karyaBuku->judul_buku ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tahun</th>
                <td>{{ $karyaBuku->tahun ?? '-' }}</td>
            </tr>
            <tr>
                <th>Penerbit</th>
                <td>{{ $karyaBuku->penerbit ?? '-' }}</td>
            </tr>
            <tr>
                <th>ISBN</th>
                <td>{{ $karyaBuku->isbn ?? '-' }}</td>
            </tr>
            <tr>
                <th>Jumlah Halaman</th>
                <td>{{ $karyaBuku->jumlah_halaman ?? '-' }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <span class="badge p-2 {{ [
                        'tervalidasi' => 'badge-success',
                        'tidak valid' => 'badge-danger',
                        'perlu validasi' => 'badge-warning',
                    ][$karyaBuku->status] ?? 'badge-success' }}">
                        {{ strtoupper($karyaBuku->status) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Sumber Data</th>
                <td>
                    <span class="badge p-2 {{ [
                        'p3m' => 'badge-primary',
                        'dosen' => 'badge-secondary',
                    ][$karyaBuku->sumber_data] ?? 'badge-primary' }}">
                        {{ strtoupper($karyaBuku->sumber_data) }}
                    </span>
                </td>
            </tr>
            <tr>
                <th>Bukti</th>
                <td>
                    @if ($karyaBuku->bukti)
                        <a href="{{ asset('storage/portofolio/karya_buku/' . $karyaBuku->bukti) }}" target="_blank">Lihat File</a>
                    @else
                        Tidak ada file
                    @endif
                </td>
            </tr>
            <tr>
                <th>Dibuat Pada</th>
                <td>{{ $karyaBuku->created_at ? $karyaBuku->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Diubah Pada</th>
                <td>{{ $karyaBuku->updated_at ? $karyaBuku->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
        Tutup</button>
</div>
