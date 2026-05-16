@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
    }

    /* --- The Blue Banner Header Styles --- */
    .banner-header {
        background-color: var(--brand-blue);
        border-radius: 12px;
        padding: 24px 32px;
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

    /* --- CareSmile Unified Theme --- */
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
        background-color: var(--brand-blue);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s;
    }

    .btn-dentist-primary:hover {
        background-color: #1456cc;
        box-shadow: 0 4px 12px rgba(31, 111, 255, 0.3);
    }

    .form-control { 
        border-radius: 8px; 
        padding: 0.6rem 1rem; 
        border: 1px solid #d1d5db; 
    }
    
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
        width: 100px; 
        height: 100px; 
        background-color: var(--brand-blue-light); 
        color: var(--brand-blue);
        border-radius: 50%; 
        display: flex; 
        align-items: center; 
        justify-content: center;
        font-size: 2.5rem; 
        margin: 0 auto 1rem auto;
    }

    /* --- Password Strength Meter & Checklist Styles --- */
    .strength-meter { 
        height: 6px; 
        background-color: #e5e7eb; 
        border-radius: 3px; 
        margin: 10px 0; 
        overflow: hidden; 
        display: none; 
    }
    #strength-bar { 
        height: 100%; 
        width: 0%; 
        transition: all 0.3s ease; 
    }

    #password-checklist {
        display: none;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 15px;
        margin-top: 10px;
        margin-bottom: 5px;
    }
    .check-item { 
        font-size: 0.8rem; 
        color: #9ca3af; 
        margin-bottom: 4px; 
        display: flex; 
        align-items: center; 
    }
    .check-item i { margin-right: 8px; width: 14px; }
    .check-item.valid { color: #10b981; font-weight: 600; }
</style>

<div class="container-fluid py-3">
    
    <div class="banner-header">
        <div class="banner-left">
            <div class="banner-title-wrapper">
                <i class="fas fa-user-md banner-icon"></i> 
                <h1 class="banner-title">Staff Profile</h1>
            </div>
            <p class="banner-subtitle">Manage your professional credentials and account password.</p>
        </div>
    </div>

    <div class="row g-4">
        {{-- LEFT COLUMN: Avatar & Password --}}
        <div class="col-lg-4">
            <div class="card dentist-card text-center p-4">
                <div class="profile-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <h4 class="fw-bold mb-1">Dr. {{ $user->name }}</h4>
                <p class="text-muted mb-3">Staff ID: #DEN-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">Clinic Staff</span>
            </div>

            <div class="card dentist-card">
                <div class="card-header card-header-light">
                    <i class="fas fa-lock me-2 text-muted"></i> Password
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
                                {{-- Added the oninput event here to trigger the validation --}}
                                <input type="password" name="password" id="new_pass" class="form-control form-control-password" placeholder="Min. 8 characters" required oninput="validatePassword(this.value)">
                                <span class="input-group-text" onclick="togglePassword('new_pass', this)"><i class="far fa-eye"></i></span>
                            </div>

                            {{-- Password Strength Meter and Checklist --}}
                            <div class="strength-meter" id="meter-container">
                                <div id="strength-bar"></div>
                            </div>
        
                            <div id="password-checklist">
                                <p class="small fw-bold mb-2 text-dark">Password requirements:</p>
                                <div class="check-item" id="req-length"><i class="fas fa-circle"></i> 8+ characters</div>
                                <div class="check-item" id="req-upper"><i class="fas fa-circle"></i> One uppercase letter</div>
                                <div class="check-item" id="req-number"><i class="fas fa-circle"></i> One number</div>
                                <div class="check-item" id="req-special"><i class="fas fa-circle"></i> One special character</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="conf_pass" class="form-control form-control-password" placeholder="Repeat new password" required>
                                <span class="input-group-text" onclick="togglePassword('conf_pass', this)"><i class="far fa-eye"></i></span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-dentist-primary w-100">Update Password</button>
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
                        <i class="fas fa-id-badge me-2 text-muted"></i> Professional Credentials
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
    // Existing Eye Toggle Logic
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

    // Ported Checklist Validation Logic
    function validatePassword(pass) {
        const checklist = document.getElementById('password-checklist');
        const meter = document.getElementById('meter-container');
        const bar = document.getElementById('strength-bar');

        if (pass.length > 0) {
            checklist.style.display = 'block';
            meter.style.display = 'block';
        } else {
            checklist.style.display = 'none';
            meter.style.display = 'none';
        }

        const rules = {
            'req-length': pass.length >= 8,
            'req-upper': /[A-Z]/.test(pass),
            'req-number': /[0-9]/.test(pass),
            'req-special': /[!@#$%^&*(),.?":{}|<>]/.test(pass)
        };

        let score = 0;
        for (const [id, passed] of Object.entries(rules)) {
            const el = document.getElementById(id);
            if (passed) {
                el.classList.add('valid');
                el.querySelector('i').className = 'fas fa-check-circle';
                score++;
            } else {
                el.classList.remove('valid');
                el.querySelector('i').className = 'fas fa-circle';
            }
        }

        const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
        bar.style.width = (score / 4) * 100 + '%';
        bar.style.backgroundColor = colors[score - 1] || '#e5e7eb';
    }
</script>
@endsection