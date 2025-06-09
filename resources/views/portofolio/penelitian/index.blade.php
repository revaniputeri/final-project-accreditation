@extends('layouts.app')

@section('title', 'Penelitian')
@section('subtitle', 'Penelitian')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Penelitian</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Penelitian -->
        <div class="callout callout-primary shadow-sm">
            <h5>Penelitian</h5>
            <p>Penelitian yang dilakukan dosen tetap sesuai bidang keahlian Program Studi dalam tiga tahun terakhir.</p>
        </div>

        {{-- DataTable --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Daftar Penelitian</h3>
                    <div class="card-tools">
                        <a id="exportPdfBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.penelitian.export_pdf') }}">
                            <i class="fa-solid fa-file-pdf me-2"></i> Export PDF
                        </a>
                        <a id="exportExcelBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.penelitian.export_excel') }}">
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

    @if ($isAdm || $isAng)
        <!-- Penelitian Charts -->
        <div class="callout callout-primary shadow-sm">
            <h5>Chart</h5>
            <p>Chart berikut menampilkan distribusi skema penelitian, tren penelitian per tahun, peran dosen dalam
                penelitian, keterlibatan mahasiswa S2, dan tren dana untuk penelitian per tahun per skema.</p>
        </div>

        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary border-bottom">
                            <h5 class="card-title mb-0 text-white">Distribusi Skema Penelitian</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body collapse">
                            <canvas id="pieChartSkemaPenelitian"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary border-bottom">
                            <h5 class="card-title mb-0 text-white">Tren Penelitian Per Tahun</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body collapse">
                            <canvas id="lineChartTrenPenelitian"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary border-bottom">
                            <h5 class="card-title mb-0 text-white">Peran Dosen dalam Penelitian</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body collapse">
                            <canvas id="doughnutChartPeranPenelitian"></canvas>
                            <div id="peranLegendPenelitian" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mt-3">
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
                            <canvas id="barChartMahasiswaS2Penelitian"></canvas>
                            <div id="mahasiswaS2LegendPenelitian" class="mt-3"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mt-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary border-bottom">
                            <h5 class="card-title mb-0 text-white">Tren Dana Penelitian Per Tahun Per Skema</h5>
                            <div class="card-tools">
                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card-body collapse">
                            <canvas id="multiLineChartDanaPenelitian"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
                                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON
                                    .msgField) {
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
                                        text: xhr.responseJSON?.message ||
                                            'Gagal menyimpan data'
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
                                Swal.fire({
                                    icon: response.alert,
                                    title: response.alert === 'success' ? 'Sukses' :
                                        'Error',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.LaravelDataTables["p_penelitian-table"].ajax
                                        .reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: xhr.responseJSON?.message ||
                                        'Gagal mengimpor data'
                                });
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

    {{-- Chart.js --}}
    <script>
        // Prepare chart data from PHP variables
        const skemaLabels = @json($skemaLabels);
        const skemaData = @json($skemaData);
        const trenLabels = @json($trenLabels);
        const trenData = @json($trenData);
        const peranLabels = @json($peranLabels);
        const peranData = @json($peranData);
        const mahasiswaS2Labels = @json($mahasiswaS2Labels);
        const mahasiswaS2Data = @json($mahasiswaS2Data);
        const multiLineDataSets = @json($multiLineDataSets);

        const chartColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#76A346', '#D9534F', '#5BC0DE'
        ];

        // Pie Chart - Distribusi Skema Penelitian
        const ctxPieSkemaPenelitian = document.getElementById('pieChartSkemaPenelitian').getContext('2d');
        const pieChartSkemaPenelitian = new Chart(ctxPieSkemaPenelitian, {
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

        // Line Chart - Tren Penelitian Per Tahun
        const ctxLineTrenPenelitian = document.getElementById('lineChartTrenPenelitian').getContext('2d');
        const lineChartTrenPenelitian = new Chart(ctxLineTrenPenelitian, {
            type: 'line',
            data: {
                labels: trenLabels,
                datasets: [{
                    label: 'Jumlah Penelitian',
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

        // Doughnut Chart - Peran Dosen dalam Penelitian (with percentage and count)
        const ctxDoughnutPeranPenelitian = document.getElementById('doughnutChartPeranPenelitian').getContext('2d');
        const totalPeranPenelitian = peranData.reduce((a, b) => a + b, 0);
        const peranPercentagesPenelitian = peranData.map(value => ((value / totalPeranPenelitian) * 100).toFixed(1));

        const doughnutChartPeranPenelitian = new Chart(ctxDoughnutPeranPenelitian, {
            type: 'doughnut',
            data: {
                labels: peranLabels,
                datasets: [{
                    data: peranData,
                    backgroundColor: chartColors,
                }]
            },
            options: {
                responsive: true,
                cutout: '50%',
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const percentage = peranPercentagesPenelitian[context.dataIndex] || 0;
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

        // Custom legend for Peran Dosen with counts and percentages
        const peranLegendPenelitianContainer = document.getElementById('peranLegendPenelitian');
        peranLabels.forEach((label, index) => {
            const color = chartColors[index % chartColors.length];
            const count = peranData[index];
            const percentage = peranPercentagesPenelitian[index];
            const legendItem = document.createElement('div');
            legendItem.innerHTML =
                `<span style="display:inline-block;width:12px;height:12px;background-color:${color};margin-right:8px;"></span>${label.charAt(0).toUpperCase() + label.slice(1)}: ${count} (${percentage}%)`;
            peranLegendPenelitianContainer.appendChild(legendItem);
        });

        // Bar Chart - Keterlibatan Mahasiswa S2 (with percentage and count)
        const ctxBarMahasiswaS2Penelitian = document.getElementById('barChartMahasiswaS2Penelitian').getContext('2d');
        const totalMahasiswaS2Penelitian = mahasiswaS2Data.reduce((a, b) => a + b, 0);
        const mahasiswaS2PercentagesPenelitian = mahasiswaS2Data.map(value => ((value / totalMahasiswaS2Penelitian) * 100)
            .toFixed(1));

        const barChartMahasiswaS2Penelitian = new Chart(ctxBarMahasiswaS2Penelitian, {
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
                                const percentage = mahasiswaS2PercentagesPenelitian[context.dataIndex] || 0;
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
        const mahasiswaS2LegendPenelitianContainer = document.getElementById('mahasiswaS2LegendPenelitian');
        mahasiswaS2Labels.forEach((label, index) => {
            const color = chartColors[index % chartColors.length];
            const count = mahasiswaS2Data[index];
            const percentage = mahasiswaS2PercentagesPenelitian[index];
            const legendItem = document.createElement('div');
            legendItem.innerHTML =
                `<span style="display:inline-block;width:12px;height:12px;background-color:${color};margin-right:8px;"></span>${label}: ${count} (${percentage}%)`;
            mahasiswaS2LegendPenelitianContainer.appendChild(legendItem);
        });

        // Multi-Line Chart - Tren Dana Penelitian Per Tahun Per Skema
        const ctxMultiLineDanaPenelitian = document.getElementById('multiLineChartDanaPenelitian').getContext('2d');

        // Assign colors to datasets
        multiLineDataSets.forEach((dataset, index) => {
            dataset.borderColor = chartColors[index % chartColors.length];
        });

        const multiLineChartDanaPenelitian = new Chart(ctxMultiLineDanaPenelitian, {
            type: 'line',
            data: {
                labels: trenLabels,
                datasets: multiLineDataSets
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                stacked: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Dana (Rp)'
                        },
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
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
