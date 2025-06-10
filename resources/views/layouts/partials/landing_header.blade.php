<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container position-relative d-flex align-items-center">

        <a href="{{ url('/') }}" class="logo d-flex align-items-center me-auto">
            <img src="{{ asset('assets/img/akresa-logo.svg') }}" alt="">
            <h1 class="sitename">Akresa</h1>
        </a>

        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ url('/#about') }}" class="{{ request()->is('/#about') ? 'active' : '' }}">About</a></li>
                <li><a href="{{ url('/#contact') }}"
                        class="{{ request()->is('/#contact') ? 'active' : '' }}">Contact</a></li>
                <li class="dropdown {{ request()->routeIs('kriteria.showDokumenPendukung') ? 'active' : '' }}">
                    <a href="#"><span>Kriteria</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        @foreach ($kriteriaList as $kriteria)
                            <li>
                                <a href="{{ route('kriteria.showDokumenPendukung', ['no_kriteria' => $kriteria->no_kriteria]) }}"
                                    class="{{ request()->routeIs('kriteria.showDokumenPendukung') && request()->no_kriteria == $kriteria->no_kriteria ? 'active' : '' }}">
                                    Kriteria {{ $kriteria->no_kriteria }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="dropdown">
                    <a href="#"><span>Denah Gedung</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                    <ul>
                        <li><a href="https://my.matterport.com/show/?m=xufa7UrDLJe" target="_blank">Lantai 5</a></li>
                        <li><a href="https://my.matterport.com/show/?m=Fj8fbnjLjQq" target="_blank">Lantai 6</a></li>
                        <li><a href="https://my.matterport.com/show/?m=fAgiViGeZaB" target="_blank">Lantai 7</a></li>
                    </ul>
                </li>
                @if(Auth::check())
                    <a href="/dashboard" class="btn btn-primary btn-get-started ms-3" style="color: white;">Dashboard</a>
                @else
                    <a href="/login" class="btn btn-primary btn-get-started ms-3" style="color: white;">Login</a>
                @endif
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

    </div>
</header>
