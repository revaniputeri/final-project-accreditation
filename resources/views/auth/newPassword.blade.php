<form id="formUpdatePassword" method="POST" action="{{route('updatePassword', ['id' => $user->id_profile]) }}">
    @csrf
    @method('PUT')
    <div class="container">
        <div class="card-header bg-primary text-white">
            <p>Masukkan Password Baru Anda</p>
        </div>
        <div class="card-body">
            <div class="bg-white">
                <div class="mb-3">
                    <label for="password" class="form-label">password</label>
                    <input type="password" class="form-control" id="password" name="password" required autofocus>
                    <div class="invalid-feedback" id="error_password"></div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Verifikasi Password</label>
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" required autofocus>
                        <div class="invalid-feedback" id="error_password2"></div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verifikasi</button>
            </div>
        </div>
    </div>
</form>