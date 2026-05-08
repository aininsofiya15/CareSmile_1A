@extends('layouts.app')

@section('content')
<style>
    /* CareSmile Admin-Patient View Theme */
    .patient-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        background-color: white;
        margin-bottom: 1.5rem;
    }

    .card-header-light {
        background-color: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        border-radius: 16px 16px 0 0 !important;
        padding: 1rem 1.5rem;
        font-weight: 700;
        color: #111827;
    }

    .profile-page-avatar {
        width: 100px;
        height: 100px;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1rem auto;
    }

    .form-control-custom {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #d1d5db;
        background-color: #f8fafc;
        transition: all 0.2s;
    }

    .form-control-custom:focus:not([readonly]) {
        background-color: white;
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.1);
    }

    .form-control-custom[readonly] {
        border-color: transparent;
        cursor: default;
    }

    .btn-admin-primary {
        background-color: #4361ee;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }
</style>

<div class="container-fluid py-3">
    
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #111827;">Patient Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients') }}" class="text-decoration-none">Patients</a></li>
                    <li class="breadcrumb-item active text-muted">{{ $patient->name }}</li>
                </ol>
            </nav>
        </div>
        <button type="button" onclick="toggleEdit()" id="editBtn" class="btn btn-outline-primary rounded-pill px-4">
            <i class="fas fa-edit me-2"></i>Edit Profile
        </button>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Identity & Account Info --}}
        <div class="col-lg-4">
            {{-- Visual Profile Card --}}
            <div class="card patient-card text-center p-4 shadow-sm" style="background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%); color: white;">
                <div class="profile-page-avatar">
                    {{ strtoupper(substr($patient->name, 0, 1)) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $patient->name }}</h4>
                <p class="opacity-75 mb-3">ID: #CS-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</p>
                <div class="bg-white text-primary rounded-pill px-3 py-1 d-inline-block small fw-bold">Active Record</div>
            </div>

            {{-- Account Information & Reset Password --}}
            <div class="card patient-card">
                <div class="card-header card-header-light">
                    <h6 class="fw-bold mb-0">Account Information</h6>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success small py-2 mb-3">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Joined Date</span>
                        <span class="small fw-bold">{{ $patient->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted small">Last Updated</span>
                        <span class="small fw-bold text-end">{{ $patient->updated_at->diffForHumans() }}</span>
                    </div>
                    <hr>
                    
                    {{-- Reset Password Form --}}
                    <form action="{{ route('admin.patients.reset-password', $patient->id) }}" method="POST" onsubmit="return confirm('Reset password to CareSmile2026!?')">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-light w-100 text-danger fw-bold rounded-3 py-2 border-0">
                            <i class="fas fa-key me-2"></i>Reset Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Profile Data --}}
        <div class="col-lg-8">
            <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Section 1: Personal --}}
                <div class="card patient-card mb-4">
                    <div class="card-header card-header-light">
                        <h6 class="fw-bold mb-0 text-primary">Personal Details</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-custom edit-input" value="{{ $patient->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom edit-input" value="{{ $patient->email }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->phone_number ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Date of Birth</label>
                                <input type="text" name="dob" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->dob ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Residential Address</label>
                                <textarea name="address" class="form-control form-control-custom edit-input" rows="2" readonly>{{ $patient->patientProfile->address ?? '-' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Medical Context --}}
                <div class="card patient-card">
                    <div class="card-header card-header-light">
                        <h6 class="fw-bold mb-0 text-danger">Medical Context</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Drug Allergies</label>
                                <input type="text" name="allergies" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->allergies ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold text-uppercase">Current Medications</label>
                                <textarea name="medications" class="form-control form-control-custom edit-input" rows="2" readonly>{{ $patient->patientProfile->medications ?? '-' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Emergency Contact</label>
                                <input type="text" name="emergency_name" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->emergency_contact_name ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase">Emergency Phone</label>
                                <input type="text" name="emergency_phone" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->emergency_contact_phone ?? '-' }}" readonly>
                            </div>
                        </div>
                        
                        {{-- Hidden Save Group --}}
                        <div id="saveGroup" class="d-none mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <button type="button" onclick="toggleEdit()" class="btn btn-light px-4 rounded-pill">Cancel</button>
                            <button type="submit" class="btn btn-admin-primary px-4 shadow-sm rounded-pill">
                                <i class="fas fa-check-circle me-2"></i> Save Patient Record
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleEdit() {
    const inputs = document.querySelectorAll('.edit-input');
    const saveGroup = document.getElementById('saveGroup');
    const editBtn = document.getElementById('editBtn');

    inputs.forEach(input => {
        input.readOnly = !input.readOnly;
        if (!input.readOnly) {
            input.style.backgroundColor = "white";
            input.style.borderColor = "#d1d5db";
        } else {
            input.style.backgroundColor = "#f8fafc";
            input.style.borderColor = "transparent";
        }
    });

    saveGroup.classList.toggle('d-none');
    editBtn.classList.toggle('d-none');
}
</script>
@endsection