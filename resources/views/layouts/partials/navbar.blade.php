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
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="{{ asset('storage/user_avatar/' . Auth::user()->id_user . '.png') }}" alt="User Photo"
                        class="rounded-circle me-2 mr-2" style="width: 40px; height: 40px; object-fit: cover;"
                        onerror="this.onerror=null; this.src='{{ asset('storage/user_avatar/default.jpg') }}';">
                    <span>{{ Auth::user()->username }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                    <div class="dropdown-item">
                        <a href="{{route('profile.pageProfile')}}" class="dropdown-item">
                            <i class="icon fas fa-user mr-1"></i> Profile
                        </a>
                    </div>

                    <div class="dropdown-item">

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item">
                                <i class="fas fa-sign-out-alt mr-1"></i> Logout
                            </button>
                        </form>
                    </div>

                </div>
            </li>
        @endauth
    </ul>
</nav>
