@extends('layouts.auth')

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
                <div class="error-message" id="passwordError"></div>
            </div>

            <div class="form-options">
                <a href="{{ route('lupaPassword') }}" class="forgot-password">Forgot password?</a>
            </div>

            <button type="submit" class="auth-button">
                <span>Login</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Toggle password visibility
        $('.toggle-password').on('click', function() {
            const passwordInput = $('#password');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            $(this).toggleClass('fa-eye fa-eye-slash');
        });

        // Form validation and submission
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var formData = form.serialize();

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        window.location.href = response.redirect;
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
                        text: 'An unexpected error occurred. Please try again.',
                        background: 'rgba(0, 0, 0, 0.9)',
                        color: 'white',
                        confirmButtonColor: '#4e73ff'
                    });
                }
            });
        });
    });
</script>
@endpush
