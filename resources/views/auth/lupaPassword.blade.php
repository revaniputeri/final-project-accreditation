@extends('layouts.auth') <!-- Ganti dari layouts.app ke layouts.auth -->

@section('title', 'Login')

@section('content')
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h3>Selamat Datang</h3>
                        <p>Silahkan masukkan data diri anda untuk melakukan verifikasi <i
                                class="fas fa-exclamation-triangle text-warning me-1"></i>
                        </p>
                    </div>
                    <div class="card-body">
                        <form id="formLupaPassword" method="POST" action="{{route('verifyDataGuest') }}">
                            @csrf
                            <div class="mb-3">
                                <label for="nidn" class="form-label">NIDN</label>
                                <input type="text" class="form-control" id="nidn" name="nidn" required autofocus>
                                <div class="invalid-feedback" id="error_nidn"></div>
                            </div>
                            <div class="mb-3">
                                <label for="tempat_tanggal_lahir" class="form-label">Tempat Tanggal Lahir</label>
                                <input type="text" class="form-control" id="tempat_tanggal_lahir"
                                    name="tempat_tanggal_lahir" required>
                                <div class="invalid-feedback" id="error_tempat_tanggal_lahir"></div>
                            </div>
                            <div class="mb-3">
                                <label for="no_telp" class="form-label">Nomor Telfon</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" required>
                                <div class="invalid-feedback" id="error_no_telp"></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
                        </form>
                    </div>
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
    <script>
        $(document).ready(function () {
            $('#formLupaPassword').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                var formData = form.serialize();
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.status) {

                            Swal.fire({
                                icon: 'success',
                                title: 'Verifikasi Success',
                                text: response.message,
                            }).then(() => {
                                modalAction(response.url);
                            });


                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat Verifikasi.',
                        });
                    }
                });
            });
        });
        function modalAction(url) {
            $.get(url)
                .done(function (response) {
                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');

                    $(document).off('submit', '#formUpdatePassword');
                    $(document).on('submit', '#formUpdatePassword', function (e) {
                        e.preventDefault();
                        var form = $(this);

                        $.ajax({
                            url: form.attr('action'),
                            method: form.find('input[name="_method"]').val() || form.attr('method'),
                            data: form.serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                $('#myModal').modal('hide');

                                if (response.alert && response.message) {
                                    Swal.fire({
                                        icon: response.alert,
                                        title: response.alert === 'success' ? 'Sukses' : 'Error',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Ini dieksekusi setelah user klik OK
                                        location.reload();
                                    });
                                }
                            },
                            error: function (xhr) {
                                $('#myModal').modal('hide');
                                if (xhr.responseJSON && xhr.responseJSON.alert && xhr.responseJSON.message) {
                                    Swal.fire({
                                        icon: xhr.responseJSON.alert,
                                        title: xhr.responseJSON.alert === 'success' ? 'Sukses' : 'Error',
                                        text: xhr.responseJSON.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Ini dieksekusi setelah user klik OK
                                        location.reload();
                                    });
                                } else {
                                    let msg = xhr.statusText || 'Terjadi kesalahan saat menyimpan data.';
                                    Swal.fire('Error!', msg, 'error');
                                }
                            }
                        });
                    });
                })
                .fail(function (xhr) {
                    Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                });
        }
    </script>
@endpush