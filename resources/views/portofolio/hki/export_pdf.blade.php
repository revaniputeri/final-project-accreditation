<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            margin: 6px 20px 5px 20px;
            line-height: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td,
        th {
            padding: 4px 3px;
        }

        th {
            text-align: left;
        }

        .d-block {
            display: block;
        }

        img.image {
            width: auto;
            height: 80px;
            max-width: 150px;
            max-height: 150px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .p-1 {
            padding: 5px 1px 5px 1px;
        }

        .font-10 {
            font-size: 10pt;
        }

        .font-11 {
            font-size: 11pt;
        }

        .font-12 {
            font-size: 12pt;
        }

        .font-13 {
            font-size: 13pt;
        }

        .border-bottom-header {
            border-bottom: 1px solid;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <table class="border-bottom-header">
        <tr>
            <td width="15%" class="text-center">
                <img src="{{ public_path('img/polinema-pw.jpg') }}" alt="Logo Polinema" class="image">
            </td>
            <td width="85%">
                <span class="text-center d-block font-11 font-bold mb-1">KEMENTERIAN PENDIDIKAN, KEBUDAYAAN, RISET, DAN
                    TEKNOLOGI</span>
                <span class="text-center d-block font-13 font-bold mb-1">POLITEKNIK NEGERI MALANG</span>
                <span class="text-center d-block font-10">Jl. Soekarno-Hatta No. 9 Malang 65141</span>
                <span class="text-center d-block font-10">Telepon (0341) 404424 Pes. 101-105, 0341-404420, Fax. (0341)
                    404420</span>
                <span class="text-center d-block font-10">Laman: www.polinema.ac.id</span>
            </td>
            <td width="15%" class="text-center">
                <img src="{{ public_path('img/jti-pw.jpg') }}" alt="Logo JTI" class="image">
            </td>
        </tr>
    </table>

    <h3 class="text-center">DATA PORTOFOLIO DOSEN - HKI</h3>

    <table class="border-all">
        <thead>
            <tr>
                <th>No</th>
                {{-- <th>ID HKI</th> --}}
                <th>Nama Dosen</th>
                <th>Judul</th>
                <th>Tahun</th>
                <th>Skema</th>
                <th>Nomor HKI</th>
                <th>Melibatkan Mahasiswa S2</th>
                <th>Status</th>
                <th>Sumber Data</th>
                <th>Bukti</th>
                <th>Dibuat Pada</th>
                <th>Diperbarui Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hki as $key => $item)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    {{-- <td>{{ $item['id_hki'] }}</td> --}}
                    <td>{{ $item['nama_dosen'] }}</td>
                    <td>{{ $item['judul'] }}</td>
                    <td>{{ $item['tahun'] }}</td>
                    <td>{{ $item['skema'] }}</td>
                    <td>{{ $item['nomor'] }}</td>
                    <td>{{ $item['melibatkan_mahasiswa_s2'] ? 'Ya' : 'Tidak' }}</td>
                    <td>{{ $item['status'] }}</td>
                    <td>{{ $item['sumber_data'] }}</td>
                    <td>
                        @if ($item['bukti'])
                            <a href="{{ url('storage/portofolio/hki' . $item['bukti']) }}" target="_blank">Lihat File</a>
                        @else
                            Tidak ada file
                        @endif
                    </td>
                    <td>{{ $item['created_at'] }}</td>
                    <td>{{ $item['updated_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="border-all" style="margin-top: 10px;">
        <tr>
            <td colspan="5" class="text-center">
                <span class="font-10">Dikembangkan oleh : <strong>Tim Pengembang Sistem Akreditasi</strong></span>
                <br />
                <span class="font-10">Diunduh pada : {{ now()->format('d M Y H:i:s') }}</span>
            </td>
        </tr>
    </table>

</body>

</html>