@extends('layouts.auth') <!-- Ganti dari layouts.app ke layouts.auth -->

@section('title', 'Login')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">Login</div>
                <div class="card-body">
                    <form id="formLogin" method="POST" action="{{ url('postlogin') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                            <div class="invalid-feedback" id="error_username"></div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback" id="error_password"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                        <a href="{{route('lupaPassword')}}" class="mt-n1"><small>Lupa password ?</small></a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#formLogin').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                success: function(res) {
                    if (res.status) {
                        window.location.href = res.redirect;
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Gagal',
                            text: res.message,
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat login.',
                    });
                }
            });
        });
    });
</script>
@endpush
