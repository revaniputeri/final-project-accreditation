<div style="font-size: 20px; margin: 0px;">
    <h2>Daftar Isi</h2>
    <ul>
        @foreach($kriteriaList as $kriteria)
            <li><a href="#kriteria-{{ $kriteria['no_kriteria'] }}" style="text-decoration: none; color: black;">{{ $kriteria['judul'] }}</a></li>
        @endforeach
    </ul>
</div>
