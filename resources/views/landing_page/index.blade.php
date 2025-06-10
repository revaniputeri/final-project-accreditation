@extends('layouts.landing')

@section('title', 'Akresa')

@section('content')
    <!-- Hero Section -->
    <section id="hero" class="hero section">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-7 order-2 order-lg-1 d-flex flex-column justify-content-center">
                    <h1>Selamat Datang di Akresa</h1>
                    <p><strong class="text-primary">Akreditasi Sistem Akademik</strong> Program Studi Sistem Informasi Bisnis Jurusan Teknologi Informasi,
                        Politeknik
                        Negeri Malang</p>
                    <div class="d-flex">
                        <a href="#about" class="btn-get-started">Selengkapnya</a>
                    </div>
                </div>
                <div class="col-lg-5 order-1 order-lg-2 hero-img">
                    <img src="{{ asset('assets/img/hero-img.png') }}" class="img-fluid" alt="">
                </div>
            </div>
        </div>
    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">
        <div class="container" data-aos="fade-up">
            <div class="row gx-0">
                <div class="col-lg-6 d-flex align-items-center" data-aos="zoom-out" data-aos-delay="200">
                    <img src="{{ asset('assets/img/about.jpg') }}" class="img-fluid" alt="">
                </div>
                <div class="col-lg-6 d-flex flex-column justify-content-center" data-aos="fade-up"
                    data-aos-delay="200">
                    <div class="content">
                        <h3>Tentang Sistem Informasi Bisnis</h3>
                        <p>Program Studi Sistem Informasi Bisnis (SIB) merupakan salah satu program studi di
                            Jurusan Teknologi
                            Informasi, yang merupakan hasil pengembangan dari program DIII Manajemen Informatika.
                            Program SIB
                            dirancang untuk menghasilkan lulusan Sarjana Terapan (ST) di bidang Sistem Informasi
                            yang memiliki kemampuan
                            untuk mengatasi permasalahan bisnis dengan menggunakan teknologi. Program SIB adalah
                            program studi terapan yang
                            mengintegrasikan teknologi informasi untuk menghasilkan produk atau solusi bisnis yang
                            dapat diimplementasikan
                            dalam industri kreatif. Lulusan dibekali dengan keterampilan fundamental dalam dua
                            aspek,
                            yaitu teknologi dan bisnis.</p>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /About Section -->

    <!-- Features Section -->
    <section id="features" class="features section">
        <div class="container">
            <ul class="nav nav-tabs row g-2 d-flex" data-aos="fade-up" data-aos-delay="100">
                <li class="nav-item col-md-2 col-2">
                    <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
                        <h4>Profil</h4>
                    </a>
                </li><!-- End tab nav item -->
                <li class="nav-item col-md-2 col-2">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
                        <h4>Visi</h4>
                    </a>
                </li><!-- End tab nav item -->
                <li class="nav-item col-md-2 col-2">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-3">
                        <h4>Misi</h4>
                    </a>
                </li><!-- End tab nav item -->
                <li class="nav-item col-md-2 col-2">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-4">
                        <h4>Tujuan</h4>
                    </a>
                </li><!-- End tab nav item -->
                <li class="nav-item col-md-2 col-2">
                    <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-5">
                        <h4>Sasaran</h4>
                    </a>
                </li><!-- End tab nav item -->
            </ul>

            <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
                <div class="tab-pane fade active show" id="features-tab-1">
                    <div class="row">
                        <div class="col-lg-12 d-flex flex-column justify-content-center">
                            <h3>Sejarah Politeknik Negeri Malang</h3>
                            <p>Dimulai sebagai Fakultas Teknologi Non-Gelar di Universitas Brawijaya, yang didirikan
                                setelah diterbitkannya Keputusan Presiden Republik Indonesia No. 59 Tahun 1982,
                                Politeknik Negeri Malang
                                sekarang ini telah berkembang menjadi lembaga pendidikan vokasi yang independent.
                                Perubahan status ini
                                diatur dalam Surat Keputusan Menteri Pendidikan dan Kebudayaan No. 0313/O/1991.
                                Politeknik Negeri Malang
                                terus berupaya meningkatkan kualitasnya, terutama dalam bidang Pendidikan,
                                Penelitian,
                                dan
                                Pengabdian kepada Masyarakat, yang diarahkan pada teknologi terapan. Upaya ini telah
                                menunjukkan hasil
                                positif, seperti ditunjukkan oleh prestasi akreditasi "A" pada tahun 2018 (Keputusan
                                No.
                                409/SK/BANPT/Akred/PT/XII/2018) dan akreditasi internasional dari ASIC (Acreditation
                                Service for
                                International School Collage and University) pada tahun 2020 untuk 20 program studi.
                                Program studi D4-SIB
                                didirikan pada tahun 2010 berdasarkan Surat Keputusan Menteri Nasional Pendidikan
                                No.
                                50/D/O/2010.
                                Awalnya, program studi D4-SIB berada di bawah Departemen Teknik Elektro, Politeknik
                                Negeri Malang,
                                sebelum akhirnya dipindahkan ke Departemen Teknologi Informasi pada tahun 2015.
                                Pada tahun 2018, program
                                studi D4-SIB memperoleh peringkat akreditasi "B" dari BAN-PT, berdasarkan Keputusan
                                No.
                                1810/SK/BANPT/Akred/DiplIV/VII/2018.</p>
                        </div>
                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade" id="features-tab-2">
                    <div class="row">
                        <div class="col-lg-12 d-flex flex-column justify-content-center">
                            <h3>Visi</h3>
                            <p>Menjadi Program Studi terkemuka di bidang Sistem Informasi Bisnis di Indonesia dan
                                di
                                luar
                                negeri.</p>
                        </div>
                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade" id="features-tab-3">
                    <div class="row">
                        <div class="col-lg-12 d-flex flex-column justify-content-center">
                            <h3>Misi</h3>
                            <p>Melaksanakan pendidikan vokasi di bidang Sistem Informasi Bisnis yang inovatif,
                                aplikatif,
                                dan diarahkan pada kebutuhan industri dan masyarakat, serta menghasilkan lulusan
                                yang
                                kompeten,
                                etis, dan wirausaha.</p>
                        </div>
                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade" id="features-tab-4">
                    <div class="row">
                        <div class="col-lg-12 d-flex flex-column justify-content-center">
                            <h3>Tujuan</h3>
                            <ul>
                                <li><i class="bi bi-check2-all"></i> <span>Menghasilkan lulusan yang kompeten,
                                        etis, dan
                                        wirausaha di bidang Sistem Informasi Bisnis.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Mengembangkan dan menerapkan kurikulum
                                        yang
                                        relevan,
                                        aplikatif, dan diarahkan pada kebutuhan industri dan
                                        masyarakat.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Melakukan penelitian dan pengabdian
                                        kepada
                                        masyarakat yang relevan,
                                        aplikatif, dan diarahkan pada kebutuhan industri dan
                                        masyarakat.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Membangun kemitraan dengan industri,
                                        pemerintah,
                                        dan
                                        masyarakat untuk mendukung pengembangan program.</span></li>
                            </ul>
                        </div>
                    </div>
                </div><!-- End tab content item -->

                <div class="tab-pane fade" id="features-tab-5">
                    <div class="row">
                        <div class="col-lg-12 d-flex flex-column justify-content-center">
                            <h3>Sasaran</h3>
                            <ul>
                                <li><i class="bi bi-check2-all"></i> <span>Meningkatkan relevansi, kuantitas, dan
                                        kualitas
                                        pendidikan di bidang Sistem Informasi Bisnis.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Meningkatkan relevansi, kuantitas, dan
                                        kualitas
                                        penelitian di bidang Sistem Informasi Bisnis.</span></li>
                                <li><i class="bi bi-check2-all"></i> <span>Meningkatkan relevansi, kuantitas, dan
                                        kualitas
                                        pengabdian kepada masyarakat di bidang Sistem Informasi
                                        Bisnis.</span></li>
                            </ul>
                        </div>
                    </div>
                </div><!-- End tab content item -->
            </div>
        </div>
    </section><!-- /Features Section -->

    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section light-background">
        <img src="{{ asset('assets/img/cta-bg.jpg') }}" alt="">
        <div class="container">
            <div class="row" data-aos="zoom-in" data-aos-delay="100">
                <div class="col-xl-9 text-center text-xl-start">
                    <h3>Kunjungi Website Polinema</h3>
                    <p>Untuk informasi lebih lanjut tentang Polinema, silakan kunjungi website kami di <a
                            href="https://polinema.ac.id/">polinema.ac.id</a></p>
                </div>
                <div class="col-xl-3 cta-btn-container text-center">
                    <a class="cta-btn align-middle" href="https://polinema.ac.id/" target="_blank">Kunjungi Sekarang</a>
                </div>
            </div>
        </div>
    </section><!-- /Call To Action Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section">
        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Contact</h2>
            <p>Untuk informasi lebih lanjut dan bantuan, silakan tidak ragu untuk menghubungi kami.</p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4">
                <div class="col-lg-6">
                    <div class="row gy-4">
                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="200">
                                <i class="bi bi-geo-alt"></i>
                                <h3>Alamat</h3>
                                <p>Jl. Soekarno Hatta No.9</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="300">
                                <i class="bi bi-telephone"></i>
                                <h3>Telepon</h3>
                                <p>(0341) 404424</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="400">
                                <i class="bi bi-envelope"></i>
                                <h3>Email</h3>
                                <p>humas@polinema.ac.id</p>
                            </div>
                        </div><!-- End Info Item -->

                        <div class="col-md-6">
                            <div class="info-item" data-aos="fade" data-aos-delay="500">
                                <i class="bi bi-clock"></i>
                                <h3>Jam Operasional</h3>
                                <p>Senin - Jumat</p>
                                <p>09:00 WIB - 17:00 WIB</p>
                            </div>
                        </div><!-- End Info Item -->
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="map-responsive" data-aos="fade-up" data-aos-delay="200">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.927095927927!2d112.615927314776!3d-7.946993494276091!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd6281a3a0a1a0f%3A0x7a1a1a1a1a1a1a1a!2sPoliteknik%20Negeri%20Malang!5e0!3m2!1sen!2sid!4v1691234567890!5m2!1sen!2sid"
                            width="100%" height="405" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div><!-- End Contact Form -->
            </div>
        </div>
    </section><!-- /Contact Section -->
@endsection
