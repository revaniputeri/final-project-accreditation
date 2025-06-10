@extends('layouts.app')

@section('title', 'Organisasi')
@section('subtitle', 'Organisasi')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Organisasi</li>
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
                    <h3 class="card-title mb-0 text-white">Daftar Organisasi</h3>
                    <div class="card-tools">
                        <a id="exportPdfBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.organisasi.export_pdf') }}">
                            <i class="fa-solid fa-file-pdf me-2"></i> Export PDF
                        </a>
                        <a id="exportExcelBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.organisasi.export_excel') }}">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        @if ($isAdm || $isDos)
                            <button class="btn btn-custom-blue me-2"
                                onclick="modalAction('{{ route('portofolio.organisasi.import') }}')">
                                <i class="fa-solid fa-file-arrow-up me-2"></i> Import Data
                            </button>
                            <button onclick="modalAction('{{ route('portofolio.organisasi.create_ajax') }}')"
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
        'id' => 'p_organisasi-table',
        'class' => 'table table-hover table-bordered table-striped',
        'style' => 'width:100%',
    ]) }}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Jumlah Partisipasi Berdasarkan skala</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart">
                            <canvas id="chartOrganisasi1"
                                style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Status Validasi</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="chartOrganisasi2"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        <div class="col-md-6">
                <div class="card card-danger">
                    <div class="card-header">
                        <h3 class="card-title">Partisipasi Dosen berdasarkan Jabatan</h3>

                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="chartOrganisasi3"
                            style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>

        {{-- Modal --}}
        <div id="myModal" class="modal fade" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <!-- Konten modal akan diisi secara dinamis -->
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
                .done(function (response) {
                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');

                    $(document).off('submit', '#formCreateOrganisasi, #formEditOrganisasi');

                    $(document).on('submit', '#formCreateOrganisasi, #formEditOrganisasi', function (e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(form[0]);
                        // Always use POST method for AJAX to handle file uploads and method spoofing
                        var method = 'POST';
                        // Append _method field if present in the form
                        var methodInput = form.find('input[name="_method"]');
                        if (methodInput.length) {
                            formData.append('_method', methodInput.val());
                        }
                        $.ajax({
                            url: form.attr('action'),
                            method: method,
                            data: formData,
                            processData: false,
                            contentType: false,
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (res) {
                                $('#myModal').modal('hide');
                                window.LaravelDataTables["p_organisasi-table"].ajax.reload();
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
                            error: function (xhr) {
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                                    .msgField) {
                                    // Validation error
                                    var errors = xhr.responseJSON.msgField;
                                    $.each(errors, function (field, messages) {
                                        var input = form.find('[name="' + field + '"]');
                                        input.addClass('is-invalid');
                                        input.next('.invalid-feedback').text(messages[0]);
                                    });
                                } else {
                                    $('#myModal').modal('hide');
                                    window.LaravelDataTables["p_organisasi-table"].ajax.reload();
                                    if (xhr.responseJSON && xhr.responseJSON.alert && xhr
                                        .responseJSON.message) {
                                        Swal.fire({
                                            icon: xhr.responseJSON.alert,
                                            title: xhr.responseJSON.alert === 'success' ?
                                                'Sukses' : 'Error',
                                            text: xhr.responseJSON.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        Swal.fire('Error!', 'Gagal menyimpan data.', 'error');
                                    }
                                }
                            }
                        });
                    });

                    $(document).off('submit', '#form-import');

                    // Handle import form submit
                    $(document).on('submit', '#form-import', function (e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(form[0]);
                        var submitBtn = form.find('button[type="submit"]');

                        submitBtn.prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...');

                        $.ajax({
                            url: form.attr('action'),
                            method: 'POST',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function (response) {
                                $('#myModal').modal('hide');
                                if (response.alert && response.message) {
                                    Swal.fire({
                                        icon: response.alert,
                                        title: response.alert === 'success' ?
                                            'Sukses' : 'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.LaravelDataTables["p_organisasi-table"].ajax
                                            .reload();
                                    });
                                }
                            },
                            error: function (xhr) {
                                $('#myModal').modal('hide');
                                if (xhr.responseJSON && xhr.responseJSON.alert && xhr.responseJSON
                                    .message) {
                                    Swal.fire({
                                        icon: xhr.responseJSON.alert,
                                        title: xhr.responseJSON.alert === 'success' ?
                                            'Sukses' : 'Error',
                                        text: xhr.responseJSON.message,
                                        showConfirmButton: true
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: xhr.responseJSON.message
                                    });
                                }
                            },
                            complete: function () {
                                submitBtn.prop('disabled', false).html(
                                    '<i class="fas fa-upload me-2"></i> Upload');
                            }
                        });
                    });
                })
                .fail(function (xhr) {
                    Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                });
        }

        $(document).on('submit', '#formDeleteOrganisasi', function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function (response) {
                    $('#myModal').modal('hide');
                    window.LaravelDataTables["p_organisasi-table"].ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data organisasi berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat menghapus data organisasi.'
                    });
                }
            });
        });

        $(document).ready(function () {
            $('#filterStatus, #filterSumberData').change(function () {
                window.LaravelDataTables["p_organisasi-table"].draw();
            });
        });

        // Kirim parameter filter ke server saat DataTable ajax request
        $('#p_organisasi-table').on('preXhr.dt', function (e, settings, data) {
            data.filter_status = $('#filterStatus').val();
            data.filter_sumber = $('#filterSumberData').val();
        });

        // Update export PDF link with current filter values
        function updateExportPdfLink() {
            var status = $('#filterStatus').val();
            var sumber = $('#filterSumberData').val();
            var url = new URL("{{ route('portofolio.organisasi.export_pdf') }}", window.location.origin);
            if (status) {
                url.searchParams.set('filter_status', status);
            }
            if (sumber) {
                url.searchParams.set('filter_sumber', sumber);
            }
            $('#exportPdfBtn').attr('href', url.toString());
        }

        // Update export Excel link with current filter values
        function updateExportExcelLink() {
            var status = $('#filterStatus').val();
            var sumber = $('#filterSumberData').val();
            var url = new URL("{{ route('portofolio.organisasi.export_excel') }}", window.location.origin);
            if (status) {
                url.searchParams.set('filter_status', status);
            }
            if (sumber) {
                url.searchParams.set('filter_sumber', sumber);
            }
            $('#exportExcelBtn').attr('href', url.toString());
        }

        $(document).ready(function () {
            updateExportPdfLink();
            updateExportExcelLink();
            $('#filterStatus, #filterSumberData').change(function () {
                updateExportPdfLink();
                updateExportExcelLink();
            });
        });
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('portofolio.organisasi.chart1') }}",
                method: 'GET',
                success: function (response) {
                    const tingkat = [];
                    const jumlah = [];
                    console.log(response.data);
                    response.data.forEach(item => {
                        tingkat.push(item.tingkat);
                        jumlah.push(item.jumlah);
                    });

                    const ctx = document.getElementById('chartOrganisasi1').getContext('2d');

                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: tingkat,
                            datasets: [{
                                label: tingkat,
                                data: jumlah,
                                backgroundColor: ['bronze', 'silver'],
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        });
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('portofolio.organisasi.chart2') }}",
                method: 'GET',
                success: function (response) {
                    const status = [];
                    const jumlah = [];

                    response.data.forEach(item => {
                        status.push(item.status);
                        jumlah.push(item.jumlah);
                    });

                    const ctx = document.getElementById('chartOrganisasi2').getContext('2d');

                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: status,
                            datasets: [{
                                data: jumlah,
                                backgroundColor: ['green', '#f39c12', 'red'],
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                        }
                    });
                }
            });
        });
        $(document).ready(function () {
            $.ajax({
                url: "{{ route('portofolio.organisasi.chart3') }}",
                method: 'GET',
                success: function (response) {
                    const jabatan = [];
                    const jumlah = [];

                    response.data.forEach(item => {
                        jabatan.push(item.jabatan_fungsional);
                        jumlah.push(item.jumlah);
                    });

                    const ctx = document.getElementById('chartOrganisasi3').getContext('2d');

                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: jabatan,
                            datasets: [{
                                data: jumlah,
                                backgroundColor: ['green', '#f39c12', 'red','blue'],
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                        }
                    });
                }
            });
        });
    </script>
@endpush