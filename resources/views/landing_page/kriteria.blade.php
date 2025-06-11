@extends('layouts.landing')

@section('title', 'Dokumen Pendukung Kriteria')

@section('content_header')
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">Dokumen Pendukung Kriteria {{ $no_kriteria }}</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container">

        <!-- Header Section -->
        <div class="section-header mb-4">
            <h2>Dokumen Pendukung</h2>
            <p>Kriteria {{ $no_kriteria }}</p>
            <div class="divider my-3">
                <span class="divider-line"></span>
                <span class="divider-icon"><i class="bi bi-file-earmark-text"></i></span>
                <span class="divider-line"></span>
            </div>
        </div>

        @if ($dokumenPendukung->isEmpty())
            <div class="alert alert-info text-center py-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                Tidak ada dokumen pendukung untuk kriteria ini.
            </div>
        @else
            @foreach ($dokumenPendukung as $kategori => $dokumens)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white text-capitalize">
                        {{ $kategori }}
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width: 5%">
                                <col style="width: 40%">
                                <col style="width: 40%">
                                <col style="width: 15%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama File</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dokumens as $index => $dokumen)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $dokumen->nama_file }}</td>
                                        <td>{{ $dokumen->keterangan }}</td>
                                        <td>
                                            <div class="d-flex">
                                                <a href="{{ asset('storage/dokumen_pendukung/' . $dokumen->path_file) }}"
                                                    class="btn btn-sm btn-outline-primary me-2" title="Lihat Dokumen"
                                                    target="_blank">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ asset('storage/dokumen_pendukung/' . $dokumen->path_file) }}"
                                                    class="btn btn-sm btn-primary" title="Download" download>
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <style>
        .divider {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 1rem 0;
        }

        .divider-line {
            flex: 1;
            height: 1px;
            background-color: #dee2e6;
        }

        .divider-icon {
            padding: 0 1rem;
            color: #0d6efd;
            font-size: 1.5rem;
        }

        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2c4964;
            text-align: center;
        }

        .section-header p {
            text-align: center;
            margin-bottom: 0;
            color: #6c757d;
        }

        .card {
            border-radius: 10px;
            overflow: hidden;
        }

        .card-header {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .table th {
            background-color: #f8f9fa !important;
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
    </style>
@endsection
