{{-- resources/views/user/create_ajax.blade.php --}}
<form id="formCreateUser" method="POST" action="{{ route('user.store_ajax') }}">
    @csrf
    <div class="modal-header bg-info text-white">
        <h5 class="modal-title">Detail</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <table class="table table-bordered">
            <tr>
                <th>ID Profile</th>
                <td>{{ $user->id_profile}}</td>
            </tr>
            <tr>
                <th>ID user</th>
                <td><b>{{ $user->id_user }}</b></td>
            </tr>
            <tr>
                <th>NIDN</th>
                <td>{{ $user->nidn }}</td>
            </tr>
            <tr>
                <th>NIP</th>
                <td>{{ $user->nip }}</td>
            </tr>
            <tr>
                <th>Level</th>
                <td><Strong>{{ $user->user->level->nama_level}}</Strong></td>
            </tr>
            <tr>
                <th>Nama User</th>
                <td>{{ $user->nama_lengkap }}</td>
            </tr>
            <tr>
                <th>Gelar Depan</th>
                <td>{{ $user->gelar_depan }}</td>
            </tr>
            <tr>
                <th>Gelar Belakang</th>
                <td>{{ $user->gelar_belakang }}</td>
            </tr>
            <tr>
                <th>Pendidikan Terakhir</th>
                <td>{{ $user->pendidikan_terakhir }}</td>
            </tr>
            <tr>
                <th>Pangkat</th>
                <td>{{ $user->pangkat }}</td>
            </tr>
            <tr>
                <th>Jabatan Fungsional</th>
                <td>{{ $user->jabatan_fungsional }}</td>
            </tr>
            <tr>
                <th>Tempat Tanggal Lahir</th>
                <td>{{ $user->tempat_tanggal_lahir }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $user->alamat }}</td>
            </tr>
            <tr>
                <th>No Telfon</th>
                <td>{{ $user->no_telp }}</td>
            </tr>
            <tr>
                <th>username</th>
                <td>{{ $user->user->username }}</td>
            </tr>
            
            <tr>
                <th>Created At</th>
                <td>{{ $user->created_at ? $user->created_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td>{{ $user->updated_at ? $user->updated_at->format('d M Y H:i:s') : '-' }}</td>
            </tr>
        </table>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    </div>
</form>
