@extends('layouts.app')

@section('content')
<style>
    .form-container {
        background-color: #ffffff;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
        max-width: 600px; /* Narrower for single column */
        margin: 2rem auto;
        overflow: hidden;
    }

    .form-side {
        padding: 3.5rem;
    }

    .form-title { font-weight: 800; color: #1e293b; font-size: 2rem; }
    
    .input-group-text {
        background-color: #f8fafc;
        border-right: none;
        color: #94a3b8;
        border-radius: 12px 0 0 12px;
    }

    .form-control {
        border-left: none;
        padding: 0.75rem;
        background-color: #f8fafc;
        border-radius: 0 12px 12px 0;
    }

    .form-control:focus {
        background-color: #fff;
        box-shadow: none;
        border-color: #4361ee;
    }

    .input-group:focus-within .input-group-text {
        border-color: #4361ee;
        color: #4361ee;
    }

    .btn-create {
        background: #4361ee;
        color: white;
        font-weight: 600;
        padding: 1rem;
        border-radius: 12px;
        border: none;
        transition: all 0.3s;
    }

    .btn-create:hover {
        background: #3751d4;
        transform: translateY(-2px);
        color: white;
    }
</style>

<div class="container">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.patients') }}" class="text-decoration-none">Patients</a></li>
            <li class="breadcrumb-item active">Add New</li>
        </ol>
    </nav>

    <div class="form-container">
        <div class="form-side">
            <h2 class="form-title mb-2">Add New Patient</h2>
            <p class="text-muted mb-4">Create a new medical record for the clinic.</p>

            <form action="{{ route('admin.patients.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user text-sm"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Enter patient name" required value="{{ old('name') }}">
                    </div>
                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope text-sm"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="patient@email.com" required value="{{ old('email') }}">
                    </div>
                    @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted">Temporary Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock text-sm"></i></span>
                        <input type="text" name="password" class="form-control" value="CareSmile2026!" readonly>
                    </div>
                    <small class="text-muted d-block mt-2">Patients will be asked to change this upon first login.</small>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-create shadow-sm">
                        <i class="fas fa-user-plus me-2"></i>Register Patient
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 