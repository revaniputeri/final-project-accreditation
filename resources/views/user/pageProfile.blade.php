@extends('layouts.app')

@section('title', 'User')
@section('subtitle', 'User')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Profile User</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Profile User</h3>
                    <div class="card-tools">

                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset('storage/user_avatar/' . Auth::user()->id_user . '.png') }}" alt="User Photo"
                            class="profile-user-img img-fluid rounded-circle border border-3 border-primary shadow"
                            style="width: 120px; height: 120px; object-fit: cover;"
                            onerror="this.onerror=null; this.src='{{ asset('storage/user_avatar/default.jpg') }}';">

                        <!-- Icon Kamera Bootstrap -->
                        <label onclick="modalAction('{{ route('profile.editPhoto_ajax') }}')"
                            class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2 shadow"
                            style="cursor: pointer; transform: translate(25%, 25%);">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                </div>
                <h4 class="text-center">{{ $user->nama_lengkap }}</h4>
                <p class="text-center text-muted">{{ $level->nama_level }}</p>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <strong>Username:</strong>
                        <p>{{ $user->user->username ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>NIDN:</strong>
                        <p>{{ $user->nidn ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Nomor Telepon:</strong>
                        <p>{{ $user->no_telp ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>NIP: </strong>
                        <p>{{ $user->nip ?? '-' }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Alamat:</strong>
                        <p>{{ $user->alamat ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Tanggal Bergabung:</strong>
                        <p>{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="button"
                        onclick="modalAction('{{ route('profile.editProfile_ajax', ['id' => $user->id_profile]) }}')"
                        class="btn btn-sm btn-primary">Edit Profile</button>
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
        function modalAction(url) {
            $.get(url)
                .done(function(response) {
                    $('#myModal .modal-content').html(response);
                    $('#myModal').modal('show');

                    $(document).off('submit', '#formEditProfile');

                    $(document).on('submit', '#formEditProfile', function(e) {
                        e.preventDefault();
                        var form = $(this);

                        $.ajax({
                            url: form.attr('action'),
                            method: form.find('input[name="_method"]').val() || form.attr('method'),
                            data: form.serialize(),
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
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
                                        // Ini dieksekusi setelah user klik OK
                                        location.reload();
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
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Ini dieksekusi setelah user klik OK
                                        location.reload();
                                    });
                                } else {
                                    let msg = xhr.statusText ||
                                        'Terjadi kesalahan saat menyimpan data.';
                                    Swal.fire('Error!', msg, 'error');
                                }
                            }
                        });
                    });
                })
                .fail(function(xhr) {
                    Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                });
        }
    </script>
@endpush
