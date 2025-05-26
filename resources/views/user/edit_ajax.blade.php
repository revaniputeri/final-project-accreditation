<form id="formEditUser" method="POST" action="{{ route('user.update_ajax', ['id' => $user->id_profile]) }}">
    @csrf
    @method('PUT')
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Edit Level</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="id_level" class="form-label">Level</label>
            <select name="id_level_form" id="id_level_form">
                <option value=""> >-Pilih Level-< </option>
                        @foreach ($level as $i)
                            <option value="{{ $i->id_level }}" @if($i->id_level == $user->user->id_level) selected @endif>
                                {{ $i->nama_level }}
                            </option>
                        @endforeach
            </select>
        </div>
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

        <div class="mb-3">
            <label for="gelar_depan" class=" form-label">Gelar Depan</label>
            <input type="text" class="form-control" id="gelar_depan" name="gelar_depan"
                value="{{ old('gelar_depan', $user->gelar_depan)}}" required>
            <div class="invalid-feedback" id="error_gelar_depan"></div>
        </div>

        <div class="mb-3">
            <label for="gelar_belakang" class="form-label">Gelar Belakang</label>
            <input type="text" class="form-control" id="gelar_belakang" name="gelar_belakang"
                value="{{old('gelar_belakang', $user->gelar_belakang)}}" required>
            <div class="invalid-feedback" id="error_gelar_belakang"></div>
        </div>

        <div class="mb-3">
            <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
            <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir"
                value="{{old('pendidikan_terakhir', $user->pendidikan_terakhir)}}" required>
            <div class="invalid-feedback" id="error_pendidikan_terakhir"></div>
        </div>
        <div class="mb-3">
            <label for="pangkat" class="form-label">Pangkat</label>
            <input type="text" class="form-control" id="pangkat" name="pangkat"
                value="{{old('pangkat', $user->pangkat)}}" required>
            <div class="invalid-feedback" id="error_pangkat"></div>
        </div>
        <div class="mb-3">
            <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
            <input type="text" class="form-control" id="jabatan_fungsional" name="jabatan_fungsional"
                value="{{old('jabatan_fungsional', $user->jabatan_fungsional)}}" required>
            <div class="invalid-feedback" id="error_jabatan_fungsional"></div>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" value="{{old('alamat', $user->alamat)}}"
                required>
            <div class="invalid-feedback" id="error_alamat"></div>
        </div>
        <div class="mb-3">
            <label for="no_telp" class="form-label">Nomor Telfon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp"
                value="{{old('no_telp', $user->no_telp)}}" required>
            <div class="invalid-feedback" id="error_no_telp"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
        
    </div>
</form>