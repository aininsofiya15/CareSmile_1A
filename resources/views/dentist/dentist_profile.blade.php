@extends('layouts.app')

@section('content')
<style>
    /* CareSmile Unified Theme */
    .dentist-card {
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
    .btn-dentist-primary {
        background-color: #4361ee;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-dentist-primary:hover {
        background-color: #3a56d4;
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
    }
    .form-control { border-radius: 8px; padding: 0.6rem 1rem; border: 1px solid #d1d5db; }
    
    /* Password Group Styling */
    .form-control-password { border-right: none; }
    .input-group-text {
        background-color: white;
        border-left: none;
        color: #6b7280;
        cursor: pointer;
        border: 1px solid #d1d5db;
        border-radius: 0 8px 8px 0;
    }

    .profile-avatar {
        width: 100px; height: 100px; background-color: #eef2ff; color: #4361ee;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; margin: 0 auto 1rem auto;
    }
</style>

<div class="container-fluid py-3">
    <h2 class="fw-bold mb-4" style="color: #111827;">Staff Settings</h2>

    <div class="row g-4">
        {{-- LEFT COLUMN: Avatar & Security --}}
        <div class="col-lg-4">
            <div class="card dentist-card text-center p-4">
                <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <h4 class="fw-bold mb-1">Dr. {{ $user->name }}</h4>
                <p class="text-muted mb-3">Staff ID: #DEN-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">Clinic Staff</span>
            </div>

            <div class="card dentist-card">
                <div class="card-header card-header-light">
                    <i class="fas fa-lock me-2 text-muted"></i> Security
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('dentist.password.update') }}" method="POST">
                        @csrf @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" id="curr_pass" class="form-control form-control-password" placeholder="Enter current password" required>
                                <span class="input-group-text" onclick="togglePassword('curr_pass', this)"><i class="far fa-eye"></i></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">New Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="new_pass" class="form-control form-control-password" placeholder="Min. 8 characters" required>
                                <span class="input-group-text" onclick="togglePassword('new_pass', this)"><i class="far fa-eye"></i></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="conf_pass" class="form-control form-control-password" placeholder="Repeat new password" required>
                                <span class="input-group-text" onclick="togglePassword('conf_pass', this)"><i class="far fa-eye"></i></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dentist-primary w-100">Update Security</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Professional Information --}}
        <div class="col-lg-8">
            <form action="{{ route('dentist.profile.update') }}" method="POST">
                @csrf @method('PUT')
                <div class="card dentist-card">
                    <div class="card-header card-header-light">
                        <i class="fas fa-user-md me-2 text-muted"></i> Professional Credentials
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Full Name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="e.g. Dr. Ahmad Fauzi">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Email (Login Only)</label>
                                <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Contact Number</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $user->phone_number) }}" 
                                       placeholder="01X-XXXXXXX" pattern="01[0-9]-[0-9]{7,8}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold">Gender</label>
                                <select name="gender" class="form-control" required>
                                    <option value="" disabled {{ is_null($user->gender) ? 'selected' : '' }}>-- Select Gender --</option>
                                    <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label text-muted small fw-bold">Clinical Specialization</label>
                                <select name="specialization" class="form-control" required>
                                    <option value="" disabled {{ is_null($user->specialization) ? 'selected' : '' }}>-- Select Predefined Expertise --</option>
                                    <option value="General Dentistry" {{ old('specialization', $user->specialization) == 'General Dentistry' ? 'selected' : '' }}>General Dentistry</option>
                                    <option value="Orthodontics" {{ old('specialization', $user->specialization) == 'Orthodontics' ? 'selected' : '' }}>Orthodontics (Braces)</option>
                                    <option value="Periodontics" {{ old('specialization', $user->specialization) == 'Periodontics' ? 'selected' : '' }}>Periodontics (Gums)</option>
                                    <option value="Pediatric Dentistry" {{ old('specialization', $user->specialization) == 'Pediatric Dentistry' ? 'selected' : '' }}>Pediatric Dentistry (Kids)</option>
                                    <option value="Oral Surgery" {{ old('specialization', $user->specialization) == 'Oral Surgery' ? 'selected' : '' }}>Oral Surgery</option>
                                </select>
                            </div>
                        </div>

                        {{-- BUTTON AT THE BOTTOM --}}
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-dentist-primary px-5 shadow-sm">
                                <i class="fas fa-save me-2"></i> Save All Changes
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconElement) {
        const input = document.getElementById(inputId);
        const icon = iconElement.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('far', 'fas');
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fas', 'far');
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endsection