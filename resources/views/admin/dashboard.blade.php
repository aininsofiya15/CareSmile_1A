@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-dark: #1456cc;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.08);
        --shadow-soft: 0 10px 30px rgba(20, 33, 61, 0.08);
        --shadow-hover: 0 18px 40px rgba(31, 111, 255, 0.14);
        --radius-xl: 24px;
        --radius-lg: 20px;
        --radius-md: 16px;
    }

    .admin-dashboard {
        padding: 1.5rem;
        background:
            radial-gradient(circle at top left, rgba(31, 111, 255, 0.06), transparent 30%),
            linear-gradient(180deg, #f7faff 0%, #f3f7fd 100%);
        min-height: calc(100vh - 80px);
    }

    .dashboard-hero {
        position: relative;
        overflow: hidden;
        border-radius: 30px;
        padding: 2rem;
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        color: #fff;
        box-shadow: 0 20px 50px rgba(31, 111, 255, 0.20);
        margin-bottom: 1.75rem;
    }

    .dashboard-hero::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 220px;
        height: 220px;
        background: rgba(255, 255, 255, 0.10);
        border-radius: 50%;
    }

    .dashboard-hero::after {
        content: "";
        position: absolute;
        bottom: -80px;
        left: -50px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    .dashboard-hero-content {
        position: relative;
        z-index: 2;
    }

    .dashboard-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.55rem 0.95rem;
        border-radius: 999px;
        margin-bottom: 1rem;
        letter-spacing: 0.2px;
    }

    .dashboard-title {
        font-size: clamp(2rem, 4vw, 3rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 0.75rem;
    }

    .dashboard-subtitle {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.85);
        max-width: 650px;
        margin-bottom: 0;
    }

    .dashboard-header-actions .btn-dashboard {
        background: #fff;
        color: var(--brand-blue);
        font-weight: 700;
        border: none;
        border-radius: 14px;
        padding: 0.95rem 1.4rem;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.10);
        transition: all 0.25s ease;
    }

    .dashboard-header-actions .btn-dashboard:hover {
        transform: translateY(-2px);
        background: #f8fbff;
        color: var(--brand-blue-dark);
    }

    .dashboard-stat-card,
    .dashboard-action-card {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-xl);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: var(--shadow-soft);
        backdrop-filter: blur(12px);
    }

    .dashboard-stat-card {
        transition: all 0.28s ease;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-hover);
    }

    .dashboard-stat-card .card-body {
        padding: 1.6rem;
    }

    .stat-icon-wrap {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(31, 111, 255, 0.14), rgba(31, 111, 255, 0.06));
        color: var(--brand-blue);
        font-size: 1.35rem;
    }

    .stat-label {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .stat-value {
        font-size: 2.4rem;
        font-weight: 800;
        color: var(--brand-blue);
        line-height: 1;
    }

    .stat-note {
        font-size: 0.96rem;
        color: var(--text-muted) !important;
    }

    .dashboard-action-card .card-body {
        padding: 2rem;
    }

    .section-title {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--text-dark);
    }

    .section-subtitle {
        color: var(--text-muted);
        font-size: 1rem;
    }

    .quick-action-box {
        display: block;
        height: 100%;
        padding: 1.35rem;
        border-radius: var(--radius-lg);
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        border: 1px solid rgba(31, 111, 255, 0.10);
        box-shadow: 0 8px 24px rgba(20, 33, 61, 0.05);
        transition: all 0.25s ease;
        text-decoration: none;
    }

    .quick-action-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 18px 36px rgba(31, 111, 255, 0.12);
        border-color: rgba(31, 111, 255, 0.22);
    }

    .quick-action-disabled {
        opacity: 0.78;
        cursor: not-allowed;
    }

    .quick-action-disabled:hover {
        transform: none;
        box-shadow: 0 8px 24px rgba(20, 33, 61, 0.05);
    }

    .quick-action-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        font-size: 1.2rem;
    }

    .quick-action-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .quick-action-text {
        font-size: 0.95rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .mini-badge {
        display: inline-block;
        margin-top: 0.8rem;
        padding: 0.35rem 0.7rem;
        border-radius: 999px;
        background: rgba(31, 111, 255, 0.08);
        color: var(--brand-blue);
        font-size: 0.78rem;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .admin-dashboard {
            padding: 1rem;
        }

        .dashboard-hero {
            padding: 1.5rem;
            border-radius: 24px;
        }

        .dashboard-action-card .card-body,
        .dashboard-stat-card .card-body {
            padding: 1.25rem;
        }
    }
</style>

<div class="admin-dashboard">
    <div class="dashboard-hero">
        <div class="dashboard-hero-content d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-4">
            <div>
                <div class="dashboard-eyebrow">
                    <span>🛠</span>
                    <span>Admin Panel</span>
                </div>
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">
                    Monitor clinic activity, manage patient information, and access your key tools from one clean workspace.
                </p>
            </div>

            <div class="dashboard-header-actions">
                <a href="{{ route('admin.patients') }}" class="btn btn-dashboard">
                    Manage Patients
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card dashboard-stat-card h-100">
                <div class="card-body">
                    <div class="stat-icon-wrap mb-3">
                        <i class="fas fa-users"></i>
                    </div>
                    <p class="stat-label mb-2">Total Patients</p>
                    <h2 class="stat-value mb-2">0</h2>
                    <p class="stat-note mb-0">Registered patients in the system</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-stat-card h-100">
                <div class="card-body">
                    <div class="stat-icon-wrap mb-3">
                        <i class="fas fa-user-doctor"></i>
                    </div>
                    <p class="stat-label mb-2">Total Dentists</p>
                    <h2 class="stat-value mb-2">0</h2>
                    <p class="stat-note mb-0">Active dentists available for appointments</p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card dashboard-stat-card h-100">
                <div class="card-body">
                    <div class="stat-icon-wrap mb-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <p class="stat-label mb-2">Today's Appointments</p>
                    <h2 class="stat-value mb-2">0</h2>
                    <p class="stat-note mb-0">Appointments scheduled for today</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card dashboard-action-card">
        <div class="card-body">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-2">
                <div>
                    <h4 class="section-title mb-1">Quick Actions</h4>
                    <p class="section-subtitle mb-0">Jump into your main admin tasks with a cleaner workflow.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <a href="{{ route('admin.patients') }}" class="quick-action-box">
                        <div class="quick-action-icon mb-3">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <h6 class="quick-action-title mb-2">Manage Patients</h6>
                        <p class="quick-action-text mb-0">View, edit, and organize patient records efficiently.</p>
                    </a>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="quick-action-box quick-action-disabled">
                        <div class="quick-action-icon mb-3">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <h6 class="quick-action-title mb-2">Manage Dentists</h6>
                        <p class="quick-action-text mb-0">Add and manage dentist profiles in the next module.</p>
                        <span class="mini-badge">Coming Soon</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="quick-action-box quick-action-disabled">
                        <div class="quick-action-icon mb-3">
                            <i class="fas fa-file-medical"></i>
                        </div>
                        <h6 class="quick-action-title mb-2">Manage Services</h6>
                        <p class="quick-action-text mb-0">Update clinic services and treatment options later.</p>
                        <span class="mini-badge">Coming Soon</span>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="quick-action-box quick-action-disabled">
                        <div class="quick-action-icon mb-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h6 class="quick-action-title mb-2">View Reports</h6>
                        <p class="quick-action-text mb-0">Track clinic performance and activity insights soon.</p>
                        <span class="mini-badge">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection