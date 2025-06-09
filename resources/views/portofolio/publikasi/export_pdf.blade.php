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

        .text-center {
            text-align: center;
        }

        .border-all,
        .border-all th,
        .border-all td {
            border: 1px solid;
        }
    </style>
</head>

<body>
    <h3 class="text-center">DATA PORTOFOLIO DOSEN - PUBLIKASI</h3>

    <table class="border-all">
        <thead>
            <tr>
                <th>No</th>
                {{-- <th>ID Publikasi</th> --}}
                <th>Nama Dosen</th>
                <th>Judul</th>
                <th>Tempat Publikasi</th>
                <th>Tahun Publikasi</th>
                <th>Jenis Publikasi</th>
                <th>Dana</th>
                <th>Status</th>
                <th>Sumber Data</th>
                <th>Bukti</th>
                <th>Dibuat Pada</th>
                <th>Diperbarui Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($publikasi as $key => $item)
                <tr>
                    <td class="text-center">{{ $key + 1 }}</td>
                    {{-- <td>{{ $item['id_publikasi'] }}</td> --}}
                    <td>{{ $item['nama_dosen'] }}</td>
                    <td>{{ $item['judul'] }}</td>
                    <td>{{ $item['tempat_publikasi'] }}</td>
                    <td>{{ $item['tahun_publikasi'] }}</td>
                    <td>{{ ucfirst($item['jenis_publikasi']) }}</td>
                    <td>{{ $item['dana'] }}</td>
                    <td>{{ $item['status'] }}</td>
                    <td>{{ $item['sumber_data'] }}</td>
                    <td>
                        @if ($item['bukti'])
                            <a href="{{ url('storage/portofolio/publikasi/' . $item['bukti']) }}" target="_blank">Lihat File</a>
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
                <span>Dikembangkan oleh : <strong>Tim Pengembang Sistem Akreditasi</strong></span>
                <br />
                <span>Diunduh pada : {{ now()->format('d M Y H:i:s') }}</span>
            </td>
        </tr>
    </table>

</body>

</html>
