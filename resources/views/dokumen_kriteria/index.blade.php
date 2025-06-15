@extends('layouts.app')

@section('title', 'Input Dokumen Kriteria')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Dokumen Kriteria</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    @if (count($kategoriList) === 0)
        <div class="alert alert-danger text-center">Dokumen kriteria belum tersedia.</div>
    @else
        <div class="nav-tabs">
            <ul class="nav nav-tabs" id="kategoriTabs" role="tablist">
                @foreach ($kategoriList as $index => $kategori)
                    <li class="nav-item" role="presentation">
                        <a class="nav-link @if ($kategori === $selectedKategori) active @endif"
                            id="custom-content-below-{{ $kategori }}-tab" data-toggle="tab"
                            href="#custom-content-below-{{ $kategori }}" role="tab"
                            aria-controls="custom-content-below-{{ $kategori }}"
                            aria-selected="{{ $kategori === $selectedKategori ? 'true' : 'false' }}">
                            {{ ucfirst($kategori) }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="kategoriTabsContent">
                @foreach ($kategoriList as $kategori)
                    @php
                        $dokumenForKategori = $dokumenGrouped->get($kategori, collect());
                        $latestDokumen = $dokumenForKategori->first();
                    @endphp
                    <div class="tab-pane fade @if ($kategori === $selectedKategori) show active @endif"
                        id="custom-content-below-{{ $kategori }}" role="tabpanel"
                        aria-labelledby="custom-content-below-{{ $kategori }}-tab">
                        <div class="container-fluid mt-3">
                            {{-- CARD 1: Keterangan Dokumen Kriteria --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary border-bottom">
                                    <h3 class="card-title mb-0">Keterangan Dokumen Kriteria</h3>
                                </div>
                                <div class="card-body">
                                    @if (!$latestDokumen)
                                        <p class="mb-0">Tidak ada data dokumen kriteria.</p>
                                    @else
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No Kriteria</th>
                                                    <th>Judul</th>
                                                    <th>Versi</th>
                                                    <th>Status</th>
                                                    <th>Waktu Dibuat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr data-id="{{ $latestDokumen->id_dokumen_kriteria }}"
                                                    data-no_kriteria="{{ $latestDokumen->no_kriteria }}"
                                                    data-content_html="{{ htmlspecialchars($latestDokumen->content_html) }}">
                                                    <td>{{ $latestDokumen->no_kriteria }}</td>
                                                    <td>{{ $latestDokumen->judul }}</td>
                                                    <td>{{ $latestDokumen->versi }}</td>
                                                    <td>
                                                        @php
                                                            $badgeClass = [
                                                                'tervalidasi' => 'badge-success',
                                                                'revisi' => 'badge-warning',
                                                                'perlu validasi' => 'badge-info',
                                                                '' => 'badge-secondary',
                                                            ];
                                                        @endphp
                                                        <span
                                                            class="badge p-2 {{ $badgeClass[$latestDokumen->status] ?? 'badge-secondary' }}">
                                                            {{ strtoupper($latestDokumen->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $latestDokumen->created_at->format('d-m-Y H:i') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    @endif
                                </div>
                            </div>

                            {{-- CARD 2: Daftar Dokumen Pendukung --}}
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h3 class="card-title mb-0 text-white">Daftar Dokumen Pendukung</h3>
                                        @if ($latestDokumen)
                                            <button class="btnTambahDokumenPendukung btn btn-custom-blue">
                                                <i class="fas fa-plus me-2"></i> Tambah Data
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if ($latestDokumen)
                                        <div class="table-responsive">
                                            {{ $dataTable->table([
                                                'id' => 'dokumen-pendukung-table-' . $kategori,
                                                'class' => 'table table-hover table-bordered table-striped dokumen-pendukung-table',
                                                'style' => 'width:100%',
                                                'data-kategori' => $kategori,
                                            ]) }}
                                        </div>
                                    @else
                                        <p class="mb-0">Tidak ada data dokumen pendukung.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Modal --}}
                            <div id="myModal-{{ $kategori }}" class="modal fade" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <!-- Konten modal akan diisi secara dinamis -->
                                    </div>
                                </div>
                            </div>

                            {{-- CARD 3: Form Edit Isi Dokumen --}}
                            @if ($latestDokumen)
                                <form id="dokumenForm"
                                    action="{{ route('dokumen_kriteria.update', ['id' => $latestDokumen->id_dokumen_kriteria]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" id="dokumen_id" name="dokumen_id"
                                        value="{{ $latestDokumen->id_dokumen_kriteria }}">

                                    <div class="card shadow-sm mb-4">
                                        <div class="card-header bg-primary border-bottom">
                                            <h3 class="card-title mb-0">Edit Isi Dokumen</h3>
                                        </div>
                                        <div class="card-body">
                                            @if ($latestDokumen->status === 'tervalidasi')
                                                <div class="alert alert-danger mb-3 text-center">
                                                    Dokumen yang sudah tervalidasi tidak dapat diedit.
                                                </div>
                                            @endif
                                            <div class="form-group mb-3">
                                                <div class="word-like-editor">
                                                    <div class="editor-noneditable-area"
                                                        style="background-color: transparent;">
                                                        <textarea id="open-source-plugins" name="content_html">{!! old('content_html', $latestDokumen->content_html) !!}</textarea>
                                                    </div>
                                                    <div class="editor-noneditable-background"></div>
                                                </div>
                                            </div>
                                            <div class="px-3 pb-3">
                                                <button type="submit" name="action" value="save"
                                                    class="btn btn-primary">
                                                    <i class="fas fa-save me-2"></i> Simpan
                                                </button>
                                                <button type="submit" name="action" value="submit"
                                                    class="btn btn-success ms-2">
                                                    <i class="fas fa-paper-plane me-2"></i> Submit
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-info">Tidak ada dokumen untuk diedit.</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @push('css')
        <style>
            /* Container utama editor */
            .word-like-editor {
                display: flex;
                flex-direction: column;
                width: 100%;
                background-color: transparent;
                position: relative;
            }

            /* Toolbar full width dan sticky */
            .word-like-editor .tox-tinymce {
                border-radius: 4px 4px 0 0 !important;
                border-bottom: none !important;
                position: sticky;
                top: 0;
                z-index: 10;
                background-color: white !important;
            }

            /* Target iframe khusus untuk menghilangkan background ganda */
            .word-like-editor .tox-tinymce-aux {
                background-color: transparent !important;
            }

            /* Target iframe utama */
            .word-like-editor iframe {
                background-color: transparent !important;
                border: none !important;
            }

            /* Body editor disesuaikan dengan ukuran A4 */
            .editor-a4-body {
                width: 794px !important;
                min-height: 1123px !important;
                margin: 20px auto !important;
                padding: 40px !important;
                background: white !important;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }

            /* Untuk print */
            @media print {
                .editor-a4-body {
                    box-shadow: none !important;
                    padding: 20px 40px 20px 40px !important;
                    /* Add some padding for print */
                    margin: 0 auto !important;
                    width: 794px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        @php
            $dokumenForSelectedKategori = $dokumenGrouped->get($selectedKategori, collect());
            $latestDokumen = $dokumenForSelectedKategori->first();
        @endphp
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            // Show SweetAlert2 for Laravel session flash messages
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('swal_error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: @json(session('swal_error')),
                        timer: 3000,
                        showConfirmButton: false
                    });
                @endif

                // Store status per kategori in a JS object
                var statusPerKategori = {
                    @foreach ($kategoriList as $kategori)
                        '{{ $kategori }}': '{{ $dokumenGrouped->get($kategori)->first()->status ?? '' }}',
                    @endforeach
                };

                function updateEditorAndButtons(kategori) {
                    var status = statusPerKategori[kategori] || '';
                    if (status === 'tervalidasi') {
                        // Disable TinyMCE editor
                        if (tinymce.get('open-source-plugins')) {
                            tinymce.get('open-source-plugins').mode.set('readonly');
                        }
                        // Disable buttons
                        document.querySelectorAll('#dokumenForm button[type="submit"]').forEach(function(btn) {
                            btn.disabled = true;
                        });
                    } else {
                        // Enable TinyMCE editor
                        if (tinymce.get('open-source-plugins')) {
                            tinymce.get('open-source-plugins').mode.set('design');
                        }
                        // Enable buttons
                        document.querySelectorAll('#dokumenForm button[type="submit"]').forEach(function(btn) {
                            btn.disabled = false;
                        });

                        // Remove alert if exists
                        var alertDiv = document.getElementById('tervalidasi-alert');
                        if (alertDiv) {
                            alertDiv.remove();
                        }
                    }
                }

                // Initial update for the selected kategori
                updateEditorAndButtons('{{ $selectedKategori }}');

                // Update on tab change
                $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var kategori = $(e.target).attr('href').replace('#custom-content-below-', '');
                    updateEditorAndButtons(kategori);
                });
            });

            // Initialize all dokumen-pendukung DataTables with kategori parameter
            function initDokumenPendukungTables() {
                $('.dokumen-pendukung-table').each(function() {
                    var tableId = $(this).attr('id');
                    var kategori = $(this).data('kategori');

                    if ($.fn.DataTable.isDataTable('#' + tableId)) {
                        $('#' + tableId).DataTable().destroy();
                    }

                    window.LaravelDataTables = window.LaravelDataTables || {};
                    window.LaravelDataTables[tableId] = $('#' + tableId).DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('dokumen_kriteria.index') }}',
                            data: {
                                kategori: kategori,
                                no_kriteria: '{{ $selectedKategori }}'
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'nama_file',
                                name: 'nama_file'
                            },
                            {
                                data: 'keterangan',
                                name: 'keterangan'
                            },
                            {
                                data: 'user_full_name',
                                name: 'user_full_name'
                            },
                            {
                                data: 'aksi',
                                name: 'aksi',
                                orderable: false,
                                searchable: false
                            }
                        ],
                        order: [
                            [1, 'asc']
                        ],
                        dom: 'Blfrtip',
                        buttons: [
                            'excel', 'csv', 'pdf', 'print', 'reset', 'reload'
                        ],
                        select: {
                            style: 'single'
                        }
                    });
                });
            }

            $(document).ready(function() {
                initDokumenPendukungTables();

                // Reload DataTable on tab change
                $('button[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                    var targetData = $(e.target).data('target');
                    if (typeof targetData === 'string') {
                        var kategori = targetData.replace('#content-', '');
                        var tableId = 'dokumen-pendukung-table-' + kategori;
                        if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                            window.LaravelDataTables[tableId].ajax.reload();
                        }
                    } else {
                        console.warn(
                            'Tab shown event target data-target attribute is undefined or not a string.');
                    }
                });

                // Handle upload button click to open modal with correct kategori
                $(document).on('click', '.btnTambahDokumenPendukung', function() {
                    var activeTabButton = $('ul#kategoriTabs a.nav-link.active');
                    console.log('Active tab button count:', activeTabButton.length);
                    var kategori = '';
                    if (activeTabButton.length > 0) {
                        var href = activeTabButton.attr('href'); // e.g. #custom-content-below-pengendalian
                        console.log('Active tab href:', href);
                        if (typeof href === 'string' && href.length > 0) {
                            kategori = href.replace('#custom-content-below-', '');
                        }
                    }
                    if (!kategori) {
                        // fallback to first kategori tab if kategori is empty
                        var firstTabHref = $('ul#kategoriTabs a.nav-link').first().attr('href');
                        console.log('First tab href:', firstTabHref);
                        if (typeof firstTabHref === 'string' && firstTabHref.length > 0) {
                            kategori = firstTabHref.replace('#custom-content-below-', '');
                        }
                    }
                    console.log('Opening create modal with kategori:', kategori);
                    var no_kriteria = $('div.tab-pane.show.active table tbody tr').data('no_kriteria');
                    if (!no_kriteria) {
                        console.warn('no_kriteria is undefined or null');
                    }
                    var url = '{{ route('dokumen_kriteria.create_ajax', [], false) }}?no_kriteria=' +
                        encodeURIComponent(no_kriteria) + '&kategori=' + encodeURIComponent(kategori);
                    modalAction(url, kategori);
                });
            });

            function modalAction(url, kategori) {
                $.get(url)
                    .done(function(response) {
                        $('#myModal-' + kategori + ' .modal-content').html(response);
                        $('#myModal-' + kategori).modal('show');
                    })
                    .fail(function(xhr) {
                        Swal.fire('Error!', 'Gagal memuat form: ' + xhr.statusText, 'error');
                    });
            }

            // Use event delegation for form submissions to avoid multiple bindings
            $(document).on('submit', '#formCreateDokumenPendukung, #formEditDokumenPendukung', function(e) {
                e.preventDefault();
                var form = $(this);
                var kategori = form.closest('.modal').attr('id').replace('myModal-', '');
                var formData = new FormData(form[0]);
                console.log('Submitting form with kategori:', kategori);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        $('#myModal-' + kategori).modal('hide');
                        // Reload all dokumen-pendukung DataTables to ensure data refresh
                        if (window.LaravelDataTables) {
                            Object.keys(window.LaravelDataTables).forEach(function(tableId) {
                                if (tableId.startsWith('dokumen-pendukung-table-')) {
                                    window.LaravelDataTables[tableId].ajax.reload();
                                }
                            });
                        }
                        if (res.alert && res.message) {
                            Swal.fire({
                                icon: res.alert,
                                title: res.alert === 'success' ? 'Sukses' : 'Error',
                                text: res.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        $('#myModal-' + kategori).modal('hide');
                        var activeTabButton = $('ul#kategoriTabs button.nav-link.active');
                        var activeKategori = activeTabButton.attr('id');
                        if (typeof activeKategori === 'string') {
                            activeKategori = activeKategori.replace('tab-', '');
                            var activeTableId = 'dokumen-pendukung-table-' + activeKategori;
                            if (window.LaravelDataTables && window.LaravelDataTables[activeTableId]) {
                                window.LaravelDataTables[activeTableId].ajax.reload();
                            }
                        } else {
                            console.warn('Active tab button id attribute is undefined or not a string.');
                        }
                        if (xhr.responseJSON && xhr.responseJSON.alert && xhr.responseJSON.message) {
                            Swal.fire({
                                icon: xhr.responseJSON.alert,
                                title: xhr.responseJSON.alert === 'success' ? 'Sukses' : 'Error',
                                text: xhr.responseJSON.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('Error!',
                                'Gagal menyimpan data karena duplikat Kode DokumenPendukung.',
                                'error');
                        }
                    }
                });
            });

            // Use event delegation for delete form submission
            $(document).on('submit', '#formDeleteDokumenPendukung', function(e) {
                e.preventDefault();
                var form = $(this);
                var kategori = form.closest('.modal').attr('id').replace('myModal-', '');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#myModal-' + kategori).modal('hide');
                        // Reload all dokumen-pendukung DataTables to ensure data refresh
                        if (window.LaravelDataTables) {
                            Object.keys(window.LaravelDataTables).forEach(function(tableId) {
                                if (tableId.startsWith('dokumen-pendukung-table-')) {
                                    window.LaravelDataTables[tableId].ajax.reload();
                                }
                            });
                        }
                        // Prevent duplicate alerts by clearing any existing Swal timers
                        if (window.Swal && Swal.isVisible()) {
                            Swal.close();
                        }
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Dokumen Pendukung berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Tidak dapat menghapus dokumen pendukung.'
                        });
                    }
                });
            });
        </script>

        <script>
            function copyPath(path) {
                if (!path) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Path file tidak tersedia.'
                    });
                    return;
                }
                path = path.trim();
                const fullPath = window.location.origin + '/storage/dokumen_pendukung/' + path;
                navigator.clipboard.writeText(fullPath).then(function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Path file berhasil disalin: ' + fullPath,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal menyalin path file: ' + err
                    });
                });
            }
        </script>

        {{-- TinyMCE CDN with your API Key --}}
        <script src="https://cdn.tiny.cloud/1/cpg5v56ciwrj3mu95246ce8a9v1nbrxyk4aps3vblxe7pwkd/tinymce/6/tinymce.min.js"
            referrerpolicy="origin"></script>

        <script>
            const useDarkMode = window.matchMedia('(prefers-color-scheme: light)').matches;
            const isSmallScreen = window.matchMedia('(max-width: 1023.5px)').matches;

            tinymce.init({
                selector: 'textarea#open-source-plugins',
                plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
                editimage_cors_hosts: ['picsum.photos'],
                menubar: 'file edit view insert format tools table help',
                menu: {
                    file: {
                        title: 'File',
                        items: 'restore save print preview | importcss'
                    },
                    edit: {
                        title: 'Edit',
                        items: 'undo redo | cut copy paste pastetext | selectall'
                    },
                    view: {
                        title: 'View',
                        items: 'code visualaid visualchars visualblocks | spellchecker | preview fullscreen'
                    },
                    insert: {
                        title: 'Insert',
                        items: 'image link media template codesample inserttable | charmap emoticons hr'
                    },
                    format: {
                        title: 'Format',
                        items: 'bold italic underline strikethrough superscript subscript codeformat | removeformat'
                    },
                    tools: {
                        title: 'Tools',
                        items: 'spellchecker spellcheckerlanguage | a11ycheck code'
                    },
                    table: {
                        title: 'Table',
                        items: 'inserttable | cell row column | tableprops deletetable'
                    },
                    help: {
                        title: 'Help',
                        items: 'help'
                    }
                },
                toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
                width: '100%', // Toolbar full width
                height: 1123, // Tinggi A4
                autosave_ask_before_unload: true,
                image_advtab: true,
                content_css: useDarkMode ? 'dark' : 'default',
                skin: useDarkMode ? 'oxide-dark' : 'oxide',
                content_style: 'body { margin: 0; padding: 0; font-family: "Times New Roman", Times, serif; font-size: 12pt; line-height: 1.5; }',

                setup: function(editor) {
                    editor.on('init', function() {
                        // Dapatkan iframe editor
                        const iframe = editor.getContainer().querySelector('iframe');

                        // Set styling untuk iframe dan body-nya
                        iframe.style.width = '100%';
                        iframe.style.height = '1123px';
                        iframe.style.background = 'transparent'; // Ubah background iframe jadi transparan
                        iframe.style.border = 'none';

                        // Set styling untuk konten editor
                        const body = iframe.contentDocument.body;
                        body.style.width = '100%';
                        body.style.minHeight = '1123px';
                        body.style.margin = '0';
                        body.style.padding =
                            '40px 0 100px 0'; // Tambah padding top untuk jarak dari toolbar agar teks tidak tertutup toolbar
                        body.style.backgroundColor = '#e3e4e4'; // Ubah body background jadi gelap

                        // Hapus elemen background gelap tambahan jika ada
                        const existingDarkBackground = iframe.contentDocument.querySelector(
                            '.dark-background');
                        if (existingDarkBackground) {
                            existingDarkBackground.remove();
                        }

                        // Set styling untuk kertas A4 di dalam body
                        // Check if paper div already exists to avoid double paper
                        let paper = iframe.contentDocument.querySelector('.editor-a4-body');
                        if (!paper) {
                            paper = iframe.contentDocument.createElement('div');
                            paper.style.position = 'relative';
                            paper.style.zIndex = '2';
                            paper.style.width = '794px';
                            paper.style.minHeight = '1123px';
                            paper.style.margin = '0 auto';
                            paper.style.padding = '20px'; /* Add padding inside paper for content spacing */
                            paper.style.backgroundColor = 'white';
                            paper.style.boxShadow = '0 0 5px rgba(0, 0, 0, 0.1)';
                            paper.className = 'editor-a4-body';

                            // Pindahkan semua child body ke dalam paper
                            while (body.firstChild) {
                                paper.appendChild(body.firstChild);
                            }
                            body.appendChild(paper);
                        }
                    });

                    // Add event listener to enforce saving alignment style on images
                    editor.on('ExecCommand', function(e) {
                        if (e.command === 'JustifyCenter' || e.command === 'JustifyLeft' || e.command ===
                            'JustifyRight') {
                            const selectedNode = editor.selection.getNode();
                            if (selectedNode.nodeName === 'IMG') {
                                // Apply text-align style to the parent <p> or container
                                const parent = selectedNode.parentNode;
                                if (parent && parent.nodeName === 'P') {
                                    if (e.command === 'JustifyCenter') {
                                        parent.style.textAlign = 'center';
                                    } else if (e.command === 'JustifyLeft') {
                                        parent.style.textAlign = 'left';
                                    } else if (e.command === 'JustifyRight') {
                                        parent.style.textAlign = 'right';
                                    }
                                }
                            }
                        }
                    });
                },
                autosave_ask_before_unload: true,
                autosave_interval: '30s',
                autosave_prefix: '{path}{query}-{id}-',
                autosave_restore_when_empty: false,
                autosave_retention: '2m',
                image_advtab: true,
                link_list: [{
                        title: 'My page 1',
                        value: 'https://www.tiny.cloud'
                    },
                    {
                        title: 'My page 2',
                        value: 'http://www.moxiecode.com'
                    }
                ],
                image_list: [{
                        title: 'My page 1',
                        value: 'https://www.tiny.cloud'
                    },
                    {
                        title: 'My page 2',
                        value: 'http://www.moxiecode.com'
                    }
                ],
                image_class_list: [{
                        title: 'None',
                        value: ''
                    },
                    {
                        title: 'Some class',
                        value: 'class-name'
                    }
                ],
                importcss_append: true,
                file_picker_callback: (callback, value, meta) => {
                    /* Provide file and text for the link dialog */
                    if (meta.filetype === 'file') {
                        callback('https://www.google.com/logos/google.jpg', {
                            text: 'My text'
                        });
                    }

                    /* Provide image and alt text for the image dialog */
                    if (meta.filetype === 'image') {
                        callback('https://www.google.com/logos/google.jpg', {
                            alt: 'My alt text'
                        });
                    }

                    /* Provide alternative source and posted for the media dialog */
                    if (meta.filetype === 'media') {
                        callback('movie.mp4', {
                            source2: 'alt.ogg',
                            poster: 'https://www.google.com/logos/google.jpg'
                        });
                    }
                },
                height: 1123,
                image_caption: true,
                quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
                noneditable_class: 'mceNonEditable',
                toolbar_mode: 'sliding',
                contextmenu: 'link image table',
                skin: useDarkMode ? 'oxide-dark' : 'oxide',
                content_css: useDarkMode ? 'dark' : 'default',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
                automatic_uploads: true,
                images_upload_url: '{{ route('dokumen_kriteria.upload.image') }}',
                paste_data_images: true,

                images_upload_handler: function(blobInfo) {
                    return new Promise(function(resolve, reject) {
                        let xhr = new XMLHttpRequest();
                        xhr.open('POST', '{{ route('dokumen_kriteria.upload.image') }}');
                        xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');

                        let formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());

                        xhr.onload = function() {
                            if (xhr.status !== 200) {
                                reject('Upload gagal: ' + xhr.status);
                                return;
                            }
                            const json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject('Upload gagal');
                                return;
                            }
                            resolve(json.location); // url disimpan di content_html
                        };

                        xhr.onerror = function() {
                            reject('Upload gagal: error jaringan.');
                        };

                        xhr.send(formData);
                    });
                }
            });
        </script>
    @endpush
@endsection
