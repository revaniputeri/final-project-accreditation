@extends('layouts.app')

@section('title', 'Input Dokumen Kriteria')

@section('content_header')
    <div class="container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active">Dokumen Kriteria</li>
            </ol>
        </nav>
    </div>
@endsection

@section('content')
    @php
        $latestDokumen = $dokumen->first();
    @endphp

    {{-- Display existing dokumen data in read-only table --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3>Keterangan Dokumen Kriteria</h3>
        </div>
        <div class="card-body">
            @if (!$latestDokumen)
                <p>Tidak ada data dokumen kriteria.</p>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>No Kriteria</th>
                            <th>Judul</th>
                            <th>Versi</th>
                            <th>Status</th>
                            <th>Waktu Dibuat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-id="{{ $latestDokumen->id_dokumen_kriteria }}"
                            data-content_html="{{ htmlspecialchars($latestDokumen->content_html) }}">
                            <td>1</td>
                            <td>{{ $latestDokumen->no_kriteria }}</td>
                            <td>{{ $latestDokumen->judul }}</td>
                            <td>{{ $latestDokumen->versi }}</td>
                            <td>{{ $latestDokumen->status }}</td>
                            <td>{{ $latestDokumen->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Form to update content_html of latest dokumen --}}
    @if ($latestDokumen)
        <form id="dokumenForm" action="{{ route('dokumen_kriteria.update', ['id' => $latestDokumen->id_dokumen_kriteria]) }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" id="dokumen_id" name="dokumen_id" value="{{ $latestDokumen->id_dokumen_kriteria }}">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label for="content_html">Isi Dokumen</label>
                        <textarea id="open-source-plugins" name="content_html">{{ $latestDokumen->content_html }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </div>
        </form>
    @else
        <p>Tidak ada dokumen untuk diedit.</p>
    @endif
@endsection

@push('scripts')
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
            toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl",
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
            height: 600,
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cancelEditBtn = document.getElementById('cancelEdit');

            cancelEditBtn.style.display = 'none';
        });
    </script>
@endpush
