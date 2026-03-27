<style>
    .app-sidebar {
        width: 260px;
        background: #ffffff;
        border-right: 1px solid rgba(31, 111, 255, 0.1);
        min-height: 100vh;
        box-shadow: 4px 0 15px rgba(0,0,0,0.03);
        /* Note: I removed the padding from here so the logo can touch the top! */
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    
    .sidebar-header {
        height: 76px; /* Matches the height of the top navbar */
        display: flex;
        align-items: center;
        padding: 0 1.5rem;
        border-bottom: 1px solid rgba(31, 111, 255, 0.05);
    }

    .sidebar-menu {
        list-style: none;
        padding: 3rem 1rem 1.5rem 1rem; 
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
        padding-top: 1.5rem;
        border-top: 1px solid rgba(31, 111, 255, 0.1);
        padding: 1.5rem 1rem;
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
                <img src="{{ asset('CareSmile.png') }}" alt="CareSmile Logo" style="height: 90px;">
            </a>
        </div>
        
        {{-- Navigation --}}
        <ul class="sidebar-menu flex-grow-1">

            {{-- Dashboard --}}
            <li>
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                @elseif(Auth::user()->isDentist())
                    <a href="{{ route('dentist.dashboard') }}" class="sidebar-link {{ request()->routeIs('dentist.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('patient.dashboard') }}" class="sidebar-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                @endif
            </li>

            {{-- Admin Only --}}
            @if(Auth::user()->isAdmin())
            <li>
                <a href="{{ route('admin.patients') }}" class="sidebar-link {{ request()->routeIs('admin.patients') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span>Patients</span>
                </a>
            </li>
            @endif

            {{-- Schedules --}}
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

            {{-- Disabled --}}
            <li>
                <span class="sidebar-link disabled">
                    <i class="fas fa-calendar-check"></i>
                    <span>Appointments</span>
                </span>
            </li>

            <li>
                <span class="sidebar-link disabled">
                    <i class="fas fa-tooth"></i>
                    <span>Services</span>
                </span>
            </li>

            <li>
                <span class="sidebar-link disabled">
                    <i class="fas fa-file-medical"></i>
                    <span>Records</span>
                </span>
            </li>

            <li>
                <span class="sidebar-link disabled">
                    <i class="fas fa-chart-line"></i>
                    <span>Reports</span>
                </span>
            </li>
        </ul>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <div class="user-box">
                <div class="user-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <small class="text-muted">Logged in</small>
                </div>
            </div>
        </div>

    </div>
</aside>