<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Karya Buku & Validasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25 bg-light">Nama Dosen</th>
                        <td>{{ $karyaBuku->user->profile->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Judul Buku</th>
                        <td>{{ $karyaBuku->judul_buku }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun</th>
                        <td>{{ $karyaBuku->tahun }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Penerbit</th>
                        <td>{{ $karyaBuku->penerbit }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">ISBN</th>
                        <td>{{ $karyaBuku->isbn }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Jumlah Halaman</th>
                        <td>{{ $karyaBuku->jumlah_halaman }}</td>
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
                            <span class="badge p-2 {{ $badgeClass[$karyaBuku->status] ?? 'badge-secondary' }}">
                                {{ strtoupper($karyaBuku->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sumber Data</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$karyaBuku->sumber_data] ?? 'badge-dark' }}">
                                {{ strtoupper($karyaBuku->sumber_data) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dibuat Pada</th>
                        <td>{{ $karyaBuku->created_at ? $karyaBuku->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah Pada</th>
                        <td>{{ $karyaBuku->updated_at ? $karyaBuku->updated_at->format('d M Y H:i') : '-' }}</td>
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
                action="{{ route('p_karya_buku.validasi_ajax', $karyaBuku->id_karya_buku) }}">
                @csrf
                <div class="form-group">
                    <label for="status">Status Validasi</label>
                    <select name="status" id="status" class="form-control select2" style="width: 100%;" required>
                        <option value="tervalidasi" {{ $karyaBuku->status == 'tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                        <option value="tidak valid" {{ $karyaBuku->status == 'tidak valid' ? 'selected' : '' }}>
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
                            window.LaravelDataTables["p_karya_buku-table"].ajax.reload();
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