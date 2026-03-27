<aside class="app-sidebar">
    <div class="sidebar-inner d-flex flex-column h-100">

        {{-- Logo / Header --}}
        <div class="sidebar-header text-center">
            <img src="{{ asset('CareSmile.png') }}" alt="CareSmile Logo" class="sidebar-logo">
            <h6 class="sidebar-title">CareSmile</h6>
            <span class="sidebar-role">{{ Auth::user()->role->label() }}</span>
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