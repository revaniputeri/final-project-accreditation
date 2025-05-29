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
    {{-- Display existing dokumen data in read-only table --}}
    <div class="card mb-4">
        <div class="card-header">
            <h3>Daftar Dokumen Kriteria</h3>
        </div>
        <div class="card-body">
            @if ($dokumen->isEmpty())
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
                        @foreach ($dokumen as $index => $item)
                            <tr data-id="{{ $item->id_dokumen_kriteria }}"
                                data-content_html="{{ htmlspecialchars($item->content_html) }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->no_kriteria }}</td>
                                <td>{{ $item->judul }}</td>
                                <td>{{ $item->versi }}</td>
                                <td>{{ $item->status }}</td>
                                <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Form to input new dokumen kriteria --}}
    <form id="dokumenForm" action="{{ route('dokumen_kriteria.store') }}" method="POST">
        @csrf
        @method('POST')
        <input type="hidden" id="dokumen_id" name="dokumen_id" value="">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="content_html">Isi Dokumen</label>
                    <textarea id="open-source-plugins" name="content_html"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" id="cancelEdit" class="btn btn-secondary" style="display:none;">Batal Edit</button>
            </div>
        </div>
    </form>
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

            images_upload_handler: function(blobInfo, success, failure) {
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('dokumen_kriteria.upload.image') }}');
                xhr.setRequestHeader('X-CSRF-Token', '{{ csrf_token() }}');

                let formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());

                xhr.onload = function() {
                    if (xhr.status !== 200) {
                        return failure('Upload gagal: ' + xhr.status);
                    }
                    const json = JSON.parse(xhr.responseText);
                    if (!json || typeof json.location !== 'string') {
                        return failure('Upload gagal');
                    }
                    success(json.location); // url disimpan di content_html
                };

                xhr.send(formData);
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dokumenForm = document.getElementById('dokumenForm');
            const dokumenIdInput = document.getElementById('dokumen_id');
            const noKriteriaInput = document.getElementById('no_kriteria');
            const judulInput = document.getElementById('judul');
            const cancelEditBtn = document.getElementById('cancelEdit');

            // Add click event to table rows for inline editing
            document.querySelectorAll('table tbody tr').forEach(row => {
                row.addEventListener('click', () => {
                    const cells = row.querySelectorAll('td');
                    const id = row.getAttribute('data-id');
                    const noKriteria = cells[1].innerText.trim();
                    const judul = cells[2].innerText.trim();

                    // Fetch content_html via data attribute
                    const contentHtml = row.getAttribute('data-content_html');

                    // Set form to update mode
                    dokumenForm.action = "/dokumen_kriteria/update/" + id;
                    dokumenForm.method = 'POST';

                    // Add hidden _method input for PUT
                    let methodInput = dokumenForm.querySelector('input[name="_method"]');
                    if (!methodInput) {
                        methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        dokumenForm.appendChild(methodInput);
                    }
                    methodInput.value = 'PUT';

                    dokumenIdInput.value = id;
                    noKriteriaInput.value = noKriteria;
                    judulInput.value = judul;

                    // Load content_html into TinyMCE
                    if (tinymce.get('open-source-plugins')) {
                        tinymce.get('open-source-plugins').setContent(contentHtml);
                    }

                    cancelEditBtn.style.display = 'inline-block';
                });
            });

            cancelEditBtn.addEventListener('click', () => {
                // Reset form to create mode
                dokumenForm.action = "{{ route('dokumen_kriteria.store') }}";
                dokumenForm.method = 'POST';

                let methodInput = dokumenForm.querySelector('input[name="_method"]');
                if (methodInput) {
                    methodInput.remove();
                }

                dokumenIdInput.value = '';
                noKriteriaInput.value = '';
                judulInput.value = '';

                if (tinymce.get('open-source-plugins')) {
                    tinymce.get('open-source-plugins').setContent('');
                }

                cancelEditBtn.style.display = 'none';
            });
        });
    </script>
@endpush
