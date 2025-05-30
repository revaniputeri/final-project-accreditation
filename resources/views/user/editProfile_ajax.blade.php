<form id="formEditProfile" method="POST" action="{{ route('user.updateProfile_ajax', ['id' => $user->id_profile]) }}">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit User</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="nidn" class="form-label">NIDN</label>
            <input type="text" class="form-control" id="nidn" name="nidn" value="{{old('nidn', $user->nidn)}}" required>
            <div class="invalid-feedback" id="error_nidn"></div>
        </div>
        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" class="form-control" id="nip" name="nip" value="{{old('nip', $user->nip)}}" required>
            <div class="invalid-feedback" id="error_nip"></div>
        </div>
        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                value="{{old('nama_lengkap', $user->nama_lengkap)}}" required>
            <div class="invalid-feedback" id="error_nama_lengkap"></div>
        </div>
        <div class="form-group mb-3">
            <label for="tempat_tanggal_lahir">Tempat Tanggal Lahir</label>
            <input type="text" class="form-control" id="tempat_tanggal_lahir" name="tempat_tanggal_lahir"
                value="{{old('tempat_tanggal_lahir', $user->tempat_tanggal_lahir)}}" required>
        </div>

        <div class="form-group mb-3">
            <label for="alamat">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat"
                value="{{old('alamat', $user->alamat)}}" required>
        </div>
        <div class="form-group mb-3">
            <label for="no_telp">Nomor Telfon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp"
                value="{{old('no_telp', $user->no_telp)}}" required>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
