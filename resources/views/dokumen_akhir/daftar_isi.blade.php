<div style="font-size: 20px; margin: 0px;">
    <h2>Daftar Isi</h2>
    <ul>
        @php
            $grouped = collect($daftarIsiList)->groupBy('no_kriteria');
        @endphp
        @foreach($grouped as $no_kriteria => $items)
            <li>
                <a href="#kriteria-{{ $no_kriteria }}" style="text-decoration: none; color:rgb(0, 0, 0)e; cursor: pointer;">
                    {{ $items->first()['judul'] }}
                </a>
                <ul style="margin-left: 5px;">
                    @foreach($items as $item)
                        <li>
                            <a href="{{ $item['link'] }}" style="text-decoration: none; color: rgb(0, 0, 0); cursor: pointer;">
                                {{ $item['kategori'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </li>
        @endforeach
    </ul>
</div>
