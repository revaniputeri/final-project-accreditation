<div style="font-size: 20px; margin: 0px;">
    <h2>Daftar Isi</h2>
    <ul>
        @foreach($daftarIsiList as $item)
            <li>
                <a href="{{ $item['link'] }}" style="text-decoration: none; color: rgb(0, 0, 0); cursor: pointer;">
                    {{ $item['judul'] }} ... {{ $item['kategori'] }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
