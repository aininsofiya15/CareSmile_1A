@extends('layouts.app')

@section('content')
<style>
    /* 1. Workspace Wrapper to match the Directory */
    .workspace-container {
        background: #ffffff;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-top: 20px;
    }

    /* 2. Refined Form Labels & Inputs */
    .form-label-custom {
        font-weight: 700;
        color: #334155;
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-custom {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
        background: #f8fafc !important;
        font-size: 0.9rem;
        transition: 0.2s;
    }

    .form-control-custom:focus {
        background: #ffffff !important;
        border-color: #1f6fff !important;
        box-shadow: 0 0 0 4px rgba(31, 111, 255, 0.1) !important;
    }

    .input-group-text-custom {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-right: none;
        color: #94a3b8;
        border-radius: 12px 0 0 12px;
        display: flex;
        align-items: center;
        padding: 0 15px;
    }

    /* 3. Section Headers */
    .form-section-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section-title::after {
        content: "";
        height: 1px;
        background: #f1f5f9;
        flex-grow: 1;
    }

    /* 4. Styled Selects */
    select.form-control-custom {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%2394a3b8' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 1.5em 1.5em;
    }
</style>

<div class="container py-4">
    {{-- Page Header --}}
    <div class="mb-4 px-2">
        <h2 class="font-weight-bold text-dark mb-1">Add New Staff</h2>
        <p class="text-muted small">Register a professional dentist to the CareSmile system.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="workspace-container">
                <form action="{{ route('admin.dentists.store') }}" method="POST">
                    @csrf

                    {{-- Section 1: Personal Details --}}
                    <div class="form-section-title">Personal Details</div>
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Full Name</label>
                            <input type="text" name="name" class="form-control-custom w-100 @error('name') is-invalid @enderror" placeholder="e.g. Dr. Siti Aminah" required value="{{ old('name') }}">
                            @error('name') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Email Address</label>
                            <input type="email" name="email" class="form-control-custom w-100 @error('email') is-invalid @enderror" placeholder="dentist@caresmile.com" required value="{{ old('email') }}">
                            @error('email') <small class="text-danger mt-1 d-block">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control-custom w-100" placeholder="01X-XXXXXXX" required value="{{ old('phone_number') }}">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label-custom">Gender</label>
                            <select name="gender" class="form-control-custom w-100" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section 2: Professional Details --}}
                    <div class="form-section-title">Professional Details</div>
                    <div class="row mb-4">
                        <div class="col-12 mb-3">
                            <label class="form-label-custom">Specialization</label>
                            <select name="specialization" class="form-control-custom w-100" required>
                                <option value="General Dentistry">General Dentistry</option>
                                <option value="Orthodontics">Orthodontics (Braces)</option>
                                <option value="Periodontics">Periodontics (Gums)</option>
                                <option value="Pediatric Dentistry">Pediatric Dentistry (Kids)</option>
                                <option value="Oral Surgery">Oral Surgery</option>
                            </select>
                        </div>
                    </div>

                    {{-- Section 3: Account Security (Matches Patient Style) --}}
                    <div class="form-section-title">Account Security</div>
                    <div class="row mb-5">
                        <div class="col-12 mb-3">
                            <label class="form-label-custom">Temporary Password</label>
                            <div class="input-group">
                                <span class="input-group-text-custom">
                                    <i class="fas fa-lock text-sm"></i>
                                </span>
                                <input type="text" name="password" class="form-control-custom flex-grow-1" style="border-radius: 0 12px 12px 0 !important; border-left: none !important;" value="CareSmileStaff2026!" readonly>
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i> Staff will be asked to change this upon first login.
                            </small>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                        <a href="{{ route('admin.dentists') }}" class="text-muted font-weight-bold text-decoration-none">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Directory
                        </a>
                        <div class="d-flex gap-3">
                            <button type="reset" class="btn btn-light px-4" style="border-radius: 12px; font-weight: 700;">Reset</button>
                            <button type="submit" class="btn btn-primary px-5 shadow-sm" style="border-radius: 12px; background: #1f6fff; font-weight: 700; border: none;">
                                <i class="fas fa-user-check me-2"></i>Create Account
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
