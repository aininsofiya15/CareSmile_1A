@extends('layouts.app')

@section('content')
<style>
    .profile-header-card {
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
        border: none;
        border-radius: 24px;
        color: white;
    }
    .admin-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        background: white;
        transition: all 0.3s ease;
    }
    .card-header-soft {
        background-color: transparent;
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }
    .profile-avatar-lg {
        width: 110px;
        height: 110px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 4px solid rgba(255, 255, 255, 0.3);
        font-size: 3rem;
        font-weight: 800;
    }
    .form-label-custom {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    .form-control-custom {
        border-radius: 12px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.2s;
    }
    .form-control-custom:focus {
        background-color: white;
        border-color: #4361ee;
        box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1);
    }
    .form-control-custom[readonly] {
        background-color: #f8fafc;
        border-color: transparent;
        cursor: default;
    }
    .btn-save {
        background: #4361ee;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border: none;
    }
</style>

<div class="container-fluid py-4">
    {{-- Top Navigation --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Patient Profile</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.patients') }}" class="text-decoration-none">Patients</a></li>
                    <li class="breadcrumb-item active">{{ $patient->name }}</li>
                </ol>
            </nav>
        </div>
        <button type="button" onclick="toggleEdit()" id="editBtn" class="btn btn-white shadow-sm border px-4 rounded-pill">
            <i class="fas fa-edit me-2 text-primary"></i>Edit Profile
        </button>
    </div>

    <form action="{{ route('admin.patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Left Column --}}
            <div class="col-lg-4">
                <div class="card profile-header-card p-4 text-center shadow-lg mb-4">
                    <div class="profile-avatar-lg rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                        {{ substr($patient->name, 0, 1) }}
                    </div>
                    <h3 class="fw-bold mb-1">{{ $patient->name }}</h3>
                    <p class="mb-3 opacity-75 small">ID: #CS-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</p>
                    <div class="badge bg-white text-primary rounded-pill px-3 py-2">Active Record</div>
                </div>

                <div class="card admin-card p-4">
                    <h6 class="fw-bold mb-3">Account Information</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Joined Date</span>
                        <span class="small fw-bold">{{ $patient->created_at->format('d M Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small">Last Updated</span>
                        <span class="small fw-bold">{{ $patient->updated_at->diffForHumans() }}</span>
                    </div>
                    <hr>
                    <button type="button" class="btn btn-light w-100 text-danger fw-bold rounded-3">
                        <i class="fas fa-key me-2"></i>Reset Password
                    </button>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="col-lg-8">
                <div class="card admin-card mb-4">
                    <div class="card-header card-header-soft">
                        <h5 class="fw-bold mb-0 text-primary">Personal Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-custom edit-input" value="{{ $patient->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom edit-input" value="{{ $patient->email }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->phone_number ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Date of Birth</label>
                                <input type="text" name="dob" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->dob ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Residential Address</label>
                                <textarea name="address" class="form-control form-control-custom edit-input" rows="2" readonly>{{ $patient->patientProfile->address ?? '-' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card admin-card">
                    <div class="card-header card-header-soft">
                        <h5 class="fw-bold mb-0 text-danger">Medical Context</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label-custom">Drug Allergies</label>
                                <input type="text" name="allergies" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->allergies ?? '-' }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Current Medications</label>
                                <textarea name="medications" class="form-control form-control-custom edit-input" rows="2" readonly>{{ $patient->patientProfile->medications ?? '-' }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Emergency Contact</label>
                                <input type="text" name="emergency_name" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->emergency_contact_name ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Emergency Phone</label>
                                <input type="text" name="emergency_phone" class="form-control form-control-custom edit-input" value="{{ $patient->patientProfile->emergency_contact_phone ?? '-' }}" readonly>
                            </div>
                        </div>

                        {{-- Hidden Save Footer --}}
                        <div id="saveGroup" class="d-none mt-5 pt-3 border-top">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" onclick="toggleEdit()" class="btn btn-light px-4 rounded-pill">Cancel</button>
                                <button type="submit" class="btn btn-save shadow">
                                    <i class="fas fa-check-circle me-2"></i>Save Patient Record
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function toggleEdit() {
    const inputs = document.querySelectorAll('.edit-input');
    const saveGroup = document.getElementById('saveGroup');
    const editBtn = document.getElementById('editBtn');

    inputs.forEach(input => {
        input.readOnly = !input.readOnly;
        // Visual toggle for editable state
        if (!input.readOnly) {
            input.style.backgroundColor = "white";
            input.style.borderColor = "#e2e8f0";
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