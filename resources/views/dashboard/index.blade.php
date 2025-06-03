@extends('layouts.app')

@section('title', 'More Info')
@section('subtitle', 'More Info')

@section('content_header')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
            <li class="breadcrumb-item active">More Info</li>
        </ol>
    </nav>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-primary border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title mb-0 text-white">Daftar Portofolio ({{ ucfirst($status) }})</h3>
            </div>
        </div>

        <div class="card-body">
            {{-- Filter --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="fw-bold" for="filterSumberData">Filter Sumber Data:</label>
                    <select id="filterSumberData" class="form-control select2">
                        <option value="">-- Semua Sumber --</option>
                        <option value="p3m">P3M</option>
                        <option value="dosen">Dosen</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold" for="filterJenis">Filter Jenis Portofolio:</label>
                    <select id="filterJenis" class="form-control select2">
                        <option value="">-- Semua Jenis --</option>
                        <option value="organisasi">Organisasi</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="penelitian">Penelitian</option>
                        <option value="pengabdian">Pengabdian Masyarakat</option>
                        <option value="publikasi">Publikasi</option>
                        <option value="sertifikasi">Sertifikasi</option>
                        <option value="prestasi">Prestasi</option>
                        <option value="hki">HKI</option>
                        <option value="karyabuku">Karya Buku</option>
                    </select>
                </div>
            </div>

            {{-- Tabel --}}
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="moreInfoTable" style="width: 100%">
                    <thead>
                        <tr class="text-center">
                            <th>No</th>
                            <th>Nama Dosen</th>
                            <th>Jenis Portofolio</th>
                            <th>Nama Portofolio</th>
                            <th>Sumber</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $item->nama_dosen ?? '-' }}</td>
                            <td>{{ ucfirst($item->jenis) }}</td>
                            <td>{{ $item->nama }}</td>
                            <td class="text-center">
                                <span class="badge bg-secondary text-uppercase">{{ $item->sumber }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = [
                                        'tervalidasi' => 'bg-success',
                                        'perlu validasi' => 'bg-warning text-dark',
                                        'tidak valid' => 'bg-danger',
                                    ][$item->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ strtoupper($item->status ?? '-') }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        const table = $('#moreInfoTable').DataTable({
            ordering: false,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            language: {
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                infoEmpty: "Tidak ada data tersedia",
                zeroRecords: "Tidak ada data yang cocok",
                paginate: {
                    first: "Pertama",
                    last: "Terakhir",
                    next: "Berikutnya",
                    previous: "Sebelumnya"
                },
            }
        });

        // Filter custom
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            const filterSumber = $('#filterSumberData').val().toLowerCase();
            const filterJenis = $('#filterJenis').val().toLowerCase();
            const jenis = data[1].toLowerCase();
            const sumber = data[3].toLowerCase();

            return (!filterSumber || sumber.includes(filterSumber)) &&
                   (!filterJenis || jenis.includes(filterJenis));
        });

        $('#filterSumberData, #filterJenis').on('change', function () {
            table.draw();
        });
    });
</script>
@endpush
