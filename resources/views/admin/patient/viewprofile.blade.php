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

    .btn-admin-primary {
        background-color: #4361ee;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
    }

    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #d1d5db; }
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
                        {{-- Hidden fields so name & email pass through validation --}}
                        <input type="hidden" name="name" value="{{ $patient->name }}">
                        <input type="hidden" name="email" value="{{ $patient->email }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $patient->name }}" readonly tabindex="-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email" class="form-control bg-light" value="{{ $patient->email }}" readonly tabindex="-1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control bg-light edit-input"
                                       value="{{ old('phone_number', $patient->patientProfile->phone_number ?? '') }}"
                                       placeholder="e.g. 011-12345678"
                                       pattern="01[0-9]-[0-9]{7,8}"
                                       title="Please follow the format: 01X-XXXXXXX"
                                       {{ $errors->has('phone_number') ? '' : 'readonly' }}>
                                @error('phone_number')
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control bg-light edit-input"
                                       value="{{ old('dob', $patient->patientProfile->dob ?? '') }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Residential Address</label>
                                <textarea name="address" class="form-control bg-light edit-input" rows="2"
                                          placeholder="Unit number, street name, and city" readonly>{{ old('address', $patient->patientProfile->address ?? '') }}</textarea>
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
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Drug Allergies</label>
                                <input type="text" name="allergies" class="form-control bg-light edit-input" maxlength="100"
                                       value="{{ old('allergies', $patient->patientProfile->allergies ?? '') }}"
                                       placeholder="e.g. Penicillin, Latex, Pollen" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Current Medications</label>
                                <textarea name="medications" class="form-control bg-light edit-input" rows="2"
                                          placeholder="List medications currently taken (e.g. Paracetamol 500mg)" readonly>{{ old('medications', $patient->patientProfile->medications ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Contact Name</label>
                                <input type="text" name="emergency_name" class="form-control bg-light edit-input"
                                       value="{{ old('emergency_name', $patient->patientProfile->emergency_contact_name ?? '') }}"
                                       placeholder="Full name of next of kin" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Phone</label>
                                <input type="text" name="emergency_phone"
                                       class="form-control bg-light edit-input @error('emergency_phone') is-invalid @enderror"
                                       value="{{ old('emergency_phone', $patient->patientProfile->emergency_contact_phone ?? '') }}"
                                       placeholder="e.g. 011-12345678"
                                       pattern="01[0-9]-[0-9]{7,8}"
                                       title="Please follow the format: 01X-XXXXXXX"
                                       {{ $errors->has('emergency_phone') ? '' : 'readonly' }}>
                                @error('emergency_phone')
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-circle me-1"></i>{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Save / Cancel — hidden until Edit is clicked --}}
                        <div id="saveGroup" class="d-none mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <button type="button" onclick="toggleEdit()" class="btn btn-light px-4 rounded-pill">Cancel</button>
                            <button type="submit" class="btn btn-admin-primary px-5 shadow-sm rounded-pill">
                                <i class="fas fa-save me-2"></i> Save Patient Record
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
    const isLocked = inputs[0].readOnly;

    inputs.forEach(input => {
        input.readOnly = !isLocked;
        if (isLocked) {
            input.classList.remove('bg-light');
        } else {
            input.classList.add('bg-light');
        }
    });

    saveGroup.classList.toggle('d-none', !isLocked);
    editBtn.classList.toggle('d-none', isLocked);
}

// Auto-open edit mode if validation errors exist on page load
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function () {
        const inputs = document.querySelectorAll('.edit-input');
        const saveGroup = document.getElementById('saveGroup');
        const editBtn = document.getElementById('editBtn');

        inputs.forEach(input => {
            input.readOnly = false;
            input.classList.remove('bg-light');
        });

        saveGroup.classList.remove('d-none');
        editBtn.classList.add('d-none');
    });
@endif
</script>
@endsection