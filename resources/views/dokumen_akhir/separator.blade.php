<div id="kriteria-{{ $no_kriteria }}-{{ strtolower($kategori) }}" style="
    page-break-after: always;
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 100vh;
    text-align: center;
">
    <div style="
        position: absolute;
        top: 40%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
    ">
        <h2 style="font-weight: bold; font-size: 36px; margin: 0;">
            {{ $judul ?? '' }}
        </h2>
        @isset($kategori)
        <div style="font-weight: normal; font-size: 20px; margin-top: 10px;">
            {{ $kategori }}
        </div>
        @endisset
    </div>
</div>
