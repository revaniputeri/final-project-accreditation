<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link d-flex align-items-center justify-content-center">
        <img src="{{ asset('img/akresa-logo-text-horizontal.svg') }}" alt="AKRESA Logo" class="brand-image img-fluid mx-auto d-block">
    </a>
    <div class="sidebar">
        <!-- Sidebar Search Form -->
        <div class="form-inline mt-3 mb-3" data-widget="sidebar-search">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" role="menu">
                @php
                    $role = strtoupper($userRole ?? '');
                    $isAdm = $role === 'ADM';
                    $isDos = $role === 'DOS';
                    $isVal = $role === 'VAL';
                    $isDir = $role === 'DIR';
                    $isAng = str_starts_with($role, 'ANG');
                @endphp

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ url('/dashboard') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- ADM: Manage Menu -->
                @if ($isAdm)
                    <div class="dropdown-divider"></div>
                    <li class="nav-header">Manage</li>

                    <li class="nav-item">
                        <a href="{{ url('/manage-user') }}"
                            class="nav-link {{ request()->is('manage-user') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>User</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/manage-level') }}"
                            class="nav-link {{ request()->is('manage-level') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-layer-group"></i>
                            <p>Level</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/manage-kriteria') }}"
                            class="nav-link {{ request()->is('manage-kriteria') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-list-ul"></i>
                            <p>Kriteria</p>
                        </a>
                    </li>
                @endif

                <!-- ADM, DOS, ANG: Portofolio Dosen -->
                @if ($isAdm || $isDos || $isAng)
                    <div class="dropdown-divider"></div>

                    <!-- Header Portofolio Dosen -->
                    <li class="nav-header">Portofolio Dosen</li>

                    <li class="nav-item">
                        <a href="{{ url('/portofolio/sertifikasi') }}"
                            class="nav-link {{ request()->is('portofolio/sertifikasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>Sertifikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/kegiatan') }}"
                            class="nav-link {{ request()->is('portofolio/kegiatan') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/prestasi') }}"
                            class="nav-link {{ request()->is('portofolio/prestasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-award"></i>
                            <p>Prestasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/organisasi') }}"
                            class="nav-link {{ request()->is('portofolio/organisasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Organisasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/publikasi') }}"
                            class="nav-link {{ request()->is('portofolio/publikasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Publikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/penelitian') }}"
                            class="nav-link {{ request()->is('portofolio/penelitian') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-laptop-code"></i>
                            <p>Penelitian</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/karya-buku') }}"
                            class="nav-link {{ request()->is('portofolio/karya-buku') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Karya Buku</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/hki') }}"
                            class="nav-link {{ request()->is('portofolio/hki') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-copyright"></i>
                            <p>HKI</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/pengabdian') }}"
                            class="nav-link {{ request()->is('portofolio/pengabdian') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-heart"></i>
                            <p>Pengabdian Masyarakat</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/portofolio/profesi') }}"
                            class="nav-link {{ request()->is('portofolio/profesi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Profesi</p>
                        </a>
                    </li>
                @endif

                <!-- ADM & ANG: Kriteria -->
                @if ($isAng)
                    <div class="dropdown-divider"></div>

                    <!-- Header Kriteria -->
                    <li class="nav-header">Kriteria</li>

                    <li class="nav-item">
                        <a href="{{ url('/dokumen_kriteria') }}"
                            class="nav-link {{ request()->is('dokumen_kriteria') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-eye"></i>
                            <p>Dokumen Kriteria</p>
                        </a>
                    </li>
                @endif

                <!-- VAL: Validasi -->
                @if ($isAdm || $isVal)
                    <div class="dropdown-divider"></div>
                    <!-- Header Validasi Kriteria -->
                    <li class="nav-header">Validasi Kriteria</li>

                    <li class="nav-item">
                        <a href="{{ url('/validasi') }}"
                            class="nav-link {{ request()->is('validasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Validasi</p>
                        </a>
                    </li>
                @endif

                <!-- DIR: Dokumen Akhir -->
                @if ($isDir || $isVal || $isAdm)
                    <div class="dropdown-divider"></div>
                    <li class="nav-header">Dokumen Akhir</li>

                    <li class="nav-item">
                        <a href="{{ url('/dokumen-akhir') }}"
                            class="nav-link {{ request()->is('dokumen-akhir') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-alt"></i>
                            <p>Dokumen Akhir</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
