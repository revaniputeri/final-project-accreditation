@extends('layouts.app')

@section('title', 'HKI')
@section('subtitle', 'HKI')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">HKI</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- HKI -->
        <div class="callout callout-primary shadow-sm">
            <h5>HKI</h5>
            <p>Penelitian dalam bidang infokom yang mendapatkan pengakuan dalam bentuk Hak Kekayaan Intelektual (HKI).</p>
        </div>

        {{-- DataTable --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Daftar HKI</h3>
                    <div class="card-tools">
                        <a id="exportPdfBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.hki.export_pdf') }}">
                            <i class="fa-solid fa-file-pdf me-2"></i> Export PDF
                        </a>
                        <a id="exportExcelBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.hki.export_excel') }}">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        @if ($isAdm || $isDos)
                            <button class="btn btn-custom-blue me-2"
                                onclick="modalAction('{{ route('portofolio.hki.import') }}')">
                                <i class="fa-solid fa-file-arrow-up me-2"></i> Import Data
                            </button>
                            <button onclick="modalAction('{{ route('portofolio.hki.create_ajax') }}')"
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
                        'id' => 'p_hki-table',
                        'class' => 'table table-hover table-bordered table-striped',
                        'style' => 'width:100%',
                    ]) }}
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

        @if ($isAdm || $isAng)
            <!-- HKI Charts -->
            <div class="callout callout-primary shadow-sm">
                <h5>Chart</h5>
                <p>Chart berikut menampilkan distribusi jenis skema HKI, keterlibatan mahasiswa S2, dan tren HKI per
                    tahun.</p>
            </div>

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Distribusi Jenis Skema HKI</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <canvas id="pieChartSkemaHKI"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Keterlibatan Mahasiswa S2</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <canvas id="barChartMahasiswaS2HKI"></canvas>
                                <div id="mahasiswaS2LegendHKI" class="mt-3"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Tren HKI Per Tahun</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <canvas id="lineChartTrenHKI"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    {!! $dataTable->scripts() !!}

    {{-- Modal --}}
    <script>
        function modalAction(url) {
            $.get(url)
                .done(function(response) {
                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');

                    $(document).off('submit', '#formCreateHKI, #formEditHKI');

                    $(document).on('submit', '#formCreateHKI, #formEditHKI', function(e) {
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
                            success: function(res) {
                                $('#myModal').modal('hide');
                                window.LaravelDataTables["p_hki-table"].ajax.reload();
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
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                                    .msgField) {
                                    // Validation error
                                    var errors = xhr.responseJSON.msgField;
                                    $.each(errors, function(field, messages) {
                                        var input = form.find('[name="' + field + '"]');
                                        input.addClass('is-invalid');
                                        input.next('.invalid-feedback').text(messages[0]);
                                    });
                                } else {
                                    $('#myModal').modal('hide');
                                    window.LaravelDataTables["p_hki-table"].ajax.reload();
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
                    $(document).on('submit', '#form-import', function(e) {
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
                            success: function(response) {
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
                                        window.LaravelDataTables["p_hki-table"].ajax
                                            .reload();
                                    });
                                }
                            },
                            error: function(xhr) {
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
                            complete: function() {
                                submitBtn.prop('disabled', false).html(
                                    '<i class="fas fa-upload me-2"></i> Upload');
                            }
                        });
                    });
                })
                .fail(function(xhr) {
                    Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                });
        }

        $(document).on('submit', '#formDeleteSertifikasi', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#myModal').modal('hide');
                    window.LaravelDataTables["p_hki-table"].ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data sertifikasi berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat menghapus data sertifikasi.'
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#filterStatus, #filterSumberData').change(function() {
                window.LaravelDataTables["p_hki-table"].draw();
            });
        });

        // Kirim parameter filter ke server saat DataTable ajax request
        $('#p_hki-table').on('preXhr.dt', function(e, settings, data) {
            data.filter_status = $('#filterStatus').val();
            data.filter_sumber = $('#filterSumberData').val();
        });

        // Update export PDF link with current filter values
        function updateExportPdfLink() {
            var status = $('#filterStatus').val();
            var sumber = $('#filterSumberData').val();
            var url = new URL("{{ route('portofolio.hki.export_pdf') }}", window.location.origin);
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
            var url = new URL("{{ route('portofolio.hki.export_excel') }}", window.location.origin);
            if (status) {
                url.searchParams.set('filter_status', status);
            }
            if (sumber) {
                url.searchParams.set('filter_sumber', sumber);
            }
            $('#exportExcelBtn').attr('href', url.toString());
        }

        $(document).ready(function() {
            updateExportPdfLink();
            updateExportExcelLink();
            $('#filterStatus, #filterSumberData').change(function() {
                updateExportPdfLink();
                updateExportExcelLink();
            });
        });
    </script>

    {{-- Chart.js --}}
    <script>
        // Prepare chart data from PHP variables
        const skemaLabels = @json($skemaLabels);
        const skemaData = @json($skemaData);
        const mahasiswaS2Labels = @json($mahasiswaS2Labels);
        const mahasiswaS2Data = @json($mahasiswaS2Data);
        const trenLabels = @json($trenLabels);
        const trenData = @json($trenData);

        const chartColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#76A346', '#D9534F', '#5BC0DE'
        ];

        // Pie Chart - Distribusi Jenis Skema HKI
        const ctxPieSkemaHKI = document.getElementById('pieChartSkemaHKI').getContext('2d');
        const pieChartSkemaHKI = new Chart(ctxPieSkemaHKI, {
            type: 'pie',
            data: {
                labels: skemaLabels,
                datasets: [{
                    data: skemaData,
                    backgroundColor: chartColors,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: false,
                    }
                }
            }
        });

        // Bar Chart - Keterlibatan Mahasiswa S2
        const ctxBarMahasiswaS2HKI = document.getElementById('barChartMahasiswaS2HKI').getContext('2d');
        const totalMahasiswaS2HKI = mahasiswaS2Data.reduce((a, b) => a + b, 0);
        const mahasiswaS2PercentagesHKI = mahasiswaS2Data.map(value => ((value / totalMahasiswaS2HKI) * 100).toFixed(1));

        const barChartMahasiswaS2HKI = new Chart(ctxBarMahasiswaS2HKI, {
            type: 'bar',
            data: {
                labels: mahasiswaS2Labels,
                datasets: [{
                    label: 'Jumlah',
                    data: mahasiswaS2Data,
                    backgroundColor: chartColors,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0,
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y || 0;
                                const percentage = mahasiswaS2PercentagesHKI[context.dataIndex] || 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    },
                    title: {
                        display: false,
                    }
                }
            }
        });

        // Custom legend for Keterlibatan Mahasiswa S2 with counts and percentages
        const mahasiswaS2LegendHKIContainer = document.getElementById('mahasiswaS2LegendHKI');
        if (mahasiswaS2LegendHKIContainer) {
            mahasiswaS2Labels.forEach((label, index) => {
                const color = chartColors[index % chartColors.length];
                const count = mahasiswaS2Data[index];
                const percentage = mahasiswaS2PercentagesHKI[index];
                const legendItem = document.createElement('div');
                legendItem.innerHTML = `<span style="display:inline-block;width:12px;height:12px;background-color:${color};margin-right:8px;"></span>${label}: ${count} (${percentage}%)`;
                mahasiswaS2LegendHKIContainer.appendChild(legendItem);
            });
        }

        // Line Chart - Tren HKI Per Tahun
        const ctxLineTrenHKI = document.getElementById('lineChartTrenHKI').getContext('2d');
        const lineChartTrenHKI = new Chart(ctxLineTrenHKI, {
            type: 'line',
            data: {
                labels: trenLabels,
                datasets: [{
                    label: 'Jumlah HKI',
                    data: trenData,
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    fill: true,
                    tension: 0.3,
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        precision: 0,
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    title: {
                        display: false,
                    }
                }
            }
        });
    </script>
@endpush
