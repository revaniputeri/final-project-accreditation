<nav class="main-header navbar navbar-expand navbar-white navbar-light shadow-sm">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        @auth
            <li class="nav-item d-flex align-items-center px-3">
                <span class="nav-link font-weight-semibold text-dark">
                    {{ Auth::user()->username }}
                </span>
            </li>
        @endauth
    </ul>
</nav>
