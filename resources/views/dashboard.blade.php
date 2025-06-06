@extends('layouts.app')

@section('title', 'Welcome To Akresa')
@section('subtitle', 'Dashboard')

@section('content_header')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<style>
    .custom-box {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0.5rem;
    }

    .custom-box:hover {
        transform: scale(1.03);
        box-shadow: 0 10px 10px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }

    .small-box.bg-warning .small-box-footer {
        color: white !important;
    }
</style>

<div class="container-fluid">
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white border-bottom">
            <h3 class="card-title mb-0">Informasi Validasi</h3>
        </div>
        <div class="card-body">
            <div class="row justify-content-center g-2">
                <!-- Data Tervalidasi -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="small-box bg-info text-center custom-box">
                        <div class="inner py-3">
                            <h1>{{ $data['totalTerValidasi'] }}</h1>
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
                        <div class="inner py-3 text-white">
                            <h1>{{ $data['totalPerluValidasi'] }}</h1>
                            <p>Data Portofolio Perlu Validasi</p>
                        </div>
                        <a href="{{ route('chart.moreInfo', ['status' => 'Perlu Validasi']) }}" class="small-box-footer text-white">
                            More info <i class="fas fa-arrow-circle-right text-white"></i>
                        </a>
                    </div>
                </div>

                <!-- Data Tidak Valid -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="small-box bg-danger text-center custom-box">
                        <div class="inner py-3">
                            <h1>{{ $data['totalTidakValid'] }}</h1>
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
</div>
@endsection
