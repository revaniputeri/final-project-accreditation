<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">Sistem Akreditasi</span>
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
                    <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
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
                        <a href="{{ url('/p_sertifikasi') }}"
                            class="nav-link {{ request()->is('p_sertifikasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-certificate"></i>
                            <p>Sertifikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_kegiatan') }}"
                            class="nav-link {{ request()->is('p_kegiatan') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-calendar-alt"></i>
                            <p>Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_prestasi') }}"
                            class="nav-link {{ request()->is('p_prestasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-award"></i>
                            <p>Prestasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_organisasi') }}"
                            class="nav-link {{ request()->is('p_organisasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Organisasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_publikasi') }}"
                            class="nav-link {{ request()->is('p_publikasi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Publikasi</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_penelitian') }}"
                            class="nav-link {{ request()->is('p_penelitian') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-laptop-code"></i>
                            <p>Penelitian</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_karya-buku') }}"
                            class="nav-link {{ request()->is('p_karya-buku') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Karya Buku</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_hki') }}" class="nav-link {{ request()->is('p_hki') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-copyright"></i>
                            <p>HKI</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_pengabdian') }}"
                            class="nav-link {{ request()->is('p_pengabdian') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-heart"></i>
                            <p>Pengabdian Masyarakat</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/p_profesi') }}" class="nav-link {{ request()->is('p_profesi') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-tie"></i>
                            <p>Profesi</p>
                        </a>
                    </li>
                @endif

                <!-- ADM & ANG: Kriteria -->
                @if ($isAdm || $isAng)
                    <div class="dropdown-divider"></div>

                    <!-- Header Kriteria -->
                    <li class="nav-header">Kriteria</li>

                    <li class="nav-item">
                        <a href="{{ url('/kriteria/1') }}"
                            class="nav-link {{ request()->is('kriteria/1') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-eye"></i>
                            <p>Kriteria 1</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/2') }}"
                            class="nav-link {{ request()->is('kriteria/2') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-gavel"></i>
                            <p>Kriteria 2</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/3') }}"
                            class="nav-link {{ request()->is('kriteria/3') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-graduate"></i>
                            <p>Kriteria 3</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/4') }}"
                            class="nav-link {{ request()->is('kriteria/4') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Kriteria 4</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/5') }}"
                            class="nav-link {{ request()->is('kriteria/5') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Kriteria 5</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/6') }}"
                            class="nav-link {{ request()->is('kriteria/6') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-book"></i>
                            <p>Kriteria 6</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/7') }}"
                            class="nav-link {{ request()->is('kriteria/7') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-microscope"></i>
                            <p>Kriteria 7</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/8') }}"
                            class="nav-link {{ request()->is('kriteria/8') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-hand-holding-heart"></i>
                            <p>Kriteria 8</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/kriteria/9') }}"
                            class="nav-link {{ request()->is('kriteria/9') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-trophy"></i>
                            <p>Kriteria 9</p>
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