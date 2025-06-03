<!DOCTYPE html>
<html>
<head>
    <title>Data Kriteria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: right;
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Data Kriteria</h2>
        <p>Tanggal: {{ date('d M Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No Kriteria</th>
                <th>Nama User</th>
                <th>Jumlah Dokumen Kriteria</th>
                <th>Jumlah Dokumen Pendukung</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kriteria as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->no_kriteria }}</td>
                <td>{{ $item->user->username ?? '-' }}</td>
                <td>{{ $item->dokumenKriteria->count() }}</td>
                <td>{{ $item->dokumenPendukung->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ date('d M Y H:i:s') }}</p>
    </div>
</body>
</html>