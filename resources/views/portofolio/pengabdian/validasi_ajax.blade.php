<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Pengabdian & Validasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25 bg-light">Nama Dosen</th>
                        <td>{{ optional($pengabdian->user->profile)->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Judul Pengabdian</th>
                        <td>{{ $pengabdian->judul_pengabdian }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Skema</th>
                        <td>{{ $pengabdian->skema }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun</th>
                        <td>{{ $pengabdian->tahun }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dana</th>
                        <td>Rp {{ number_format($pengabdian->dana, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Peran</th>
                        <td>{{ $pengabdian->peran }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light align-middle">Melibatkan Mahasiswa S2</th>
                        <td class="align-middle">
                            <span
                                class="badge p-2 {{ $pengabdian->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                                {{ $pengabdian->melibatkan_mahasiswa_s2 ? 'YA' : 'TIDAK' }}
                            </span>
                        </td>
                    </tr>
                    @php
                        $badgeClass = [
                            'tervalidasi' => 'badge-success',
                            'perlu validasi' => 'badge-warning',
                            'tidak valid' => 'badge-danger',
                            'p3m' => 'badge-primary',
                            'dosen' => 'badge-secondary',
                        ];
                    @endphp
                    <tr>
                        <th class="bg-light">Status</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$pengabdian->status] ?? 'badge-secondary' }}">
                                {{ strtoupper($pengabdian->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sumber Data</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$pengabdian->sumber_data] ?? 'badge-dark' }}">
                                {{ strtoupper($pengabdian->sumber_data) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Bukti</th>
                        <td>
                            @if($pengabdian->bukti)
                                <a href="{{ asset('storage/portofolio/pengabdian/' . $pengabdian->bukti) }}" target="_blank">Lihat Bukti</a>
                            @else
                                Tidak ada file
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Bukti</th>
                        <td>
                            @if ($pengabdian->bukti)
                                <a href="{{ asset('storage/' . $pengabdian->bukti) }}" target="_blank">Lihat Bukti</a>
                            @else
                                Tidak ada file
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dibuat Pada</th>
                        <td>{{ $pengabdian->created_at ? $pengabdian->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah Pada</th>
                        <td>{{ $pengabdian->updated_at ? $pengabdian->updated_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <hr class="my-4">

    <div class="card card-outline card-warning shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Form Validasi</h5>
        </div>
        <div class="card-body">
            <form id="form-validasi" method="POST"
                action="{{ route('portofolio.pengabdian.validasi_update', $pengabdian->id_pengabdian) }}">
                @csrf
                <div class="form-group">
                    <label for="status">Status Validasi</label>
                    <select name="status" id="status" class="form-control select2" style="width: 100%;" required>
                        <option value="tervalidasi" {{ $pengabdian->status == 'tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                        <option value="tidak valid" {{ $pengabdian->status == 'tidak valid' ? 'selected' : '' }}>
                            Tidak Valid
                        </option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i
                            class="fas fa-times me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan
                        Validasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#form-validasi').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#myModal').modal('hide');
                        if (typeof window.LaravelDataTables !== 'undefined') {
                            window.LaravelDataTables["p_pengabdian-table"].ajax.reload();
                        } else {
                            location.reload();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal memperbarui status',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memperbarui status',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
