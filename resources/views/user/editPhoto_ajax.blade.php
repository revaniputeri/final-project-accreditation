<form action="{{route('profile.storePhoto_ajax') }}" method="POST" enctype="multipart/form-data" id="input-form">
    @csrf
    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            Ubah Photo Profile
        </h5>
        <div class="modal-tools">
            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        </div>
    </div>
    <div class="modal-body">
        <div class="form-group text-center">
            <div class="form-group row">
                <label class="control-label">Current Photo</label>
                <img class="profile-user-img img-fluid img-circle mb-2"
                    src="{{ asset('storage/user_avatar/' . Auth::user()->id_user . '.png') }}" alt="User profile picture"
                    width="120" height="120" id="preview-avatar">
            </div>

            <div class="form-group row">
                <label> Pilih File : </label>
                <input type="file" id="avatar" name="avatar" class="form-control" accept="image/*"
                    onchange="previewImage(this)">
                <small id="error-avatar" class="error-text form-text text-danger"></small>
            </div>
            <div class="form-group row">
                <button type="submit" class="btn btn-sm btn-primary mt-2">Confirm</button>
                <a href="profile/" class="btn btn-sm btn-danger mt-2 ml-2">Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
    // Tambahkan rule 'filesize' di luar validate()
    $.validator.addMethod('filesize', function (value, element, param) {
        return this.optional(element) || (element.files[0].size <= param);
    }, 'Ukuran file maksimal 2MB.');

    $(document).ready(function () {
        $("#input-form").validate({
            rules: {
                avatar: {
                    required: true,
                    extension: "jpg|jpeg|png|gif",
                    filesize: 2048000 // 2MB dalam byte
                }
            },
            messages: {
                avatar: {
                    required: "Silakan pilih gambar.",
                    extension: "Format yang diizinkan: png",
                    filesize: "Ukuran file maksimal 2MB."
                }
            },
            submitHandler: function (form) {
                var formData = new FormData(form);

                $.ajax({
                    url: form.action,
                    type: form.method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('.btn-primary').prop('disabled', true).text('Uploading...');
                    },
                    success: function (response) {
                        if (response.status) {
                            $('#myModal').modal('hide'); // pastikan modal ini ada
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: response.message
                            }).then(() => {
                                // Ini dieksekusi setelah user klik OK
                                location.reload();
                            }); // pastikan ini sesuai dengan datatable Anda
                        } else {
                            $('.error-text').text('');
                            $.each(response.msgField, function (prefix, val) {
                                $('#error-' + prefix).text(val[0]);
                            });
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: response.message
                            }).then(() => {
                                        // Ini dieksekusi setelah user klik OK
                                        location.reload();
                                    });
                        }
                    },
                    complete: function () {
                        $('.btn-primary').prop('disabled', false).text('Confirm');
                    }
                });
                return false;
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function (element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });
    });

    // Fungsi preview gambar
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#preview-avatar').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>