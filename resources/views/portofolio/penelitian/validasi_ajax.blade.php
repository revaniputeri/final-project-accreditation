<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Detail Penelitian & Validasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="card card-outline card-info shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-striped mb-0">
                <tbody>
                    <tr>
                        <th class="w-25 bg-light">Nama Dosen</th>
                        <td>{{ optional($penelitian->user->profile)->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Judul Penelitian</th>
                        <td>{{ $penelitian->judul_penelitian }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Skema</th>
                        <td>{{ $penelitian->skema }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Tahun</th>
                        <td>{{ $penelitian->tahun }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dana</th>
                        <td>Rp {{ number_format($penelitian->dana, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Peran</th>
                        <td>{{ $penelitian->peran }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light align-middle">Melibatkan Mahasiswa S2</th>
                        <td class="align-middle">
                            <span
                                class="badge p-2 {{ $penelitian->melibatkan_mahasiswa_s2 ? 'badge-success' : 'badge-danger' }}">
                                {{ $penelitian->melibatkan_mahasiswa_s2 ? 'YA' : 'TIDAK' }}
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
                            <span class="badge p-2 {{ $badgeClass[$penelitian->status] ?? 'badge-secondary' }}">
                                {{ strtoupper($penelitian->status) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Sumber Data</th>
                        <td>
                            <span class="badge p-2 {{ $badgeClass[$penelitian->sumber_data] ?? 'badge-dark' }}">
                                {{ strtoupper($penelitian->sumber_data) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Bukti</th>
                        <td>
                            @if($penelitian->bukti)
                                <a href="{{ asset('storage/portofolio/penelitian/' . $penelitian->bukti) }}" target="_blank">Lihat Bukti</a>
                            @else
                                Tidak ada file
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th class="bg-light">Dibuat Pada</th>
                        <td>{{ $penelitian->created_at ? $penelitian->created_at->format('d M Y H:i') : '-' }}</td>
                    </tr>
                    <tr>
                        <th class="bg-light">Diubah Pada</th>
                        <td>{{ $penelitian->updated_at ? $penelitian->updated_at->format('d M Y H:i') : '-' }}</td>
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
            <form id="form-validasi" method="POST" action="{{ route('portofolio.penelitian.validasi_update', $penelitian->id_penelitian) }}">
                @csrf
                <div class="form-group">
                    <label for="status">Status Validasi</label>
                    <select name="status" id="status" class="form-control select2" style="width: 100%;" required>
                        <option value="tervalidasi" {{ $penelitian->status == 'tervalidasi' ? 'selected' : '' }}>
                            Tervalidasi
                        </option>
                        <option value="tidak valid" {{ $penelitian->status == 'tidak valid' ? 'selected' : '' }}>
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
        $('#formValidasiPenelitian').on('submit', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#myModal').modal('hide');
                    window.LaravelDataTables["p_penelitian-table"].ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Status validasi berhasil diperbarui',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal memperbarui status validasi'
                    });
                }
            });
        });
    });
</script>
