@extends('layouts.app')

@section('content')
<style>
    /* Premium Blue Theme for Patient */
    .patient-hero {
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
        border-radius: 24px;
        color: white;
        padding: 2.2rem;
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.2);
        margin-bottom: 2rem;
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

    .btn-white {
        background-color: white;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
    }

    .btn-white:hover {
        background-color: #f8fafc;
        transform: translateY(-2px);
        color: #3a56d4;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    /* Card Styles */
    .dashboard-card {
        background: white;
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }

    /* Used for the right column to stretch fully */
    .h-100-card {
        height: 100%;
    }

    .icon-box-blue {
        width: 55px;
        height: 40px;
        border-radius: 14px;
        background-color: #eef2ff;
        color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .date-box {
        background-color: white;
        color: #4361ee;
        border-radius: 12px;
        width: 65px;
        height: 65px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        line-height: 1.2;
    }

    .appt-list-item {
        background-color: #f8fafc;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .appt-list-item:hover {
        background-color: white;
        border-color: #e2e8f0;
        box-shadow: 0 8px 24px rgba(0,0,0,0.04);
        transform: translateY(-2px);
    }

    .status-badge {
        background-color: #eef2ff;
        color: #4361ee;
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
    }

    /* Action Button for Services Box */
    .btn-light-blue-full {
        background-color: #eef2ff;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.8rem;
        text-align: center;
        text-decoration: none;
        display: block;
        width: 100%;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-light-blue-full:hover {
        background-color: #e0e7ff;
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
            <a href="{{ route('patient.appointments.create') }}" class="btn-white shadow-sm">
                <i class="fas fa-plus me-2"></i>New Appointment
            </a>
        </div>
    </div>

    {{-- 2. MAIN CONTENT GRID --}}
    <div class="row g-4 mb-5">
        
        {{-- LEFT COLUMN: Stacked Cards (Next Appointment + Our Services) --}}
        <div class="col-lg-4 d-flex flex-column gap-4">
            
            {{-- Top Card: Next Appointment --}}
            <div class="dashboard-card">
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box-blue mb-0 me-3">
                        <i class="far fa-calendar-check"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark">Next Appointment</h5>
                </div>

                @if($nextAppointment)
                    <div>
                        <p class="text-muted small fw-bold text-uppercase mb-1">Date</p>
                        <h2 class="fw-black text-dark mb-4" style="font-size: 2.2rem; letter-spacing: -1px;">
                            {{ \Carbon\Carbon::parse($nextAppointment->appointment_date)->format('d M Y') }}
                        </h2>
                        
                        <div class="p-3 rounded-4 mb-2" style="background-color: #f8fafc;">
                            <div class="d-flex align-items-center text-dark fw-bold mb-2">
                                <i class="far fa-clock me-3 text-muted" style="font-size: 1.2rem;"></i>
                                <span class="fs-5">{{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('h:i A') }}</span>
                            </div>
                            <div class="d-flex align-items-center text-primary fw-bold">
                                <i class="fas fa-tooth me-3" style="font-size: 1.2rem;"></i>
                                <span>{{ $nextAppointment->service }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-4 text-center">
                        <div class="icon-box-blue mx-auto mb-3" style="background: #f8fafc; color: #cbd5e1;"><i class="far fa-calendar-times"></i></div>
                        <h5 class="fw-bold text-dark">Not Scheduled</h5>
                        <p class="text-muted mb-0">You have no upcoming visits.</p>
                    </div>
                @endif
            </div>

            {{-- Bottom Card: Our Services --}}
            <div class="dashboard-card">
                <div class="icon-box-blue mb-3" style="width: 48px; height: 48px; font-size: 1.2rem;">
                    <i class="fas fa-list-ul"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Our Services</h5>
                <p class="text-muted small mb-4">Browse all dental treatments and prices.</p>
                
                {{-- Make sure to link this to your services page route --}}
                <a href="{{ route('patient.services.index') }}" class="btn-light-blue-full mt-auto">View Services</a>
            </div>

        </div>

        {{-- RIGHT COLUMN: Upcoming Appointments List --}}
        <div class="col-lg-8">
            <div class="dashboard-card h-100-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark">Upcoming Schedule</h5>
                    <a href="{{ route('patient.appointments') }}" class="text-primary fw-bold text-decoration-none small">View All</a>
                </div>

                <div class="card-body p-0">
                    @if($upcomingAppointments->isEmpty())
                        <div class="text-center py-5">
                            <p class="text-muted mb-0">No other appointments scheduled.</p>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-3">
                            @foreach($upcomingAppointments as $a)
                                <div class="appt-list-item d-flex align-items-center justify-content-between flex-wrap gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        {{-- Custom Date Box --}}
                                        <div class="date-box">
                                            <span class="fw-bold" style="font-size: 1.4rem;">{{ \Carbon\Carbon::parse($a->appointment_date)->format('d') }}</span>
                                            <span class="fw-bold" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">{{ \Carbon\Carbon::parse($a->appointment_date)->format('M') }}</span>
                                        </div>
                                        
                                        {{-- Details --}}
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark" style="font-size: 1.1rem;">{{ $a->service }}</h6>
                                            <p class="text-muted small mb-0 fw-medium">
                                                <i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Status Pill --}}
                                    <div>
                                        <span class="status-badge">Scheduled</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection