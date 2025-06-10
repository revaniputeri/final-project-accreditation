@extends('layouts.app')

@section('title', 'Pengabdian')
@section('subtitle', 'Pengabdian')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Pengabdian Masyarakat</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- Pengabdian Masyarakat -->
        <div class="callout callout-primary shadow-sm">
            <h5>Pengabdian Masyarakat</h5>
            <p>Kegiatan pelayanan kepada masyarakat sesuai bidang keilmuan Program Studi dalam tiga tahun terakhir.</p>
        </div>

        {{-- DataTable --}}
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Daftar Pengabdian Masyarakat</h3>
                    <div class="card-tools">
                        <a id="exportPdfBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.pengabdian.export_pdf') }}">
                            <i class="fa-solid fa-file-pdf me-2"></i> Export PDF
                        </a>
                        <a id="exportExcelBtn" class="btn btn-custom-blue me-2"
                            href="{{ route('portofolio.pengabdian.export_excel') }}">
                            <i class="fas fa-file-excel me-2"></i> Export Excel
                        </a>
                        @if ($isAdm || $isDos)
                            <button class="btn btn-custom-blue me-2"
                                onclick="modalAction('{{ route('portofolio.pengabdian.import') }}')">
                                <i class="fa-solid fa-file-arrow-up me-2"></i> Import Data
                            </button>
                            <button onclick="modalAction('{{ route('portofolio.pengabdian.create_ajax') }}')"
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
                        'id' => 'p_pengabdian-table',
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
            <!-- Pengabdian Charts -->
            <div class="callout callout-primary shadow-sm">
                <h5>Chart</h5>
                <p>Chart berikut menampilkan distribusi skema pengabdian, tren pengabdian masyarakat per tahun, peran dosen
                    dalam pengabdian, dan keterlibatan mahasiswa S2.</p>
            </div>

            <div class="container-fluid mt-3">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Distribusi Skema Pengabdian</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <canvas id="pieChartSkema"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Tren Pengabdian Masyarakat Per Tahun</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <canvas id="lineChartTren"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mt-3">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary border-bottom">
                                <h5 class="card-title mb-0 text-white">Peran Dosen dalam Pengabdian</h5>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body collapse">
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <canvas id="doughnutChartPeran"></canvas>
                                        <div id="peranLegend" class="mt-3"></div>
                                    </div>
                                </div>
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
                                <div class="row mb-4">
                                    <div class="col-md-12">
                                        <canvas id="barChartMahasiswaS2"></canvas>
                                        <div id="mahasiswaS2LegendPengabdian" class="mt-3"></div>
                                    </div>
                                </div>
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

                    $(document).off('submit', '#formCreatePengabdian, #formEditPengabdian');

                    $(document).on('submit', '#formCreatePengabdian, #formEditPengabdian', function(e) {
                        e.preventDefault();
                        var form = $(this);
                        var formData = new FormData(form[0]);
                        var method = 'POST';
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
                                window.LaravelDataTables["p_pengabdian-table"].ajax.reload();
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
                                    window.LaravelDataTables["p_pengabdian-table"].ajax.reload();
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
                                        title: response.alert === 'success' ? 'Sukses' :
                                            'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.LaravelDataTables["p_pengabdian-table"].ajax
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

        $(document).on('submit', '#formDeletePengabdian', function(e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    $('#myModal').modal('hide');
                    window.LaravelDataTables["p_pengabdian-table"].ajax.reload();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Data pengabdian berhasil dihapus.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Tidak dapat menghapus data pengabdian.'
                    });
                }
            });
        });

        $(document).ready(function() {
            $('#filterStatus, #filterSumberData').change(function() {
                window.LaravelDataTables["p_pengabdian-table"].draw();
            });
        });

        $('#p_pengabdian-table').on('preXhr.dt', function(e, settings, data) {
            data.filter_status = $('#filterStatus').val();
            data.filter_sumber = $('#filterSumberData').val();
        });

        function updateExportPdfLink() {
            var status = $('#filterStatus').val();
            var sumber = $('#filterSumberData').val();
            var url = new URL("{{ route('portofolio.pengabdian.export_pdf') }}", window.location.origin);
            if (status) {
                url.searchParams.set('filter_status', status);
            }
            if (sumber) {
                url.searchParams.set('filter_sumber', sumber);
            }
            $('#exportPdfBtn').attr('href', url.toString());
        }

        function updateExportExcelLink() {
            var status = $('#filterStatus').val();
            var sumber = $('#filterSumberData').val();
            var url = new URL("{{ route('portofolio.pengabdian.export_excel') }}", window.location.origin);
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
        const trenLabels = @json($trenLabels);
        const trenData = @json($trenData);
        const peranLabels = @json($peranLabels);
        const peranData = @json($peranData);
        const mahasiswaS2Labels = @json($mahasiswaS2Labels);
        const mahasiswaS2Data = @json($mahasiswaS2Data);

        const chartColors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#E7E9ED', '#76A346', '#D9534F', '#5BC0DE'
        ];

        // Pie Chart - Distribusi Skema Pengabdian
        const ctxPieSkema = document.getElementById('pieChartSkema').getContext('2d');
        const pieChartSkema = new Chart(ctxPieSkema, {
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

        // Line Chart - Tren Pengabdian Masyarakat Per Tahun
        const ctxLineTren = document.getElementById('lineChartTren').getContext('2d');
        const lineChartTren = new Chart(ctxLineTren, {
            type: 'line',
            data: {
                labels: trenLabels,
                datasets: [{
                    label: 'Jumlah Pengabdian',
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

        // Doughnut Chart - Peran Dosen dalam Pengabdian (with percentage and count)
        const ctxDoughnutPeran = document.getElementById('doughnutChartPeran').getContext('2d');
        const totalPeran = peranData.reduce((a, b) => a + b, 0);
        const peranPercentages = peranData.map(value => ((value / totalPeran) * 100).toFixed(1));

        const doughnutChartPeran = new Chart(ctxDoughnutPeran, {
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
                                const percentage = peranPercentages[context.dataIndex] || 0;
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
        const peranLegendContainer = document.getElementById('peranLegend');
        peranLabels.forEach((label, index) => {
            const color = chartColors[index % chartColors.length];
            const count = peranData[index];
            const percentage = peranPercentages[index];
            const legendItem = document.createElement('div');
            legendItem.innerHTML =
                `<span style="display:inline-block;width:12px;height:12px;background-color:${color};margin-right:8px;"></span>${label.charAt(0).toUpperCase() + label.slice(1)}: ${count} (${percentage}%)`;
            peranLegendContainer.appendChild(legendItem);
        });

        // Bar Chart - Keterlibatan Mahasiswa S2
        const ctxBarMahasiswaS2 = document.getElementById('barChartMahasiswaS2').getContext('2d');
        const totalMahasiswaS2Pengabdian = mahasiswaS2Data.reduce((a, b) => a + b, 0);
        const mahasiswaS2PercentagesPengabdian = mahasiswaS2Data.map(value => ((value / totalMahasiswaS2Pengabdian) * 100)
            .toFixed(1));

        const barChartMahasiswaS2Pengabdian = new Chart(ctxBarMahasiswaS2, {
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
                                const percentage = mahasiswaS2PercentagesPengabdian[context.dataIndex] || 0;
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
        const mahasiswaS2LegendContainer = document.getElementById('mahasiswaS2LegendPengabdian');
        mahasiswaS2Labels.forEach((label, index) => {
            const color = chartColors[index % chartColors.length];
            const count = mahasiswaS2Data[index];
            const percentage = mahasiswaS2PercentagesPengabdian[index];
            const legendItem = document.createElement('div');
            legendItem.innerHTML =
                `<span style="display:inline-block;width:12px;height:12px;background-color:${color};margin-right:8px;"></span>${label}: ${count} (${percentage}%)`;
            mahasiswaS2LegendContainer.appendChild(legendItem);
        });
    </script>
@endpush
