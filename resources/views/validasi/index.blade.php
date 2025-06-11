@extends('layouts.app')

@section('title', 'Validasi')
@section('subtitle', 'Validasi')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Validasi Dokumen Kriteria</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary border-bottom">
                <h3 class="card-title">Validasi Dokumen Kriteria</h3>
            </div>
            <div class="card-body">
                <!-- Dropdown Pilihan Kriteria -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pilih Nomor Kriteria</label>
                            <select id="kriteriaDropdown" class="form-control select2">
                                <option value="" selected disabled>-- Pilih Kriteria --</option>
                                @foreach ($dokumenKriteria as $dokumen)
                                    <option value="{{ $dokumen->no_kriteria }}">
                                        Kriteria {{ $dokumen->no_kriteria }} - {{ $dokumen->judul }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pilih Kategori</label>
                            <select id="kategoriDropdown" class="form-control select2">
                                <option value="" selected disabled>-- Pilih Kategori --</option>
                                <option value="penetapan">Penetapan</option>
                                <option value="pelaksanaan">Pelaksanaan</option>
                                <option value="evaluasi">Evaluasi</option>
                                <option value="pengendalian">Pengendalian</option>
                                <option value="peningkatan">Peningkatan</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Tabel Informasi Dokumen -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Informasi Dokumen Kriteria</h3>
                            </div>
                            <div class="card-body p-0">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th width="15%">No Kriteria</th>
                                            <th width="35%">Judul</th>
                                            <th width="15%">Versi</th>
                                            <th width="15%">Status</th>
                                            <th width="20%">Terakhir Diupdate</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dokumenInfoBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Silakan pilih kriteria
                                                terlebih dahulu</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview PDF dan Form Validasi -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Preview Dokumen & Form Validasi</h3>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- PDF Preview -->
                                    <div class="col-md-6">
                                        <div class="border rounded p-2" style="background-color: #f8f9fa;">
                                            <div style="width: 100%; height: 842px;"> <!-- A4 height -->
                                                <iframe id="pdfPreview" src=""
                                                    style="width: 100%; height: 100%; border: none;">
                                                    Browser Anda tidak mendukung PDF viewer.
                                                </iframe>
                                            </div>
                                            <div class="text-center mt-2">
                                                <small class="text-muted">Preview dokumen PDF (Ukuran A4)</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form Validasi -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="statusSelect">Status Validasi</label>
                                            <select id="statusSelect" class="form-control">
                                                <option value="" selected disabled>-- Pilih Status --</option>
                                                <option value="tervalidasi">Tervalidasi</option>
                                                <option value="revisi">Revisi</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="komentarInput">Komentar Validasi</label>
                                            <textarea id="komentarInput" class="form-control" rows="8" placeholder="Masukkan komentar validasi..."></textarea>
                                        </div>

                                        <button id="submitValidation" class="btn btn-primary btn-block" disabled>
                                            <i class="fas fa-check-circle mr-2"></i>Submit Validasi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <style>
        .embed-responsive-16by9 {
            padding-bottom: 56.25%;
        }

        .embed-responsive {
            position: relative;
            display: block;
            width: 100%;
            padding: 0;
            overflow: hidden;
        }

        .embed-responsive .embed-responsive-item {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .card-info {
            border-top: 3px solid #17a2b8;
        }
    </style>
@endpush

@push('scripts')
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2({
                placeholder: "-- Pilih Kriteria --",
                allowClear: true
            });

            var selectedKriteria = null;

            function loadPdfAndData(kriteriaId, kategori) {
                if (!kriteriaId || !kategori) return;

                $('#pdfPreview').attr('src', '');
                $('#statusSelect').val('');
                $('#komentarInput').val('');
                $('#submitValidation').prop('disabled', true);

                // Load dokumen info
                $.ajax({
                    url: "{{ route('validasi.getDokumenInfo') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        no_kriteria: kriteriaId,
                        kategori: kategori,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            var data = response.data;
                            var rowHtml = '<tr>' +
                                '<td>' + data.no_kriteria + '</td>' +
                                '<td>' + data.judul + '</td>' +
                                '<td>' + data.versi + '</td>' +
                                '<td><span class="badge p-2 ' + getStatusBadgeClass(data.status) +
                                '">' +
                                data.status.toUpperCase() + '</span></td>' +
                                '<td>' + (data.updated_at || '-') + '</td>' +
                                '</tr>';
                            $('#dokumenInfoBody').html(rowHtml);
                        } else {
                            $('#dokumenInfoBody').html(
                                '<tr><td colspan="5" class="text-center text-muted">Informasi dokumen tidak ditemukan</td></tr>'
                            );
                        }
                    },
                    error: function() {
                        $('#dokumenInfoBody').html(
                            '<tr><td colspan="5" class="text-center text-danger">Gagal memuat informasi dokumen</td></tr>'
                        );
                    }
                });

                // Load PDF and validation data
                $.ajax({
                    url: "{{ route('validasi.showFile') }}",
                    dataType: "json",
                    type: "POST",
                    data: {
                        kriteria: kriteriaId,
                        kategori: kategori,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.pdfUrl) {
                            $('#pdfPreview').attr('src', response.pdfUrl);
                            if (response.status) {
                                $('#statusSelect').val(response.status);
                            }
                            if (response.komentar) {
                                $('#komentarInput').val(response.komentar);
                            }
                            $('#submitValidation').prop('disabled', false);
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Dokumen PDF tidak ditemukan.'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Gagal memuat dokumen PDF.'
                        });
                    }
                });
            }

            function getStatusBadgeClass(status) {
                switch (status.toLowerCase()) {
                    case 'tervalidasi':
                        return 'badge-success';
                    case 'revisi':
                        return 'badge-warning';
                    case 'perlu validasi':
                        return 'badge-info';
                    default:
                        return 'badge-secondary';
                }
            }

            $('#kriteriaDropdown').on('change', function() {
                selectedKriteria = $(this).val();
                selectedKategori = $('#kategoriDropdown').val();
                if (selectedKriteria && selectedKategori) {
                    loadPdfAndData(selectedKriteria, selectedKategori);
                } else {
                    $('#dokumenInfoBody').html(
                        '<tr><td colspan="5" class="text-center text-muted">Silakan pilih kriteria dan kategori terlebih dahulu</td></tr>'
                    );
                    $('#pdfPreview').attr('src', '');
                    $('#statusSelect').val('');
                    $('#komentarInput').val('');
                    $('#submitValidation').prop('disabled', true);
                }
            });

            $('#kategoriDropdown').on('change', function() {
                selectedKategori = $(this).val();
                selectedKriteria = $('#kriteriaDropdown').val();
                if (selectedKriteria && selectedKategori) {
                    loadPdfAndData(selectedKriteria, selectedKategori);
                } else {
                    $('#dokumenInfoBody').html(
                        '<tr><td colspan="5" class="text-center text-muted">Silakan pilih kriteria dan kategori terlebih dahulu</td></tr>'
                    );
                    $('#pdfPreview').attr('src', '');
                    $('#statusSelect').val('');
                    $('#komentarInput').val('');
                    $('#submitValidation').prop('disabled', true);
                }
            });

            $('#submitValidation').on('click', function() {
                if (!selectedKriteria || !selectedKategori) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih kriteria dan kategori terlebih dahulu.'
                    });
                    return;
                }
                var status = $('#statusSelect').val();
                var komentar = $('#komentarInput').val();

                if (!status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Pilih status validasi.'
                    });
                    return;
                }

                if (status === 'revisi' && !komentar) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Komentar validasi harus diisi ketika status validasi adalah Revisi.'
                    });
                    return;
                }

                $.ajax({
                    url: "{{ route('validasi.store') }}",
                    type: "PUT",
                    data: {
                        kriteria: selectedKriteria,
                        kategori: selectedKategori,
                        status: status,
                        komentar: komentar,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Sukses',
                                text: 'Validasi berhasil disimpan.'
                            });
                            // Refresh informasi dokumen setelah validasi
                            loadPdfAndData(selectedKriteria, selectedKategori);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Gagal menyimpan validasi: ' + (response
                                    .message || '')
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat menyimpan validasi.'
                        });
                    }
                });
            });
        });
    </script>
@endpush
