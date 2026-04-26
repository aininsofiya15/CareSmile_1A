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

    .form-control {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        border: 1px solid #d1d5db;
    }

    .form-control:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
    }

    /* Fix for Eye Icon Button alignment */
    .input-group .btn {
        border-color: #d1d5db;
        z-index: 0;
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
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0" style="color: #111827;">My Profile</h2>
        @if(session('success'))
            <div class="alert alert-success py-2 px-3 mb-0 rounded-pill" style="font-size: 0.9rem;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Identity & Security --}}
        <div class="col-lg-4">
            
            {{-- Identity Card --}}
            <div class="card patient-card text-center p-4">
                <div class="profile-page-avatar">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">Patient ID: #CS-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                <span class="badge bg-success rounded-pill px-3 py-2 mb-3" style="font-weight: 500; width: fit-content; margin: 0 auto;">Active Account</span>
            </div>

            {{-- Account Security Card --}}
            <div class="card patient-card">
                <div class="card-header card-header-light">
                    <i class="fas fa-lock me-2 text-muted"></i> Password
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('patient.password.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="current_password" class="form-control @error('current_password') is-invalid @enderror" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="••••••••" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-patient-primary w-100 mt-2">Change Password</button>
                    </form>
                </div>
            </div>
        </div> {{-- FIXED: Added closing div for col-lg-4 --}}

        {{-- RIGHT COLUMN: THE DATA FORM --}}
        <div class="col-lg-8">
            <form action="{{ route('patient.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Personal Information Card --}}
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

                {{-- Medical Context Card --}}
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
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-patient-primary">
                                <i class="fas fa-save me-2"></i> Save All Information
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div> {{-- FIXED: Closing div for col-lg-8 --}}
    </div> {{-- FIXED: Closing div for row --}}
</div>

<script>
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    });
</script>
@endsection