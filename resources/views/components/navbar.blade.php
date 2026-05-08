<nav class="navbar navbar-expand-lg {{ request()->routeIs('home') ? 'navbar-home' : 'navbar-inner' }} shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-lg-none" href="{{ route('home') }}">CareSmile</a>
        
        <div class="d-none d-lg-block" style="width: 200px;"></div>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            {{-- Navigation Links - Centered --}}
            <ul class="navbar-nav mx-auto text-center">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                </li>

                {{-- Dynamic Dashboard Link (Only for Logged In Users) --}}
                @auth
                    <li class="nav-item">
                        @php
                            $dashboardRoute = match(Auth::user()->role) {
                                \App\Enums\Role::Admin   => 'admin.dashboard',
                                \App\Enums\Role::Dentist => 'dentist.dashboard',
                                default                  => 'patient.dashboard',
                            };
                        @endphp
                        <a class="nav-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}" href="{{ route($dashboardRoute) }}">Dashboard</a>
                    </li>
                @endauth

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
                </li>
                
            </ul>

            {{-- Right Side: Theme Toggle & Auth --}}
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <button class="btn btn-link nav-link p-2 rounded-circle" onclick="toggleTheme()" id="theme-toggle" title="Toggle Dark/Light Mode" style="text-decoration: none;">
                        <span id="theme-icon-light" class="theme-icon">🌙</span>
                        <span id="theme-icon-dark" class="theme-icon d-none">☀️</span>
                    </button>
                </li>

                @guest
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm px-3" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-light btn-sm text-primary px-3" href="{{ route('register') }}">Register</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            <span class="avatar-circle d-flex align-items-center justify-content-center text-uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li>
                                <span class="dropdown-item-text text-muted small">
                                    <span class="badge bg-primary-subtle text-primary">{{ Auth::user()->role->label() }}</span>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            
                            {{-- Dynamic Profile Link --}}
                            <li>
                                @php
                                    $profileRoute = match(Auth::user()->role) {
                                        \App\Enums\Role::Admin   => 'admin.profile',
                                        \App\Enums\Role::Dentist => 'dentist.profile',
                                        default                  => 'patient.profile',
                                    };
                                @endphp
                                <a class="dropdown-item {{ request()->routeIs('*.profile') ? 'active' : '' }}" href="{{ route($profileRoute) }}">
                                    <i class="bi bi-person me-2"></i>Profile
                                </a>
                            </li>

                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>