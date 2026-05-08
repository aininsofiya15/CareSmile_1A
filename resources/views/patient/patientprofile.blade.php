@extends('layouts.app')

@section('content')
<style>
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
    .btn-patient-primary {
        background-color: #4361ee;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #d1d5db; }
    .profile-page-avatar {
        width: 100px; height: 100px; background-color: #eef2ff; color: #4361ee;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; margin: 0 auto 1rem auto;
    }
</style>

<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0" style="color: #111827;">My Profile</h2>
            <p class="text-muted small mb-0">Manage your personal and medical information</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Avatar & Security --}}
        <div class="col-lg-4">
            <div class="card patient-card text-center p-4">
                <div class="profile-page-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">Patient ID: #CS-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">Active Account</span>
            </div>

            <div class="card patient-card">
                <div class="card-header card-header-light"><i class="fas fa-lock me-2 text-muted"></i> Security</div>
                <div class="card-body p-4">
                    <form action="{{ route('patient.password.update') }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" placeholder="Enter your current password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">New Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Confirm Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat your new password" required>
                        </div>
                        <button type="submit" class="btn btn-patient-primary w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Profile Data --}}
        <div class="col-lg-8">
            <form action="{{ route('patient.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Personal Details --}}
                <div class="card patient-card mb-4">
                    <div class="card-header card-header-light"><i class="far fa-id-card me-2 text-muted"></i> Personal Information</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" class="form-control bg-light" value="{{ $user->name }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email Address</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" 
                                       value="{{ old('phone_number', $profile->phone_number ?? '') }}" 
                                       placeholder="e.g. 012-3456789">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $profile->dob ?? '') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Home Address</label>
                                <textarea name="address" class="form-control" rows="2" placeholder="Unit number, street name, and city">{{ old('address', $profile->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Medical Context --}}
                <div class="card patient-card">
                    <div class="card-header card-header-light"><i class="fas fa-file-medical me-2 text-muted"></i> Medical Context</div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Known Drug Allergies</label>
                                <input type="text" name="allergies" class="form-control" maxlength="100" 
                                       value="{{ old('allergies', $profile->allergies ?? '') }}" 
                                       placeholder="e.g. Penicillin, Latex, Pollen">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Current Medications</label>
                                <textarea name="medications" class="form-control" rows="2" 
                                          placeholder="List medications you are currently taking (e.g. Paracetamol 500mg)">{{ old('medications', $profile->medications ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" 
                                       value="{{ old('emergency_contact_name', $profile->emergency_contact_name ?? '') }}" 
                                       placeholder="Full name of next of kin">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" 
                                       value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone ?? '') }}" 
                                       placeholder="e.g. 013-9876543">
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-patient-primary px-5 py-2 shadow-sm">
                                <i class="fas fa-save me-2"></i> Save All Information
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection