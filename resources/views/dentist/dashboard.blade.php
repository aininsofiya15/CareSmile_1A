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

    :root {
        --brand-blue: #1f6fff;
        --brand-blue-dark: #1456cc;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.08);
        --shadow-soft: 0 10px 30px rgba(20, 33, 61, 0.08);
        --success-soft: #dcfce7;
        --success-text: #15803d;
        --warning-soft: #fef3c7;
        --warning-text: #b45309;
        --surface-soft: #f8fafc;
        --radius-lg: 16px;
    }

    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-muted);
        margin: 0.35rem 0 0;
    }

    .dashboard-card {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
        height: 100%;
    }

    .dashboard-card .card-body {
        padding: 1.25rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
        text-transform: uppercase;
    }

    .stat-value {
        color: var(--text-dark);
        font-size: 1.65rem;
        font-weight: 800;
        line-height: 1.2;
        margin: 0;
    }

    .section-title {
        color: var(--text-dark);
        font-size: 1.15rem;
        font-weight: 800;
        margin: 0;
    }

    .section-muted {
        color: var(--text-muted);
        font-size: 0.92rem;
        margin: 0.25rem 0 0;
    }

    .schedule-detail {
        background: var(--surface-soft);
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1rem;
        height: 100%;
    }

    .detail-label {
        color: var(--text-muted);
        font-size: 0.82rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: var(--text-dark);
        font-weight: 700;
        margin: 0;
    }

    .status-badge,
    .count-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        padding: 0.35rem 0.7rem;
    }

    .status-active {
        background: var(--success-soft);
        color: var(--success-text);
    }

    .status-inactive {
        background: #f1f5f9;
        color: #64748b;
    }

    .status-fully-booked {
        background: #ffedd5;
        color: #c2410c;
    }

    .status-unavailable {
        background: #fee2e2;
        color: #dc2626;
    }

    .count-booked {
        background: var(--warning-soft);
        color: var(--warning-text);
    }

    .count-available {
        background: var(--success-soft);
        color: var(--success-text);
    }

    .utilization-progress {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #e2e8f0;
        margin-top: 0.65rem;
    }

    .utilization-progress-fill {
        height: 100%;
        border-radius: inherit;
        background: var(--brand-blue);
    }

    .utilization-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 0.32rem 0.7rem;
    }

    .utilization-none {
        background: #f1f5f9;
        color: #64748b;
    }

    .utilization-low {
        background: #fef3c7;
        color: #b45309;
    }

    .utilization-moderate {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .utilization-high {
        background: #ffedd5;
        color: #c2410c;
    }

    .utilization-full {
        background: #fee2e2;
        color: #dc2626;
    }

    .weekly-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
        gap: 1rem;
    }

    .day-card {
        border: 1px solid var(--card-border);
        border-radius: 14px;
        background: #fff;
        padding: 1rem;
        min-height: 190px;
    }

    .day-card.is-today {
        border-color: rgba(31, 111, 255, 0.45);
        box-shadow: 0 12px 28px rgba(31, 111, 255, 0.12);
    }

    .day-name {
        color: var(--text-dark);
        font-weight: 800;
        margin: 0;
    }

    .day-date {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.8rem;
    }

    .mini-schedule {
        border-top: 1px dashed var(--card-border);
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    .empty-state {
        color: var(--text-muted);
        text-align: center;
        padding: 2rem 1rem;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th {
        background: var(--surface-soft);
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 700;
        padding: 0.9rem 1rem;
        text-align: left;
        border-bottom: 1px solid var(--card-border);
    }

    .table-custom td {
        color: var(--text-dark);
        padding: 1rem;
        border-bottom: 1px solid var(--card-border);
        vertical-align: middle;
    }

    .table-custom tr:last-child td {
        border-bottom: none;
    }
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
                <div class="mt-auto d-grid"><a href="#" class="