@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="video-background">
        <div class="video-overlay"></div>
    </div>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-brand">
                <div class="logo-container">
                    <img src="{{ asset('img/akresa-logo-text.svg') }}" alt="AKRESA Logo" class="logo-image"
                        style="width: 120px;">
                </div>
            </div>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="{{ route('postlogin') }}" class="auth-form">
                @csrf
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-with-icon">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>
                    <div class="error-message" id="usernameError"></div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <i class="fas fa-eye toggle-password"></i>
                    </div>
                    <div class="error-message" id="passwordError"></div>
                </div>

                <div class="form-options">
                    <a href="#" id="showForgotPassword" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="auth-button">
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>

            <!-- Forgot Password Form -->
            <form id="forgotPasswordForm" method="POST" action="{{ route('verifyDataGuest') }}" class="auth-form"
                style="display:none;">
                @csrf
                <div class="form-group">
                    <label for="nidn">NIDN</label>
                    <div class="input-with-icon">
                        <i class="fas fa-id-card"></i>
                        <input type="text" class="form-control" id="nidn" name="nidn"
                            placeholder="Enter your NIDN" required autofocus>
                    </div>
                    <div class="error-message" id="error_nidn"></div>
                </div>
                <div class="form-group">
                    <label for="tempat_tanggal_lahir">Tempat Tanggal Lahir</label>
                    <div class="input-with-icon">
                        <i class="fas fa-calendar-alt"></i>
                        <input type="text" class="form-control" id="tempat_tanggal_lahir" name="tempat_tanggal_lahir"
                            placeholder="Enter your birth place and date" required>
                    </div>
                    <div class="error-message" id="error_tempat_tanggal_lahir"></div>
                </div>
                <div class="form-group">
                    <label for="no_telp">Nomor Telfon</label>
                    <div class="input-with-icon">
                        <i class="fas fa-phone"></i>
                        <input type="text" class="form-control" id="no_telp" name="no_telp"
                            placeholder="Enter your phone number" required>
                    </div>
                    <div class="error-message" id="error_no_telp"></div>
                </div>
                <div class="form-options">
                    <a href="#" id="backToLogin" class="forgot-password">Back to login</a>
                </div>
                <button type="submit" class="auth-button">Verifikasi</button>
            </form>

            <!-- New Password Form -->
            <form id="formUpdatePassword" method="POST" action="" class="auth-form" style="display:none;">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="password">Password Baru</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your new password" required autofocus>
                    </div>
                    <div class="error-message" id="error_password"></div>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Verifikasi Password</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Enter your new password again" required>
                    </div>
                    <div class="error-message" id="error_password_confirmation"></div>
                </div>
                <div class="form-options">
                    <a href="#" id="backToLogin" class="forgot-password">Back to login</a>
                </div>
                <button type="submit" class="auth-button">Ubah Password</button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Existing styles from login.blade.php */
        :root {
            --primary-color: rgba(255, 255, 255, 0.9);
            --secondary-color: rgba(255, 255, 255, 0.7);
            --accent-color: #4e73ff;
            --error-color: #ff4d4d;
            --transition-speed: 0.3s;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
            color: white;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-image: url('{{ asset('img/background-login.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .video-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 2rem;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: rgba(248, 248, 248, 0.517);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transform: translateY(0);
            transition: all 0.4s ease;
            animation: fadeInUp 0.6s ease;
            color: black;
        }

        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-container {
            margin-bottom: 24px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-image {
            width: 250px;
            height: auto;
            margin-bottom: 1.5rem;
        }

        .auth-form {
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-with-icon i:first-child {
            position: absolute;
            left: 15px;
            color: black;
            font-size: 1rem;
        }

        .input-with-icon .toggle-password {
            position: absolute;
            right: 15px;
            color: #000000;
            cursor: pointer;
            font-size: 1rem;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.685);
            color: black;
            font-size: 0.95rem;
            transition: all var(--transition-speed) ease;
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 2px rgba(78, 115, 255, 0.2);
            background: rgba(255, 255, 255, 0.15);
        }

        .input-with-icon input::placeholder {
            color: rgba(0, 0, 0, 0.5);
        }

        .form-options {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }

        .forgot-password {
            color: #000000;
            text-decoration: none;
            transition: color var(--transition-speed, 0.3s) ease;
            cursor: pointer;
        }

        .forgot-password:hover {
            color: var(--primary-color, #007bff);
            text-decoration: underline;
        }

        .auth-button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--accent-color), #3a5bff);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-speed) ease;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            box-shadow: 0 4px 15px rgba(78, 115, 255, 0.3);
        }

        .auth-button span {
            margin-right: 10px;
        }

        .auth-button:hover {
            background: linear-gradient(135deg, #3a5bff, var(--accent-color));
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 115, 255, 0.4);
        }

        .auth-button:active {
            transform: translateY(0);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.8rem;
            margin-top: 5px;
            display: none;
        }

        .has-error input {
            border-color: var(--error-color) !important;
        }

        .has-error .error-message {
            display: block;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 576px) {
            .auth-card {
                padding: 1.5rem;
                border-radius: 15px;
            }

            .logo-image {
                width: 100px;
            }
        }
    </style>
@endpush

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Toggle password visibility in login form
            $('.toggle-password').on('click', function() {
                const passwordInput = $('#password');
                const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
                passwordInput.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            // Show forgot password form
            $('#showForgotPassword').on('click', function(e) {
                e.preventDefault();
                $('#loginForm').hide();
                $('#forgotPasswordForm').show();
            });

            // Back to login form (for forgot password and update password forms)
            $('#forgotPasswordForm .forgot-password, #formUpdatePassword .forgot-password').on('click', function(
                e) {
                e.preventDefault();
                $('#forgotPasswordForm').hide();
                $('#formUpdatePassword').hide();
                $('#loginForm').show();
            });

            // Login form validation and submission
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                const form = $(this);

                // Reset errors
                $('.form-group').removeClass('has-error');
                $('.error-message').hide();

                // Validate inputs
                let isValid = true;

                if (!$('#username').val().trim()) {
                    $('#usernameError').text('Username is required').show();
                    $('#username').parent().parent().addClass('has-error');
                    isValid = false;
                }

                if (!$('#password').val()) {
                    $('#passwordError').text('Password is required').show();
                    $('#password').parent().parent().addClass('has-error');
                    isValid = false;
                }

                if (!isValid) return;

                // Submit form via AJAX
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
                                title: 'Login Failed',
                                text: response.message,
                                background: 'rgba(0, 0, 0, 0.9)',
                                color: 'white',
                                confirmButtonColor: '#4e73ff'
                            });

                            if (response.errors) {
                                $.each(response.errors, function(field, messages) {
                                    $(`#${field}Error`).text(messages[0]).show();
                                    $(`#${field}`).parent().parent().addClass(
                                        'has-error');
                                });
                            }
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

            // Forgot password form submission
            $('#forgotPasswordForm').submit(function(e) {
                e.preventDefault();
                const form = $(this);

                // Reset errors
                $('.form-group').removeClass('has-error');
                $('.error-message').hide();

                // Submit form via AJAX
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Verifikasi Success',
                                text: response.message,
                            }).then(() => {
                                $('#forgotPasswordForm').hide();
                                $('#formUpdatePassword').show();
                                $('#formUpdatePassword').attr('action', response.url);
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan saat Verifikasi.',
                        });
                    }
                });
            });

            // New password form submission
            $(document).on('submit', '#formUpdatePassword', function(e) {
                e.preventDefault();
                var form = $(this);

                // Client-side validation for password confirmation
                var password = form.find('input[name="password"]').val();
                var passwordConfirmation = form.find('input[name="password_confirmation"]').val();
                if (password !== passwordConfirmation) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Verifikasi password tidak cocok',
                    });
                    return;
                }

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
                                // After successful password update, show login form instead of reload
                                $('#formUpdatePassword').hide();
                                $('#loginForm').show();
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#myModal').modal('hide');
                        if (xhr.responseJSON && xhr.responseJSON.alert && xhr.responseJSON
                            .message) {
                            Swal.fire({
                                icon: xhr.responseJSON.alert,
                                title: xhr.responseJSON.alert === 'success' ? 'Sukses' :
                                    'Error',
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

            // Clear error when typing
            $('input').on('input', function() {
                $(this).parent().parent().removeClass('has-error');
                $(`#${this.id}Error`).hide();
            });
        });
    </script>
@endpush
