@extends('layouts.app')

@section('content')
<style>
    /* Reusing the CareSmile Patient Theme */
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
        transition: background-color 0.2s;
    }

    .btn-patient-primary:hover {
        background-color: #3a56d4;
        color: white;
    }

    .btn-patient-outline {
        background-color: transparent;
        color: #4361ee;
        border: 1px solid #4361ee;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-patient-outline:hover {
        background-color: #f8fafc;
    }

    /* Form Styling */
    .form-control, .form-select {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #d1d5db;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    .profile-page-avatar {
        width: 100px;
        height: 100px;
        background-color: #eef2ff;
        color: #4361ee;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        margin: 0 auto 1rem auto;
        text-transform: uppercase;
    }
</style>

<div class="container-fluid py-3">
    
    <h2 class="fw-bold mb-4" style="color: #111827;">My Profile</h2>

    <div class="row g-4">
        {{-- LEFT COLUMN: Identity & Settings --}}
        <div class="col-lg-4">
            
            {{-- Identity Card --}}
            <div class="card patient-card text-center p-4">
                {{-- Dynamically pulls the first letter of their name --}}
                <div class="profile-page-avatar">
                    {{ substr($user->name, 0, 1) }}
                </div>
                {{-- Dynamically pulls their actual name --}}
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">Patient ID: #CS-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                <span class="badge bg-success rounded-pill px-3 py-2 mb-3" style="font-weight: 500;">Active Account</span>
            </div>

            {{-- Account Security (UI Mockup for now) --}}
            <div class="card patient-card">
                <div class="card-header card-header-light">
                    <i class="fas fa-lock me-2 text-muted"></i> Security
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Current Password</label>
                        <input type="password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">New Password</label>
                        <input type="password" class="form-control" placeholder="••••••••">
                    </div>
                    <button class="btn btn-patient-primary w-100 mt-2">Change Password</button>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: THE DATA FORM --}}
        <div class="col-lg-8">
            
            {{-- We wrap BOTH cards in one form so they save together --}}
            <form action="{{ route('patient.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Personal Information Form --}}
                <div class="card patient-card mb-4">
                    <div class="card-header card-header-light">
                        <i class="far fa-id-card me-2 text-muted"></i> Personal Information
                    </div>
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
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $profile->phone_number) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Date of Birth</label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob', $profile->dob) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Home Address</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $profile->address) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Medical Context Form --}}
                <div class="card patient-card">
                    <div class="card-header card-header-light">
                        <i class="fas fa-file-medical me-2 text-muted"></i> Medical Context
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Known Drug Allergies</label>
                                <input type="text" name="allergies" class="form-control" value="{{ old('allergies', $profile->allergies) }}" placeholder="e.g., Penicillin, Latex">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Current Medications</label>
                                <textarea name="medications" class="form-control" rows="2" placeholder="List any daily medications">{{ old('medications', $profile->medications) }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $profile->emergency_contact_name) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $profile->emergency_contact_phone) }}">
                            </div>
                        </div>
                        
                        {{-- ONE Save Button for the whole form --}}
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-patient-primary">
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