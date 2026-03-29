@extends('layouts.app')

@section('content')
<style>
    /* Premium Blue Theme for Patient */
    .patient-hero {
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
        border-radius: 24px;
        color: white;
        padding: 3rem 3rem;
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.2);
        margin-bottom: 2.5rem;
    }

    .patient-badge {
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

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .icon-box-blue {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        background-color: #eef2ff; /* Very light blue */
        color: #4361ee; /* Signature CareSmile blue */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1.2rem;
    }

    /* Quick Action Cards */
    .action-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        padding: 1.8rem;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.06);
    }

    .btn-blue-light {
        background-color: #eef2ff;
        color: #4361ee;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-blue-light:hover {
        background-color: #e0e7ff;
        color: #3a56d4;
    }

    .btn-white {
        background-color: white;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-white:hover {
        background-color: #f8fafc;
        transform: scale(1.02);
        color: #3a56d4;
    }
</style>

<div class="container-fluid py-2">
    
    {{-- 1. GIANT BLUE HERO BANNER --}}
    <div class="patient-hero d-flex justify-content-between align-items-center flex-wrap gap-4">
        <div>
            <div class="patient-badge">
                <i class="far fa-user-circle"></i> Patient Portal
            </div>
            <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">Welcome Back!</h1>
            <p class="mb-0 text-white" style="max-width: 600px; opacity: 0.9;">Manage your dental appointments, track your oral health records, and access your treatment history all in one place.</p>
        </div>
        <div>
            <a href="#" class="btn-white shadow-sm"><i class="fas fa-plus me-2"></i>New Appointment</a>
        </div>
    </div>

    {{-- 2. STAT CARDS ROW --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <h6 class="fw-bold text-muted mb-2">Next Appointment</h6>
                <h2 class="fw-black text-dark mb-2" style="font-weight: 800;">Not Scheduled</h2>
                <p class="text-muted small mb-0">You have no upcoming visits.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue">
                    <i class="fas fa-tooth"></i>
                </div>
                <h6 class="fw-bold text-muted mb-2">Total Visits</h6>
                <h1 class="fw-black text-dark mb-2" style="font-size: 3rem; font-weight: 800;">0</h1>
                <p class="text-muted small mb-0">Past completed treatments.</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card stat-card">
                <div class="icon-box-blue">
                    <i class="far fa-bell"></i>
                </div>
                <h6 class="fw-bold text-muted mb-2">Notifications</h6>
                <h1 class="fw-black text-dark mb-2" style="font-size: 3rem; font-weight: 800;">0</h1>
                <p class="text-muted small mb-0">Unread messages from clinic.</p>
            </div>
        </div>
    </div>

    {{-- 3. QUICK ACTIONS ROW --}}
    <h3 class="fw-bold mb-2">My Services</h3>
    <p class="text-muted mb-4">Quick access to your dental services and records.</p>
    
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="far fa-calendar-plus"></i>
                </div>
                <h6 class="fw-bold fs-5">Book Visit</h6>
                <p class="text-muted small mb-4">Schedule a new checkup or treatment.</p>
                <div class="mt-auto d-grid">
                    <a href="#" class="btn-blue-light">Book Now</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="far fa-folder-open"></i>
                </div>
                <h6 class="fw-bold fs-5">My Records</h6>
                <p class="text-muted small mb-4">View your x-rays and dental history.</p>
                <div class="mt-auto d-grid">
                    <a href="#" class="btn-blue-light">View Records</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="fas fa-file-prescription"></i>
                </div>
                <h6 class="fw-bold fs-5">Prescriptions</h6>
                <p class="text-muted small mb-4">Check your active medications and care instructions.</p>
                <div class="mt-auto d-grid">
                    <a href="#" class="btn-blue-light">View Details</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card action-card">
                <div class="icon-box-blue" style="width: 45px; height: 45px; font-size: 1.2rem;">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <h6 class="fw-bold fs-5">Billing</h6>
                <p class="text-muted small mb-4">Review past invoices and make payments.</p>
                <div class="mt-auto d-grid">
                    <a href="#" class="btn-blue-light">View Billing</a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection