<form id="formCreateKriteria" method="POST" action="{{ route('kriteria.store_ajax') }}">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Kriteria</h5>
        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        <div class="mb-3">
            <label for="no_kriteria" class="form-label">No Kriteria</label>
            <input type="text" class="form-control" id="no_kriteria" name="no_kriteria" value="kriteria1" readonly>
            <div class="invalid-feedback" id="error_no_kriteria"></div>
        </div>
        <div class="mb-3">
            <label for="id_user" class="form-label">Nama User (Maksimal 2 User)</label>
            <div class="input-group">
                <select class="form-select" id="id_user" name="id_user"
                    style="width: calc(98% - 40px); margin-right: 13px;">
                    <option value="">Pilih User</option>

                </select>
                <button type="button" class="btn btn-primary" id="addNewUser">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <div id="selectedUsers" class="mt-2"></div>
            <div class="invalid-feedback" id="error_id_user"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times me-1"></i>
            Batal</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
    </div>
</form>
<script>
    $(document).ready(function () {
        $.ajax({
            url: '{{ route("kriteria.get_last_number") }}',
            type: 'GET',
            success: function (response) {
                console.log(response);
                let nextNumber = 1;
                if (response.last_number) {
                    const lastNumberStr = response.last_number.toString();
                    const match = lastNumberStr.match(/\d+$/);
                    if (match) {
                        nextNumber = parseInt(match[0]) + 1;
                    }
                }
                $('#no_kriteria').val('kriteria ' + nextNumber);
            },
            error: function () {
                $('#no_kriteria').val('kriteria1');
            }
        });

        $.ajax({
            url: '{{ route("kriteria.get_users") }}',
            type: 'GET',
            success: function (response) {
                let options = '<option value="">Pilih User</option>';
                if (response && response.length > 0) {
                    response.forEach(function (profile_user) {
                        options += `<option value="${profile_user.id_user}">${profile_user.nama_lengkap}</option>`;
                    });
                }
                if (response.status === false) {
                    alert(response.message);
                }
                $('#id_user').html(options);
            },
            error: function (xhr, status, error) {
                console.error('Error loading users:', error);
                $('#id_user').html('<option value="">Gagal memuat user</option>');
            }
        });

        let selectedUsers = [];

        $('#addNewUser').on('click', function () {
            const userId = $('#id_user').val();
            const userName = $('#id_user option:selected').text();

            // Tidak ada validasi di JS, biarkan controller yang memvalidasi
            if (!userId) {
                $('#id_user').val('');
                $('#id_user').focus();
                return;
            }

            selectedUsers.push({ id: userId, name: userName });
            renderSelectedUsers();
            $('#id_user').val('');
        });

        function renderSelectedUsers() {
            let html = '';
            selectedUsers.forEach((user, idx) => {
                html += `
                    <div class="input-group mb-1">
                        <input type="text" class="form-control rounded" value="${user.name}" readonly style="width: calc(80% - 40px); margin-right: 14px;">
                        <input type="hidden" name="selected_users[]" value="${user.id}">
                        <button type="button" class="btn btn-danger removeUser" data-id="${user.id}" style="margin-right: 2px;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
            });
            $('#selectedUsers').html(html);
        }

        $('#selectedUsers').on('click', '.removeUser', function () {
            const userId = $(this).data('id').toString();
            selectedUsers = selectedUsers.filter(u => u.id !== userId);
            renderSelectedUsers();
        });

        $('#formCreateKriteria').off('submit').on('submit', function (e) {
            if ($('#id_user').val() !== '') {
                alert('Klik tombol tambah (+) untuk memasukkan user ke daftar!');
                e.preventDefault();
                return false;
            }
            setTimeout(function () {
                selectedUsers = [];
                renderSelectedUsers();
            }, 500);
        });

        $(document).on('hidden.bs.modal', '.modal', function () {
            selectedUsers = [];
            renderSelectedUsers();
        });
    });
</script>