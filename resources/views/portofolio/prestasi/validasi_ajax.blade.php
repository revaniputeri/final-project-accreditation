<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Prestasi & Validasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25 bg-light">Nama Dosen</th>
                        <td>{{ optional($prestasi->user->profile)->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light align-middle">Prestasi Yang Dicapai</th>
                        <td class="align-middle">{{ $prestasi->prestasi_yang_dicapai }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Waktu Pencapaian</th>
                        <td>{{ date('d-m-Y', strtotime($prestasi->waktu_pencapaian)) }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tingkat</th>
                        <td>{{ $prestasi->tingkat }}</td>
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
                            <span class="badge p-2 {{ $badgeClass[$prestasi->status] ?? 'badge-secondary' }}">
                                {{ strtoupper($prestasi->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sumber Data</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$prestasi->sumber_data] ?? 'badge-dark' }}">
                                {{ strtoupper($prestasi->sumber_data) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Bukti</th>
                        <td>
                            @if($prestasi->bukti)
                                <a href="{{ asset('storage/portofolio/prestasi/' . $prestasi->bukti) }}" target="_blank">Lihat Bukti</a>
                            @else
                                Tidak ada file
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dibuat Pada</th>
                        <td>{{ $prestasi->created_at ? $prestasi->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah Pada</th>
                        <td>{{ $prestasi->updated_at ? $prestasi->updated_at->format('d M Y H:i') : '-' }}</td>
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
                action="{{ route('portofolio.prestasi.validasi_update', $prestasi->id_prestasi) }}">
                @csrf
                <div class="form-group">
                    <label for="status">Status Validasi</label>
                    <select name="status" id="status" class="form-control select2" style="width: 100%;" required>
                        <option value="tervalidasi" {{ $prestasi->status == 'tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                        <option value="tidak valid" {{ $prestasi->status == 'tidak valid' ? 'selected' : '' }}>
                            Tidak Valid
                        </option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Validasi</button>
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
                            window.LaravelDataTables["p_prestasi-table"].ajax.reload();
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
