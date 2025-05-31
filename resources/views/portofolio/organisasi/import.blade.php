<div class="modal-header bg-primary text-white">
    <h5 class="modal-title">Import Data Organisasi</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<form id="form-import" action="{{ route('portofolio.organisasi.import_ajax') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="modal-body">
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Pastikan file Excel mengikuti format template.
        </div>

        <div class="mb-3">
            <label class="form-label">Download Template</label>
            <div>
                <a href="{{ asset('template/template_organisasi.xlsx') }}" class="btn btn-success btn-sm" download>
                    <i class="fas fa-file-excel me-1"></i> Download Template
                </a>
            </div>
            <small class="text-muted">Format: .xlsx (Excel)</small>
        </div>

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <label for="file_organisasi" class="btn btn-info">Choose File</label>
            </div>
            <input type="file" class="form-control d-none" id="file_organisasi" name="file_organisasi" required
                accept=".xlsx,.xls"
                onchange="document.getElementById('file_organisasi_text').value = this.files[0]?.name || 'No file chosen'">
            <input type="text" class="form-control" id="file_organisasi_text" placeholder="No file chosen" readonly>
            <div id="error-file_organisasi" class="invalid-feedback"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload me-1"></i> Upload
        </button>
    </div>
</form>

<script>
$(document).ready(function() {
    $("#form-import").submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var submitBtn = $(this).find('button[type="submit"]');

        // Disable button and show loading
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Memproses...');

        // Clear previous errors
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modal-master').modal('hide');

                if (response.status) {
                    let message = response.message;
                    if (response.details && response.details.length > 0) {
                        message += '\n\nDetail:\n' + response.details.slice(0, 5).join('\n');
                        if (response.details.length > 5) {
                            message += '\n...dan ' + (response.details.length - 5) + ' pesan lainnya.';
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Import Berhasil',
                        text: message,
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (typeof window.LaravelDataTables !== 'undefined' && window.LaravelDataTables["p_organisasi-table"]) {
                            window.LaravelDataTables["p_organisasi-table"].ajax.reload();
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Import Gagal',
                        text: response.message,
                        showConfirmButton: true,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                $('#modal-master').modal('hide');
                let errorMessage = 'Terjadi kesalahan saat mengupload file';

                if (xhr.responseJSON) {
                    if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    // Handle validation errors
                    if (xhr.responseJSON.errors) {
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            if (field === 'file_organisasi') {
                                $('#file_organisasi').addClass('is-invalid');
                                $('#error-file_organisasi').text(errors[field][0]);
                            }
                        }
                        return; // Don't show SweetAlert if we're showing field errors
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage,
                    showConfirmButton: true,
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false).html('<i class="fas fa-upload me-1"></i> Upload');
            }
        });
    });
});
</script>
