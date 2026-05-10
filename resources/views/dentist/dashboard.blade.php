@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #4361ee;
        --brand-blue-dark: #3a56d4;
        --brand-blue-light: #eef2ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(67, 97, 238, 0.1);
        --shadow-soft: 0 10px 30px rgba(0,0,0,0.04);
        --radius-lg: 20px;
    }

    /* Hero Banner */
    .dentist-hero {
        background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-blue-dark) 100%);
        border-radius: 24px;
        color: white;
        padding: 3rem;
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.2);
        margin-bottom: 2.5rem;
    }

    .portal-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
    }

    /* Dashboard Cards */
    .dashboard-card {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
        height: 100%;
        transition: transform 0.3s ease;
    }
    .dashboard-card:hover { transform: translateY(-5px); }

    .icon-box-blue {
        width: 55px; height: 55px;
        border-radius: 14px;
        background-color: var(--brand-blue-light);
        color: var(--brand-blue);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin-bottom: 1.2rem;
    }

    /* Stat Typography */
    .stat-label { color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; }
    .stat-value { color: var(--text-dark); font-size: 2.5rem; font-weight: 800; margin-bottom: 0; }

    /* Utilization Badges */
    .utilization-badge {
        display: inline-flex;
        padding: 0.35rem 0.8rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 800;
        margin-top: 0.5rem;
    }
    .utilization-low { background: #dcfce7; color: #15803d; }
    .utilization-moderate { background: #dbeafe; color: #1d4ed8; }
    .utilization-high { background: #ffedd5; color: #c2410c; }

    .utilization-progress {
        height: 8px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        margin: 10px 0;
    }
    .utilization-fill { height: 100%; background: var(--brand-blue); border-radius: 10px; }

    /* Tables & Grids */
    .weekly-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .day-card { border: 1px solid var(--card-border); border-radius: 15px; padding: 1.2rem; background: #fff; }
    .day-card.is-today { border-color: var(--brand-blue); box-shadow: 0 0 15px rgba(67, 97, 238, 0.15); }

    .table-custom { width: 100%; border-collapse: collapse; }
    .table-custom th { padding: 1rem; background: #f8fafc; text-align: left; font-size: 0.8rem; color: var(--text-muted); }
    .table-custom td { padding: 1rem; border-top: 1px solid #f1f5f9; }

    .btn-white { background: white; color: var(--brand-blue); font-weight: 700; border-radius: 10px; padding: 0.8rem 1.5rem; text-decoration: none; }
    .btn-blue-light { background: var(--brand-blue-light); color: var(--brand-blue); font-weight: 700; border-radius: 8px; padding: 0.5rem; text-align: center; text-decoration: none; display: block; }
</style>

<div class="container-fluid py-4">

    {{-- 1. HERO BANNER --}}
    <div class="dentist-hero d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <div class="portal-badge"><i class="fas fa-user-md"></i> Dentist Portal</div>
            <h1 class="fw-bold mb-2">Hello, Dr. {{ explode(' ', auth()->user()->name)[0] }}!</h1>
            <p class="mb-0 opacity-90">Manage your daily clinic operations and track patient progress efficiently.</p>
        </div>
        <a href="{{ route('dentist.profile') }}" class="btn-white shadow-sm"><i class="fas fa-user-circle me-2"></i>Edit My Profile</a>
    </div>

    {{-- 2. PRIMARY STATS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="dashboard-card p-4">
                <div class="icon-box-blue"><i class="far fa-calendar-check"></i></div>
                <div class="stat-label">Today's Appointments</div>
                <h2 class="stat-value">{{ $todayCount }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card p-4">
                <div class="icon-box-blue"><i class="fas fa-users"></i></div>
                <div class="stat-label">Total Patients</div>
                <h2 class="stat-value">{{ $totalPatients }}</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="dashboard-card p-4">
                <div class="icon-box-blue"><i class="fas fa-chart-line"></i></div>
                <div class="stat-label">Weekly Utilization</div>
                <h2 class="stat-value">{{ $dashboardStats['weekly_utilization_percentage'] }}%</h2>
            </div>
        </div>
    </div>

    {{-- 3. WORKLOAD SUMMARY --}}
    <div class="dashboard-card mb-5">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">Workload Summary</h5>
            <div class="row text-center g-3">
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Total Schedules</small>
                    <span class="fw-bold fs-5">{{ $dashboardStats['workload_total_schedules'] }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Total Appointments</small>
                    <span class="fw-bold fs-5">{{ $dashboardStats['workload_total_appointments'] }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Available Slots</small>
                    <span class="fw-bold fs-5 text-primary">{{ $dashboardStats['weekly_available_slots'] }}</span>
                </div>
                <div class="col-6 col-md-3">
                    <small class="text-muted d-block">Avg. Utilization</small>
                    <span class="fw-bold fs-5">{{ $dashboardStats['workload_average_utilization'] }}%</span>
                </div>
            </div>
            <div class="utilization-progress mt-4">
                <div class="utilization-fill" style="width: {{ $dashboardStats['workload_average_utilization'] }}%"></div>
            </div>
        </div>
    </div>

    {{-- 4. WEEKLY SCHEDULE GRID --}}
    <h4 class="fw-bold mb-4">Weekly Calendar</h4>
    <div class="weekly-grid mb-5">
        @foreach($weekDays as $day)
            <div class="day-card {{ $day['is_today'] ? 'is-today' : '' }}">
                <p class="fw-bold mb-0 text-dark">{{ $day['day_name'] }}</p>
                <small class="text-muted">{{ $day['date_label'] }}</small>
                
                <div class="mt-3">
                    @if($day['has_schedule'])
                        <div class="d-flex justify-content-between mb-1">
                            <small>Booked: <strong>{{ $day['booked_slots'] }}</strong></small>
                            <small>Free: <strong>{{ $day['available_slots'] }}</strong></small>
                        </div>
                        <div class="utilization-progress" style="height: 5px;">
                            <div class="utilization-fill" style="width: {{ $day['utilization_percentage'] }}%"></div>
                        </div>
                        <span class="utilization-badge {{ $day['utilization_class'] }}">{{ $day['utilization_percentage'] }}% Busy</span>
                    @else
                        <span class="badge bg-light text-muted w-100 py-2">No Schedule</span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- 5. UPCOMING APPOINTMENTS --}}
    <div class="dashboard-card">
        <div class="p-4 border-bottom">
            <h5 class="fw-bold mb-0">Upcoming Appointments</h5>
        </div>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Patient Name</th>
                        <th>Date & Time</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingAppointments as $appointment)
                        <tr>
                            <td class="fw-bold">{{ $appointment['patient_name'] }}</td>
                            <td>
                                <div>{{ $appointment['appointment_date'] }}</div>
                                <small class="text-muted">{{ $appointment['appointment_start_time'] }}</small>
                            </td>
                            <td><span class="badge bg-blue-light text-primary">{{ $appointment['service'] }}</span></td>
                            <td><span class="badge bg-success bg-opacity-10 text-success">{{ $appointment['status'] }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No upcoming appointments found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection