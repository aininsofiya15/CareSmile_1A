@extends('layouts.app')

@section('content')
<style>
    /* Reuse your beautiful styles */
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
    }
    .profile-avatar-lg {
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 4px solid rgba(255, 255, 255, 0.3);
        font-size: 2.5rem;
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
    }
    .btn-save {
        background: #4361ee;
        color: white;
        border-radius: 12px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        border: none;
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Edit Staff Profile</h2>
            <p class="text-muted small">Update professional details for {{ $dentist->name }}</p>
        </div>
        <a href="{{ route('admin.dentists') }}" class="btn btn-white shadow-sm border px-4 rounded-pill">
            <i class="fas fa-arrow-left me-2"></i>Back to Directory
        </a>
    </div>

    <form action="{{ route('admin.dentists.update', $dentist->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- Left Side: Summary Card --}}
            <div class="col-lg-4">
                <div class="card profile-header-card p-4 text-center shadow-lg mb-4">
                    <div class="profile-avatar-lg rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 class="fw-bold mb-1">{{ $dentist->name }}</h3>
                    <div class="badge bg-white text-primary rounded-pill px-3 py-2 mb-3">
                        {{ $dentist->specialization }}
                    </div>
                    <p class="mb-0 opacity-75 small">Staff ID: #ST-{{ str_pad($dentist->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>

            {{-- Right Side: Edit Form --}}
            <div class="col-lg-8">
                <div class="card admin-card mb-4">
                    <div class="card-header bg-white border-0 pt-4 px-4">
                        <h5 class="fw-bold mb-0">Professional Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-custom">Full Name</label>
                                <input type="text" name="name" class="form-control form-control-custom" value="{{ $dentist->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Email Address</label>
                                <input type="email" name="email" class="form-control form-control-custom" value="{{ $dentist->email }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control form-control-custom" value="{{ $dentist->phone_number }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Specialization</label>
                                <select name="specialization" class="form-select form-control-custom">
                                    <option value="General Dentistry" {{ $dentist->specialization == 'General Dentistry' ? 'selected' : '' }}>General Dentistry</option>
                                    <option value="Orthodontics" {{ $dentist->specialization == 'Orthodontics' ? 'selected' : '' }}>Orthodontics</option>
                                    <option value="Periodontics" {{ $dentist->specialization == 'Periodontics' ? 'selected' : '' }}>Periodontics</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light px-4 rounded-pill">Reset Changes</button>
                            <button type="submit" class="btn btn-save shadow">
                                <i class="fas fa-save me-2"></i>Update Staff Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection