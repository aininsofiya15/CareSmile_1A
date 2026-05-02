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
        width: 260px; 
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
        flex-direction: row;
        align-items: center;
        gap: 10px;
        width: 100%;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-action-group form {
        margin: 0;
        display: flex;
        align-items: center;
    }

    .btn-edit-staff {
        flex: 1;
        background: #ffffff;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 10px;
        border-radius: 12px;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .btn-edit-staff:hover {
        background: #f1f5f9;
        color: #1f6fff;
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
        transition: all 0.2s;
    }

    .btn-delete-staff:hover {
        background: #fecaca;
        transform: scale(1.05);
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
        
        {{-- Inner Header with Functional Search --}}
        <div class="workspace-header">
            <h5 class="font-weight-bold mb-0 text-dark">
                @if(request('search'))
                    Search Results for "{{ request('search') }}"
                @else
                    Active Professional Staff
                @endif
            </h5>
            
            <div class="d-flex align-items-center gap-3">
                @if(request('search'))
                    <a href="{{ route('admin.dentists') }}" class="text-primary small font-weight-bold text-decoration-none">
                        <i class="fas fa-times-circle mr-1"></i> Clear Search
                    </a>
                @endif
                
                <form action="{{ route('admin.dentists') }}" method="GET" class="m-0">
                    <div class="input-group" style="max-width: 320px;">
                        <input type="text" 
                               name="search" 
                               class="form-control border-right-0" 
                               placeholder="Search by name " 
                               value="{{ request('search') }}"
                               style="border-radius: 12px 0 0 12px; background: #f8fafc; border: 1px solid #e2e8f0; height: 45px;">
                        <div class="input-group-append">
                            <button class="btn btn-primary px-3" type="submit" style="border-radius: 0 12px 12px 0; background: #1f6fff; border: 1px solid #1f6fff;">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
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
                        <a href="{{ route('admin.dentists.edit', $dentist->id) }}" class="btn-edit-staff">Edit Profile</a>
                        
                        <form action="{{ route('admin.dentists.destroy', $dentist->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this staff record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-delete-staff">
                                <i class="fas fa-trash-alt fa-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5 w-100">
                    <div class="mb-3 text-muted opacity-25">
                        <i class="fas fa-search fa-4x"></i>
                    </div>
                    <p class="text-muted">No dentists found matching your criteria.</p>
                    @if(request('search'))
                        <a href="{{ route('admin.dentists') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">View All Staff</a>
                    @endif
                </div>
            @endforelse
        </div>

        <div class="footer-text">
            CareSmile Dental Management System &bull; Dentist Directory View
        </div>
    </div>
</div>
@endsection