<footer id="footer" class="footer light-background">

    <div class="footer-top">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-6 col-md-6 footer-about">
                    <a href="{{ url('/') }}" class="logo d-flex align-items-center">
                        <img src="{{ asset('img/akresa-logo.svg') }}" alt="Akresa Logo" class="me-2">
                        <span class="sitename">Akresa</span>
                    </a>
                    <div class="footer-contact pt-3">
                        <p><strong>Alamat:</strong><span> Jl. Soekarno Hatta No.9</span></p>
                        <p><strong>Telepon:</strong><span> (0341) 404424</span></p>
                        <p><strong>Email:</strong><span> humas@polinema.ac.id</span></p>
                    </div>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Navigation</h4>
                    <ul>
                <li><a href="{{ request()->routeIs('kriteria.showDokumenPendukung') ? url('/') : '#hero' }}">Home</a></li>
                <li><a href="{{ request()->routeIs('kriteria.showDokumenPendukung') ? url('/') : '#about' }}">About us</a></li>
                <li><a href="{{ request()->routeIs('kriteria.showDokumenPendukung') ? url('/') : '#contact' }}">Contact</a></li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Kriteria</h4>
                    <ul>
                        @foreach($kriteriaList as $kriteria)
                            <li><a href="{{ route('kriteria.showDokumenPendukung', ['no_kriteria' => $kriteria->no_kriteria]) }}">Kriteria {{ $kriteria->no_kriteria }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-lg-2 col-md-3 footer-links">
                    <h4>Denah Gedung</h4>
                    <ul>
                        <li><a href="https://my.matterport.com/show/?m=xufa7UrDLJe" target="_blank">Lantai 5</a></li>
                        <li><a href="https://my.matterport.com/show/?m=Fj8fbnjLjQq" target="_blank">Lantai 6</a></li>
                        <li><a href="https://my.matterport.com/show/?m=fAgiViGeZaB" target="_blank">Lantai 7</a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="copyright text-center">
        <div
            class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">

            <div class="d-flex flex-column align-items-center align-items-lg-start">
                <div>
                    © Copyright <strong><span>Akresa</span></strong>. All Rights Reserved
                </div>
            </div>

        </div>
    </div>

</footer>
