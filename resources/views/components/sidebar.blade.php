<style>
    .app-sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid rgba(31, 111, 255, 0.1);
        min-height: 100vh;
        box-shadow: 4px 0 15px rgba(0,0,0,0.03);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .sidebar-header {
        height: 76px;
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        border-bottom: 1px solid rgba(31, 111, 255, 0.05);
    }

    .sidebar-menu {
        list-style: none;
        padding: 1.5rem 1rem;
        margin: 0;
        flex-grow: 1; 
    }

    .sidebar-menu li {
        margin-bottom: 0.5rem;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 0.85rem 1.25rem;
        color: #6c7a92;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.25s ease;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
    }

    .sidebar-link:hover, .sidebar-link.active {
        background: rgba(31, 111, 255, 0.08);
        color: #1f6fff;
    }

    .sidebar-link.text-danger:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545 !important;
    }

    .sidebar-footer {
        padding: 1.5rem 1rem;
        border-top: 1px solid rgba(31, 111, 255, 0.1);
        margin-top: auto; 
    }

    .user-box {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .user-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: #14213d;
        line-height: 1.2;
    }
</style>

<aside class="app-sidebar">
    <div class="sidebar-inner d-flex flex-column h-100">

        <div class="sidebar-header">
            <a href="{{ route('home') }}">
                <img src="{{ asset('CareSmile.png') }}" alt="CareSmile Logo" style="height: 60px;">
            </a>
        </div>

        <ul class="sidebar-menu">
            {{-- 1. Dashboard --}}
            <li>
                @php
                    $dashboardRoute = match(Auth::user()->role) {
                        \App\Enums\Role::Admin   => 'admin.dashboard',
                        \App\Enums\Role::Dentist => 'dentist.dashboard',
                        default                  => 'patient.dashboard',
                    };
                @endphp
                <a href="{{ route($dashboardRoute) }}" class="sidebar-link {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- 2. Admin Only: User Management --}}
            @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('admin.dentists') }}" class="sidebar-link {{ request()->routeIs('admin.dentists*') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i><span>Dentists</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.patients') }}" class="sidebar-link {{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i><span>Patients</span>
                    </a>
                </li>
            @endif

            {{-- 3. Appointments (Dynamic Route) --}}
            <li>
                @php
                    $apptRoute = match(Auth::user()->role) {
                        \App\Enums\Role::Admin   => 'admin.appointments',
                        \App\Enums\Role::Dentist => 'dentist.appointments',
                        default                  => 'patient.appointments',
                    };
                @endphp
                <a href="{{ route($apptRoute) }}" class="sidebar-link {{ request()->routeIs('*.appointments*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appointments</span>
                </a>
            </li>

            {{-- 4. Schedules (Admin or Dentist) --}}
            @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i><span>Schedules</span>
                    </a>
                </li>
            @elseif(Auth::user()->isDentist())
                <li>
                    <a href="{{ route('dentist.schedules.index') }}" class="sidebar-link {{ request()->routeIs('dentist.schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-clock"></i><span>My Schedule</span>
                    </a>
                </li>
            @endif

            {{-- 5. Services --}}
            @if(Auth::user()->isAdmin())
                <li>
                    <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                        <i class="fas fa-tooth"></i>
                        <span>Services</span>
                    </a>
                </li>
            @elseif(!Auth::user()->isAdmin() && !Auth::user()->isDentist())
                {{-- Patient Only: Browse Services --}}
                <li>
                    {{-- Note: Change '#' to your actual patient services route if you have one --}}
                    <a href="{{ route('patient.services.index') }}" class="sidebar-link">
                        <i class="fas fa-list-ul"></i>
                        <span>Browse Services</span>
                    </a>
                </li>
            @endif

            {{-- 6. My Profile (Hidden for Admin) --}}
            @if(!Auth::user()->isAdmin())
                <li>
                    @php
                        $profileRoute = match(Auth::user()->role) {
                            \App\Enums\Role::Dentist => 'dentist.profile',
                            default                  => 'patient.profile',
                        };
                    @endphp
                    <a href="{{ route($profileRoute) }}" class="sidebar-link {{ request()->routeIs('*.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>
            @endif

            {{-- 7. Logout --}}
            <li>
                <form method="POST" action="{{ route('logout') }}" id="sidebar-logout-form">
                    @csrf
                    <button type="submit" class="sidebar-link text-danger">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>

        {{-- Fixed User Footer --}}
        <div class="sidebar-footer">
            <div class="user-box">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->role->name }}</small>
                </div>
            </div>
        </div>
    </div>
</aside>