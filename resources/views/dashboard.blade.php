@extends('layouts.app')

@section('title', 'Welcome To Akresa')
@section('subtitle', 'Dashboard')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <style>
        .custom-box {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 0.5rem;
            padding: 15px;
            text-align: center;
        }

        .custom-box:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .small-box.bg-warning .small-box-footer {
            color: white !important;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .chart-section {
            width: 48%;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.1);
            border-radius: 0.25rem;
        }

        .card-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 0.25rem 0.25rem 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .chart-canvas {
            width: 100%;
            height: 300px;
            padding: 10px;
        }
    </style>

    <div class="container-fluid">
        <!-- Informasi Validasi -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white border-bottom">
                <h3 class="card-title mb-0">Informasi Validasi</h3>
            </div>
            <div class="card-body">
                <div class="row justify-content-center g-2">
                    <!-- Data Tervalidasi -->
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="small-box bg-info text-center custom-box">
                            <div class="inner">
                                <h1>{{ $data['totalTerValidasi'] ?? 0 }}</h1>
                                <p>Data Portofolio Tervalidasi</p>
                            </div>
                            <a href="{{ route('chart.moreInfo', ['status' => 'Tervalidasi']) }}" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Data Perlu Validasi -->
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="small-box bg-warning text-center custom-box">
                            <div class="inner text-white">
                                <h1>{{ $data['totalPerluValidasi'] ?? 0 }}</h1>
                                <p>Data Portofolio Perlu Validasi</p>
                            </div>
                            <a href="{{ route('chart.moreInfo', ['status' => 'Perlu Validasi']) }}"
                                class="small-box-footer text-white">
                                More info <i class="fas fa-arrow-circle-right text-white"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Data Tidak Valid -->
                    <div class="col-md-4 col-sm-6 mb-3">
                        <div class="small-box bg-danger text-center custom-box">
                            <div class="inner">
                                <h1>{{ $data['totalTidakValid'] ?? 0 }}</h1>
                                <p>Data Portofolio Tidak Valid</p>
                            </div>
                            <a href="{{ route('chart.moreInfo', ['status' => 'Tidak Valid']) }}" class="small-box-footer">
                                More info <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Chart Section -->
        <div class="chart-container" style="display: flex; justify-content: space-between; gap: 20px; width: 100%;">
            <div class="chart-section" style="width: 48%;">
                <div class="card-header bg-primary text-white border-bottom"
                    style="padding: 10px; border-radius: 0.25rem 0.25rem 0 0; display: flex; justify-content: flex-start; align-items: center; min-height: 40px;">
                    <h3 class="card-title mb-0"
                        style="font-size: 16px; font-weight: bold; white-space: nowrap; margin: 0; margin-right: auto;">
                        Status Validasi</h3>
                    <select id="pieChartFilter" class="form-control form-control-sm"
                        style="background-color: white; color: black; border: none; border-radius: 3px; padding: 5px 15px; font-size: 13.5px; height: 30px; width: 150px;">
                        <option value="all">Semua Kategori</option>
                        <option value="penelitian">Penelitian</option>
                        <option value="publikasi">Publikasi</option>
                        <option value="pengabdian">Pengabdian</option>
                        <option value="sertifikasi">Sertifikasi</option>
                        <option value="hki">HKI</option>
                        <option value="karya_buku">Karya Buku</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="organisasi">Organisasi</option>
                        <option value="prestasi">Prestasi</option>
                        <option value="profesi">Profesi</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="pieChart" class="chart-canvas" style="width: 100%; height: 300px;"></canvas>
                </div>
            </div>

            <!-- Bar Chart -->
            <div class="chart-section">
                <div class="card-header">
                    <h3 class="card-title mb-0">Detail Setiap Portofolio</h3>
                </div>
                <div class="card-body">
                    <canvas id="barChart" class="chart-canvas"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const chartData = @json($data['data'] ?? []);
        const totalTerValidasi = {{ intval($data['totalTerValidasi'] ?? 0) }};
        const totalPerluValidasi = {{ intval($data['totalPerluValidasi'] ?? 0) }};
        const totalTidakValid = {{ intval($data['totalTidakValid'] ?? 0) }};

        const ctxPie = document.getElementById('pieChart').getContext('2d');
        const ctxBar = document.getElementById('barChart').getContext('2d');

        const portofolios = ['penelitian', 'publikasi', 'pengabdian', 'sertifikasi', 'hki', 'karya_buku', 'kegiatan', 'organisasi', 'prestasi', 'profesi'];

        const pieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['Tervalidasi', 'Perlu Validasi', 'Tidak Valid'],
                datasets: [{
                    data: [totalTerValidasi, totalPerluValidasi, totalTidakValid],
                    backgroundColor: ['#26A69A', '#FFC107', '#EF5350'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 12 },
                            boxWidth: 12,
                            padding: 10
                        }
                    }
                }
            }
        });

        const barChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: portofolios.map(p => p.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())),
                datasets: [
                    {
                        label: 'Tervalidasi',
                        data: portofolios.map(p => chartData[p]?.Tervalidasi || 0),
                        backgroundColor: '#26A69A',
                    },
                    {
                        label: 'Perlu Validasi',
                        data: portofolios.map(p => chartData[p]?.["Perlu Validasi"] || 0),
                        backgroundColor: '#FFC107',
                    },
                    {
                        label: 'Tidak Valid',
                        data: portofolios.map(p => chartData[p]?.["Tidak Valid"] || 0),
                        backgroundColor: '#EF5350',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 12 },
                            boxWidth: 12,
                            padding: 10
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: { size: 10 },
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: { size: 10 },
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Filter pie chart
        const pieFilter = document.getElementById('pieChartFilter');
        pieFilter.addEventListener('change', () => {
            const val = pieFilter.value;
            let pieData = [0, 0, 0];
            if (val === 'all') {
                pieData = [totalTerValidasi, totalPerluValidasi, totalTidakValid];
            } else if (chartData[val]) {
                pieData = [
                    chartData[val]["Tervalidasi"] || 0,
                    chartData[val]["Perlu Validasi"] || 0,
                    chartData[val]["Tidak Valid"] || 0
                ];
            }
            pieChart.data.datasets[0].data = pieData;
            pieChart.update();
        });

    </script>
@endpush