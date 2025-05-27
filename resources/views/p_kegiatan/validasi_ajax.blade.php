<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Kegiatan & Validasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25 bg-light">Nama Dosen</th>
                        <td>{{ $kegiatan->user->profile->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Nama Kegiatan</th>
                        <td>{{ $kegiatan->nama_kegiatan }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jenis Kegiatan</th>
                        <td>{{ $kegiatan->jenis_kegiatan }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal Mulai</th>
                        <td>{{ $kegiatan->tanggal_mulai }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tanggal Selesai</th>
                        <td>{{ $kegiatan->tanggal_selesai }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tempat</th>
                        <td>{{ $kegiatan->tempat }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Peran</th>
                        <td>{{ $kegiatan->peran }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Deskripsi</th>
                        <td>{{ $kegiatan->deskripsi }}</td>
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
                            <span class="badge p-2 {{ $badgeClass[$kegiatan->status] ?? 'badge-secondary' }}">
                                {{ strtoupper($kegiatan->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sumber Data</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$kegiatan->sumber_data] ?? 'badge-dark' }}">
                                {{ strtoupper($kegiatan->sumber_data) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dibuat Pada</th>
                        <td>{{ $kegiatan->created_at ? $kegiatan->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah Pada</th>
                        <td>{{ $kegiatan->updated_at ? $kegiatan->updated_at->format('d M Y H:i') : '-' }}</td>
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
                action="{{ route('p_kegiatan.validasi_update', $kegiatan->id_kegiatan) }}">
                @csrf
                <div class="form-group">
                    <label for="status">Status Validasi</label>
                    <select name="status" id="status" class="form-control select2" style="width: 100%;" required>
                        <option value="Tervalidasi" {{ $kegiatan->status == 'Tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                        <option value="Tidak Valid" {{ $kegiatan->status == 'Tidak Valid' ? 'selected' : '' }}>
                            Tidak Valid
                        </option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
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
                            window.LaravelDataTables["p_kegiatan-table"].ajax.reload();
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
