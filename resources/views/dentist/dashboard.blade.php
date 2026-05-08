@extends('layouts.app')

@section('content')
<style>
    /* Unified CareSmile Blue Theme */
    .dentist-hero {
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
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

    /* Stat Cards */
    .stat-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
        padding: 2rem;
        height: 100%;
    }

    .stat-card:hover { transform: translateY(-5px); }

    .icon-box-blue {
        width: 55px; height: 55px;
        border-radius: 14px;
        background-color: #eef2ff;
        color: #4361ee;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; margin-bottom: 1.2rem;
    }

    /* Action Cards */
    .action-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        padding: 1.8rem;
        height: 100%;
        display: flex; flex-direction: column;
        transition: transform 0.2s ease;
    }

    .action-card:hover { transform: translateY(-3px); box-shadow: 0 15px 35px rgba(0,0,0,0.06); }

    .btn-blue-light {
        background-color: #eef2ff;
        color: #4361ee;
        font-weight: 700;
        border: none; border-radius: 8px;
        padding: 0.6rem 1rem; font-size: 0.85rem;
        text-align: center; text-decoration: none;
        transition: all 0.2s;
    }

    .btn-blue-light:hover { background-color: #e0e7ff; color: #3a56d4; }

    .btn-white {
        background-color: white;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-white:hover { background-color: #f8fafc; color: #3a56d4; }
</style>

<div class="container-fluid py-2">

    {{-- 1. HERO BANNER --}}
    <div class="dentist-hero d-flex justify-content-between align-items-center flex-wrap gap-4">
        <div>
            <div class="portal-badge">
                <i class="fas fa-user-md"></i> Dentist Portal
            </div>
            <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">Hello, Dr. {{ explode(' ', auth()->user()->name)[0] }}!</h1>
            <p class="mb-0 text-white" style="max-width: 600px; opacity: 0.9;">
                Welcome to your daily clinic overview. Efficiently manage your patients and track your upcoming treatments.
            </p>
        </div>
        <div>
            <a href="{{ route('dentist.profile') }}" class="btn-white shadow-sm"><i class="fas fa-user-circle me-2"></i>Edit My Profile</a>
        </div>
    </div>

    {{-- 2. STAT CARDS --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue"><i class="far fa-calendar-check"></i></div>
                <h6 class="fw-bold text-muted mb-2">Today's Appointments</h6>
                <h1 class="fw-black text-dark mb-2" style="font-size: 3rem; font-weight: 800;">
                    {{ $todayCount }}
                </h1>
                <p class="text-muted small mb-0">Scheduled for today's session.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue"><i class="fas fa-users"></i></div>
                <h6 class="fw-bold text-muted mb-2">Total Patients</h6>
                <h1 class="fw-black text-dark mb-2" style="font-size: 3rem; font-weight: 800;">
                    {{ $totalPatients }}
                </h1>
                <p class="text-muted small mb-0">Patients assigned to your care.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue"><i class="fas fa-chart-pie"></i></div>
                <h6 class="fw-bold text-muted mb-2">Weekly Load</h6>
                <h1 class="fw-black text-dark mb-2" style="font-size: 3rem; font-weight: 800;">
                    {{ $weekCount }}
                </h1>
                <p class="text-muted small mb-0">Treatments scheduled this week.</p>
            </div>
        </div>
    </div>

    {{-- 3. QUICK ACTIONS --}}
    <h3 class="fw-bold mb-2">Management Tools</h3>
    <p class="text-muted mb-4">Quick access to essential clinic functions.</p>

    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;"><i class="fas fa-clock"></i></div>
                <h6 class="fw-bold fs-5">My Schedule</h6>
                <p class="text-muted small mb-4">Manage your work hours and availability.</p>
                <div class="mt-auto d-grid"><a href="#" class="btn-blue-light">Edit Hours</a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;"><i class="fas fa-book-medical"></i></div>
                <h6 class="fw-bold fs-5">Patient Records</h6>
                <p class="text-muted small mb-4">View x-rays and medical history logs.</p>
                <div class="mt-auto d-grid"><a href="#" class="btn-blue-light">View All</a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;"><i class="fas fa-prescription-bottle-alt"></i></div>
                <h6 class="fw-bold fs-5">Consultations</h6>
                <p class="text-muted small mb-4">Review notes from previous treatments.</p>
                <div class="mt-auto d-grid"><a href="#" class="btn-blue-light">Open Records</a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;"><i class="fas fa-file-invoice"></i></div>
                <h6 class="fw-bold fs-5">Billing/Reports</h6>
                <p class="text-muted small mb-4">Generate clinic performance reports.</p>
                <div class="mt-auto d-grid"><a href="#" class="btn-blue-light">View Analytics</a></div>
            </div>
        </div>
    </div>
</div>
@endsection