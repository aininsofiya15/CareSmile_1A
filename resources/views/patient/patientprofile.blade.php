@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-dark: #1456cc;
        --brand-blue-light: #eef5ff;
    }

    /* --- The Blue Banner Header Styles --- */
    .banner-header {
        background-color: var(--brand-blue);
        border-radius: 12px;
        padding: 30px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(31, 111, 255, 0.15);
    }

    .banner-left {
        display: flex;
        flex-direction: column;
    }

    .banner-title-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
    }

    .banner-icon {
        font-size: 26px;
    }

    .banner-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        color: white;
        letter-spacing: -0.5px;
    }

    .banner-subtitle {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.85);
        margin: 6px 0 0 0;
        font-weight: 400;
    }

    /* --- Existing Profile Styles --- */
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
        background-color: var(--brand-blue);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-patient-primary:hover {
        background-color: var(--brand-blue-dark);
        color: white;
    }

    .form-control { 
        border-radius: 8px; 
        padding: 0.6rem 1rem; 
        border: 1px solid #d1d5db; 
    }
    
    .profile-page-avatar {
        width: 100px; height: 100px; background-color: var(--brand-blue-light); color: var(--brand-blue);
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; margin: 0 auto 1rem auto;
    }
</style>

<div class="container-fluid py-3">
    
    <div class="banner-header">
        <div class="banner-left">
            <div class="banner-title-wrapper">
                <i class="fas fa-user-circle banner-icon"></i> 
                <h1 class="banner-title">My Profile</h1>
            </div>
            <p class="banner-subtitle">Manage your personal and medical information</p>
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