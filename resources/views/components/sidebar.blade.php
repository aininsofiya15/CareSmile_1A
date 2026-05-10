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
        padding: 2rem 1rem 1.5rem 1rem;
        margin: 0;
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
    }

    .sidebar-link:hover, .sidebar-link.active {
        background: rgba(31, 111, 255, 0.08);
        color: #1f6fff;
    }

    .sidebar-link i {
        font-size: 1.1rem;
        width: 24px;
        text-align: center;
    }

    .sidebar-link.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .sidebar-footer {
        margin-top: auto;
        padding: 1.5rem 1rem;
        border-top: 1px solid rgba(31, 111, 255, 0.1);
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

        {{-- Logo / Header --}}
        <div class="sidebar-header">
            <a href="{{ route('home') }}">
                <img src="{{ asset('CareSmile.png') }}" alt="CareSmile Logo" style="height: 60px;">
            </a>
        </div>

        {{-- Navigation --}}
        <ul class="sidebar-menu flex-grow-1">

            {{-- Dynamic Dashboard Link based on Role --}}
            <li>
                @php
                    $dashboardRoute = 'patient.dashboard';
                    if(Auth::user()->isAdmin()) $dashboardRoute = 'admin.dashboard';
                    elseif(Auth::user()->isDentist()) $dashboardRoute = 'dentist.dashboard';
                @endphp

                <a href="{{ route($dashboardRoute) }}" class="sidebar-link {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                    <i class="fas fa-chart-pie"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            {{-- Admin Only Section --}}
            @if(Auth::user()->isAdmin())
                {{-- Dentists Management --}}
                <li>
                    <a href="{{ route('admin.dentists') }}" class="sidebar-link {{ request()->routeIs('admin.dentists*') ? 'active' : '' }}">
                        <i class="fas fa-user-md"></i>
                        <span>Dentists</span>
                    </a>
                </li>

                {{-- Patients Management --}}
                <li>
                    <a href="{{ route('admin.patients') }}" class="sidebar-link {{ request()->routeIs('admin.patients*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Patients</span>
                    </a>
                </li>
            @endif

            {{-- Schedules Section --}}
            <li>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.schedules.index') }}" class="sidebar-link {{ request()->routeIs('admin.schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Schedules</span>
                    </a>
                @elseif(Auth::user()->isDentist())
                    <a href="{{ route('dentist.schedules.index') }}" class="sidebar-link {{ request()->routeIs('dentist.schedules.*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span>My Schedule</span>
                    </a>
                @endif
            </li>

            {{-- Appointments --}}
            <li>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.appointments') }}"
                    class="sidebar-link {{ request()->routeIs('admin.appointments*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                @elseif(Auth::user()->isDentist())
                    <a href="{{ route('dentist.appointments') }}"
                    class="sidebar-link {{ request()->routeIs('dentist.appointments*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                @elseif(Auth::user()->isPatient())
                    <a href="{{ route('patient.appointments') }}"
                    class="sidebar-link {{ request()->routeIs('patient.appointments*') ? 'active' : '' }}">
                        <i class="fas fa-calendar-check"></i>
                        <span>Appointments</span>
                    </a>
                @endif
            </li>

            {{-- Services (Static/Disabled for now) --}}
            <li>
                <a href="{{ route('admin.services.index') }}" class="sidebar-link {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                    <i class="fas fa-tooth"></i>
                    <span>Services</span>
                </a>
            </li>


        </ul>

        <div class="sidebar-footer">
            <div class="user-box">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    {{-- Access the ->value or ->name property of the Enum --}}
                    <small class="text-muted">{{ ucfirst(Auth::user()->role->value) }}</small>
                </div>
            </div>
        </div>

    </div>
</aside>
