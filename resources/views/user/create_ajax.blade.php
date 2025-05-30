{{-- resources/views/level/create_ajax.blade.php --}}
<form id="formCreateUser" method="POST" action="{{ route('user.store_ajax') }}">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah User</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="id_level" class="form-label">Level</label>
            <select name="id_level" id="id_level">
                <option value=""> >-Pilih Level-< </option>
                        @foreach ($level as $i)
                            <option value="{{$i->id_level}}">{{$i->nama_level}}</option>
                        @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="nidn" class="form-label">NIDN</label>
            <input type="text" class="form-control" id="nidn" name="nidn" required>
            <div class="invalid-feedback" id="error_nidn"></div>
        </div>
        <div class="mb-3">
            <label for="nip" class="form-label">NIP</label>
            <input type="text" class="form-control" id="nip" name="nip" required>
            <div class="invalid-feedback" id="error_nip"></div>
        </div>
        <div class="mb-3">
            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            <div class="invalid-feedback" id="error_nama_lengkap"></div>
        </div>
        <div class="form-group mb-3">
            <label for="tempat_lahir">Tempat Lahir</label>
            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" required>
        </div>

        <div class="form-group mb-3">
            <label for="tanggal_lahir">Tanggal Lahir</label>
            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
        </div>

        <div class="mb-3">
            <label for="gelar_depan" class=" form-label">Gelar Depan</label>
            <input type="text" class="form-control" id="gelar_depan" name="gelar_depan">
            <div class="invalid-feedback" id="error_gelar_depan"></div>
        </div>

        <div class="mb-3">
            <label for="gelar_belakang" class="form-label">Gelar Belakang</label>
            <input type="text" class="form-control" id="gelar_belakang" name="gelar_belakang">
            <div class="invalid-feedback" id="error_gelar_belakang"></div>
        </div>

        <div class="mb-3">
            <label for="pendidikan_terakhir" class="form-label">Pendidikan Terakhir</label>
            <input type="text" class="form-control" id="pendidikan_terakhir" name="pendidikan_terakhir" required>
            <div class="invalid-feedback" id="error_pendidikan_terakhir"></div>
        </div>
        <div class="mb-3">
            <label for="pangkat" class="form-label">Pangkat</label>
            <input type="text" class="form-control" id="pangkat" name="pangkat">
            <div class="invalid-feedback" id="error_pangkat"></div>
        </div>
        <div class="mb-3">
            <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
            <input type="text" class="form-control" id="jabatan_fungsional" name="jabatan_fungsional">
            <div class="invalid-feedback" id="error_jabatan_fungsional"></div>
        </div>
        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <input type="text" class="form-control" id="alamat" name="alamat" required>
            <div class="invalid-feedback" id="error_alamat"></div>
        </div>
        <div class="mb-3">
            <label for="no_telp" class="form-label">Nomor Telfon</label>
            <input type="text" class="form-control" id="no_telp" name="no_telp" required>
            <div class="invalid-feedback" id="error_no_telp"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
