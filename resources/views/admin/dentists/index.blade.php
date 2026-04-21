@extends('layouts.app')

@section('content')
<style>
    /* 1. The Big Workspace Background */
    .workspace-container {
        background: #ffffff;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        min-height: 650px;
        margin-top: 20px;
    }

    /* 2. Header inside Workspace */
    .workspace-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 1px solid #f8fafc;
    }

    /* 3. The Grid of Square Cards */
    .dentist-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 25px;
    }

    /* 4. Square Card Styling */
    .dentist-card {
        background: #f8fafc;
        width: 260px; /* Perfect square-ish width */
        border-radius: 24px;
        padding: 24px;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .dentist-card:hover {
        background: #ffffff;
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(31, 111, 255, 0.1) !important;
        border-color: rgba(31, 111, 255, 0.2);
    }

    /* 5. Avatar & Badge */
    .avatar-sq {
        width: 75px;
        height: 75px;
        background: #ffffff;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.02);
    }

    .spec-tag {
        background: #1f6fff;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.6rem;
        padding: 4px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }

    /* 6. Action Buttons */
    .btn-action-group {
        display: flex;
        gap: 8px;
        width: 100%;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-edit-staff {
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 10px;
        border-radius: 12px;
        flex-grow: 1;
        text-align: center;
        text-decoration: none;
    }

    .btn-delete-staff {
        background: #fee2e2;
        color: #ef4444;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .footer-text {
        margin-top: 40px;
        text-align: center;
        font-size: 0.8rem;
        color: #94a3b8;
    }
</style>

<div class="container py-4">
    {{-- Main Page Title --}}
    <div class="d-flex justify-content-between align-items-center mb-3 px-2">
        <h2 class="font-weight-bold text-dark mb-0">Dentist Directory</h2>
        <a href="{{ route('admin.dentists.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 15px; background: #1f6fff; font-weight: 600;">
            <i class="fas fa-plus mr-2"></i> Create Account
        </a>
    </div>

    {{-- The Big Workspace Container --}}
    <div class="workspace-container">
        
        {{-- Inner Header with Search --}}
        <div class="workspace-header">
            <h5 class="font-weight-bold mb-0 text-dark">Active Professional Staff</h5>
            <div class="input-group" style="max-width: 300px;">
                <input type="text" class="form-control" placeholder="Search by name..." style="border-radius: 12px; background: #f8fafc; border: 1px solid #e2e8f0;">
            </div>
        </div>

        {{-- Dentist Grid inside the Workspace --}}
        <div class="dentist-grid">
            @forelse($dentists as $dentist)
                <div class="dentist-card shadow-sm">
                    <div class="avatar-sq">
                        <i class="fas {{ $dentist->gender == 'Female' ? 'fa-user-nurse' : 'fa-user-md' }} fa-2x text-primary"></i>
                    </div>

                    <h5 class="font-weight-bold text-dark mb-1">{{ $dentist->name }}</h5>
                    <span class="spec-tag">{{ $dentist->specialization ?? 'General' }}</span>

                    <div class="small text-muted mb-1 text-truncate w-100 text-center">
                        <i class="fas fa-envelope mr-1 opacity-50"></i> {{ $dentist->email }}
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fas fa-phone mr-1 opacity-50"></i> {{ $dentist->phone_number ?? 'No Phone' }}
                    </div>

                    {{-- Action Buttons --}}
                    <div class="btn-action-group">
                        <a href="#" class="btn-edit-staff">Edit Profile</a>
                        <button class="btn-delete-staff">
                            <i class="fas fa-trash-alt fa-sm"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 w-100">
                    <p class="text-muted">No dentists registered in the system.</p>
                </div>
            @endforelse
        </div>

        <div class="footer-text">
            CareSmile Dental Management System &bull; Dentist Directory View
        </div>
    </div>
</div>
@endsection