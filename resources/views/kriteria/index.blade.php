@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Data Kriteria</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal"
                                data-target="#modal-create">
                                <i class="fas fa-plus"></i> Tambah Data
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('kriteria.export.excel') }}">Export Excel</a>
                                    <a class="dropdown-item" href="{{ route('kriteria.export.pdf') }}">Export PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="kriteria-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>No Kriteria</th>
                                    <th>Nama User</th>
                                    <th>Jumlah Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div class="modal fade" id="modal-create" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Kriteria</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="create-content"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="modal-edit" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kriteria</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="edit-content"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="modal-detail" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kriteria</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="detail-content"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="delete-content"></div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        $(function () {
            var table = $('#kriteria-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('kriteria.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'no_kriteria', name: 'no_kriteria' },
                    { data: 'user.username', name: 'user.username' },
                    { data: 'dokumen_count', name: 'dokumen_count' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });

            // Create
            $('#modal-create').on('show.bs.modal', function () {
                $.get("{{ route('kriteria.create.ajax') }}", function (data) {
                    $('#create-content').html(data);
                });
            });

            // Edit
            $('#kriteria-table').on('click', '.edit-btn', function () {
                var no_kriteria = $(this).data('no-kriteria');
                var id_user = $(this).data('id-user');
                $('#modal-edit').modal('show');
                $.get("{{ url('kriteria/edit-ajax') }}/" + no_kriteria + "/" + id_user, function (data) {
                    $('#edit-content').html(data);
                });
            });

            // Detail
            $('#kriteria-table').on('click', '.detail-btn', function () {
                var no_kriteria = $(this).data('no-kriteria');
                var id_user = $(this).data('id-user');
                $('#modal-detail').modal('show');
                $.get("{{ url('kriteria/detail-ajax') }}/" + no_kriteria + "/" + id_user, function (data) {
                    $('#detail-content').html(data);
                });
            });

            // Delete
            $('#kriteria-table').on('click', '.delete-btn', function () {
                var no_kriteria = $(this).data('no-kriteria');
                var id_user = $(this).data('id-user');
                $('#modal-delete').modal('show');
                $.get("{{ url('kriteria/confirm-ajax') }}/" + no_kriteria + "/" + id_user, function (data) {
                    $('#delete-content').html(data);
                });
            });
        });
    </script>
@endpush