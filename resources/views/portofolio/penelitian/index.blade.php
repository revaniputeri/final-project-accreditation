@extends('layouts.app')

@section('title', 'Penelitian')
@section('subtitle', 'Penelitian')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Penelitian</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        {{-- DataTable --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Daftar Penelitian</h3>
                    <div class="card-tools">
                        <a id="exportPdfBtn" class="btn btn-custom-blue me-2" href="{{ route('portofolio.penelitian.export_pdf') }}">
                            <i class="fa-solid fa-file-pdf me-2"></i> Export PDF
                        </a>
                        <a id="exportExcelBtn" class="btn btn-custom-blue me-2" href="{{ route('portofolio.penelitian.export_excel') }}">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        @if ($isAdm || $isDos)
                            <button class="btn btn-custom-blue me-2"
                                onclick="modalAction('{{ route('portofolio.penelitian.import') }}')">
                                <i class="fa-solid fa-file-arrow-up me-2"></i> Import Data
                            </button>
                            <button onclick="modalAction('{{ route('portofolio.penelitian.create_ajax') }}')"
                                class="btn btn-custom-blue me-2">
                                <i class="fas fa-plus me-2"></i> Tambah Data
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-6 form-group">
                        <label for="filterSumberData">Filter Sumber Data:</label>
                        <select id="filterSumberData" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Filter Sumber Data --</option>
                            <option value="p3m">P3M</option>
                            <option value="dosen">Dosen</option>
                        </select>
                    </div>
                    <div class="col-6 form-group">
                        <label for="filterStatus">Filter Status:</label>
                        <select id="filterStatus" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Filter Status --</option>
                            <option value="tervalidasi">Tervalidasi</option>
                            <option value="perlu validasi">Perlu Validasi</option>
                            <option value="tidak valid">Tidak Valid</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    {{ $dataTable->table([
                        'id' => 'p_penelitian-table',
                        'class' => 'table table-hover table-bordered table-striped',
                        'style' => 'width:100%',
                    ]) }}
                </div>
            </div>
        </div>

        <div id="myModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}
    <script>
        function modalAction(url) {
            $.get(url)
                .done(function(response) {
                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');

                    $(document).off('submit', '#formCreatePenelitian, #formEditPenelitian');
                    $(document).on('submit', '#formCreatePenelitian, #formEditPenelitian', function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(form[0]);
                        var method = form.find('input[name="_method"]').val() || 'POST';

                        $.ajax({
                            url: form.attr('action'),
                            method: method,
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(res) {
                                $('#myModal').modal('hide');
                                window.LaravelDataTables["p_penelitian-table"].ajax.reload();
                                if (res.alert && res.message) {
                                    Swal.fire({
                                        icon: res.alert,
                                        title: res.alert === 'success' ? 'Sukses' : 'Error',
                                        text: res.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                }
                            },
                            error: function(xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.msgField) {
                                    var errors = xhr.responseJSON.msgField;
                                    $.each(errors, function(field, messages) {
                                        var input = form.find('[name="' + field + '"]');
                                        input.addClass('is-invalid');
                                        input.next('.invalid-feedback').text(messages[0]);
                                    });
                                } else {
                                    $('#myModal').modal('hide');
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON?.message || 'Gagal menyimpan data'
                                    });
                                }
                            }
                        });
                    });

                    $(document).off('submit', '#form-import-penelitian');
                    $(document).on('submit', '#form-import-penelitian', function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(form[0]);
                        var submitBtn = form.find('button[type="submit"]');

                        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                $('#myModal').modal('hide');
                                Swal.fire({
                                    icon: response.alert,
                                    title: response.alert === 'success' ? 'Sukses' : 'Error',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.LaravelDataTables["p_penelitian-table"].ajax.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message || 'Gagal mengimpor data'
                                });
                            },
                            complete: function() {
                                submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-2"></i> Upload');
                            }
                        });
                    });
                })
                .fail(function(xhr) {
                    Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                });
        }

        $(document).on('submit', '#formDeletePenelitian', function(e) {
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
                        text: 'Data penelitian berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat menghapus data penelitian.'
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#filterStatus, #filterSumberData, #filterTahun').change(function() {
                window.LaravelDataTables["p_penelitian-table"].draw();
            });

            function updateExportLinks() {
                var status = $('#filterStatus').val();
                var sumber = $('#filterSumberData').val();
                var tahun = $('#filterTahun').val();

                var pdfUrl = new URL("{{ route('portofolio.penelitian.export_pdf') }}");
                if (status) pdfUrl.searchParams.set('filter_status', status);
                if (sumber) pdfUrl.searchParams.set('filter_sumberdata', sumber);
                if (tahun) pdfUrl.searchParams.set('filter_tahun', tahun);
                $('#exportPdfBtn').attr('href', pdfUrl.toString());

                var excelUrl = new URL("{{ route('portofolio.penelitian.export_excel') }}");
                if (status) excelUrl.searchParams.set('filter_status', status);
                if (sumber) excelUrl.searchParams.set('filter_sumberdata', sumber);
                if (tahun) excelUrl.searchParams.set('filter_tahun', tahun);
                $('#exportExcelBtn').attr('href', excelUrl.toString());
            }

            updateExportLinks();
            $('#filterStatus, #filterSumberData, #filterTahun').change(updateExportLinks);
        });

        $('#p_penelitian-table').on('preXhr.dt', function(e, settings, data) {
            data.filter_status = $('#filterStatus').val();
            data.filter_sumber = $('#filterSumberData').val();
            data.filter_tahun = $('#filterTahun').val();
        });
    </script>
@endpush
