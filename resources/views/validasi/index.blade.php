@extends('layouts.app')

@section('title', 'Validasi')
@section('subtitle', 'Validasi')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Validasi</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-primary border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0 text-white">Validasi</h3>
                    <div class="card-tools">
                        
                    </div>
                </div>
            </div>

            <div class="card-body">
                {{-- Tombol Kriteria --}}
                <div class="row">
                    <div class="form-group row">
                        <label class="col-2 control-label col-form-label">Kriteria</label>
                        <div class="flex">
                            @for ($i = 1; $i <= 9; $i++)
                                <button type="button" class="btn btn-sm btn-primary kriteria-btn" data-kriteria="{{$i}}">
                                    Kriteria {{$i}}
                                </button>
                            @endfor
                            <small class="form-text text-muted pl-2">Pilih Kriteria</small>
                        </div>
                    </div>
                </div>

                {{-- Tempat tampilnya PDF --}}
                <div class="row mb-3" id="pdfContainer">
                    <p class="text-muted">Pilih kriteria untuk melihat PDF</p>
                </div>
            </div>

        </div>
@endsection
    @push('css')
        <style>
            .pdf-wrapper {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
            }
        </style>
    @endpush
    @push('scripts')
        <script>
            $(document).ready(function () {
                var selectedKriteria = null;

                var pdfLoader = {
                    ajax: {
                        reload: function () {
                            if (!selectedKriteria) return;

                            $('#pdfContainer').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading PDF...</div>');

                            $.ajax({
                                url: "{{ route('validasi.showFile') }}",
                                dataType: "json",
                                type: "POST",
                                data: {
                                    kriteria: selectedKriteria,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function (response) {
                                    if (response.success && response.pdfUrl) {
                                        // Buat iframe untuk PDF
                                        var komentarHtml = ``;
                                        if (response.status === 'revisi' && response.komentar) {
                                            komentarHtml = `
                                                                <div class="alert w-75 mx-auto bg-white border">
                                                                <strong>Alasan Tidak Valid:</strong><br>${response.komentar}
                                                                </div>
                                                            `;
                                        }
                                        var validDisabled = response.status === 'tervalidasi' ? 'disabled' : '';
                                        var tidakValidDisabled = response.status === 'revisi' ? 'disabled' : '';
                                        var iframeHtml = `
                                                                                        <div class="col-12">
                                                                                            <div class="card">
                                                                                                <div class="card-header">
                                                                                                    <h5>PDF Kriteria ${selectedKriteria}</h5>
                                                                                                </div>
                                                                                                <div class="card-body p-0" flex>
                                                                                                    <div class="pdf-wrapper d-flex justify-content-center">
                                                                                                    <iframe src="${response.pdfUrl}" 
                                                                                                            position="center"
                                                                                                            width="75%" 
                                                                                                            height="400px" 
                                                                                                            style="border: none;">
                                                                                                        <p>Browser Anda tidak mendukung PDF viewer. 
                                                                                                           <a href="${response.pdfUrl}" target="_blank">Download PDF</a>
                                                                                                        </p>
                                                                                                    </iframe>
                                                                                                    </div>
                                                                                                </div>
                                                                                                <div class="card-footer text-center bg-primary">
                                                                                                        ${komentarHtml}
                                                                                                        <div style="display: flex; flex-direction: column; align-items: center;">
                                                                                                            <div id="komentarWrapper" class="mb-2" style="display: none; width: 75%;">
                                                                                                                <textarea id="komentarInput" class="form-control" style="height: 300px;" placeholder="Tulis alasan tidak valid..."></textarea>
                                                                                                            </div>
                                                                                                            <div id="komentarButton" class="mb-2" style="display: none; width: 75%;">
                                                                                                                <button type="button" id="submit-comment" class="btn btn-sm btn-success ">Kirim Komentar</button>
                                                                                                                <button type="button" id="batal-comment" class="btn btn-sm btn-secondary ">Batal</button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <a href="${response.pdfUrl}" target="_blank" class="btn btn-sm btn-secondary border">
                                                                                                        <i class="fas fa-external-link-alt"></i> Buka di tab baru
                                                                                                        </a>
                                                                                                        <button id="btn-valid" class="btn btn-sm btn-success mx-1 border" ${validDisabled}>Valid</button>
                                                                                                        <button id="btn-tidak-valid" class="btn btn-sm btn-danger mx-1 border" ${tidakValidDisabled}>Tidak Valid</button>


                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    `;
                                        $('#pdfContainer').html(iframeHtml);
                                    } else {
                                        $('#pdfContainer').html('<div class="alert alert-warning">PDF tidak ditemukan untuk kriteria ini</div>');
                                    }
                                },
                                error: function () {
                                    $('#pdfContainer').html('<div class="alert alert-danger">Error loading PDF</div>');
                                }
                            });
                        }
                    }
                };

                $('.kriteria-btn').on('click', function () {
                    console.log("Tombol kriteria diklik");
                    selectedKriteria = $(this).data('kriteria');

                    // Update button states
                    $('.kriteria-btn').removeClass('btn-success').addClass('btn-primary');
                    $(this).removeClass('btn-primary').addClass('btn-success');

                    pdfLoader.ajax.reload();
                });
                $(document).on('click', '#btn-valid', function () {
                    // Kirim data ke controller Laravel
                    $.ajax({
                        url: "{{ route('validasi.valid') }}",
                        type: "PUT",
                        data: {
                            kriteria: selectedKriteria,
                            status: 'tervalidasi',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (!response.success) {
                                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan.', 'error');
                            } else {
                                Swal.fire('Berhasil', 'Data Tervalidasi.', 'success')
                                    .then(() => {
                                        pdfLoader.ajax.reload(); // 🔄 reload halaman
                                    });
                            }
                        }
                    });
                });
                $(document).on('click', '#submit-comment', function () {
                    // Kirim data ke controller Laravel
                    $.ajax({
                        url: "{{ route('validasi.store') }}",
                        type: "PUT",
                        data: {
                            kriteria: selectedKriteria,
                            komentar: $('#komentarInput').val(),
                            status: 'revisi',
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (!response.success) {
                                Swal.fire('Gagal', 'Terjadi kesalahan saat menyimpan.', 'error');
                            }
                            else {
                                Swal.fire('Berhasil', 'Komentar dikirim.', 'success')
                                    .then(() => {
                                        pdfLoader.ajax.reload(); // 🔄 reload halaman
                                    });
                            }
                        }
                    });
                });
                $(document).on('click', '#btn-tidak-valid', function () {
                    $('#komentarWrapper').show();
                    $('#komentarButton').show();
                });
                $(document).on('click', '#batal-comment', function () {
                    $('#komentarWrapper').hide();
                    $('#komentarButton').hide();
                });
            });

        </script>
    @endpush