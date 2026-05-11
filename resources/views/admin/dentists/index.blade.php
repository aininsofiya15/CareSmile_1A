@extends('layouts.app')

@section('content')
<style>
    /* 0. Page Background */
    .content-wrapper {
        min-height: calc(100vh - 80px);
        background-color: #f8fbff;
        padding-bottom: 3rem;
    }

    /* 1. Header — identical to Services */
    .services-header {
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(31, 111, 255, 0.2);
    }

    .btn-add-service {
        background: white;
        color: #1f6fff;
        border: none;
        border-radius: 12px;
        padding: 0.7rem 1.4rem;
        font-weight: 700;
        transition: all 0.2s;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .btn-add-service:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        background: #fff;
        color: #1f6fff;
        text-decoration: none;
    }

    /* 2. White Box — identical to Services */
    .card-white-box {
        border: none;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 15px 40px rgba(20, 33, 61, 0.06);
        overflow: hidden;
    }

    /* 3. Search bar inside card header */
    .search-input {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px 0 0 12px;
        height: 42px;
        font-size: 0.875rem;
    }

    .search-input:focus {
        background: #fff;
        border-color: #1f6fff;
        box-shadow: none;
    }

    .btn-search {
        background: #1f6fff;
        border: 1px solid #1f6fff;
        border-radius: 0 12px 12px 0;
        color: white;
        padding: 0 1rem;
    }

    .btn-search:hover {
        background: #1557d6;
    }

    /* 4. Dentist Grid */
    .dentist-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 1.5rem;
    }

    /* 5. Dentist Card */
    .dentist-card {
        background: #f8fafc;
        width: 240px;
        border-radius: 20px;
        padding: 22px 18px 18px;
        border: 1px solid transparent;
        display: flex;
        flex-direction: column;
        align-items: center;
        transition: all 0.3s ease;
    }

    .dentist-card:hover {
        background: #ffffff;
        transform: translateY(-6px);
        box-shadow: 0 15px 30px rgba(31, 111, 255, 0.1);
        border-color: rgba(31, 111, 255, 0.2);
    }

    /* 6. Avatar */
    .avatar-sq {
        width: 70px;
        height: 70px;
        background: #ffffff;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }

    /* 7. Specialisation tag */
    .spec-tag {
        background: #1f6fff;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.6rem;
        padding: 4px 12px;
        border-radius: 50px;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 12px;
    }

    /* 8. Action buttons — matching Services edit/delete style */
    .btn-action-group {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        margin-top: auto;
        padding-top: 14px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-action-group form {
        margin: 0;
        display: flex;
        align-items: center;
    }

    .btn-edit-staff {
        flex: 1;
        background: #f1f5f9;
        color: #1f6fff;
        border: none;
        font-weight: 700;
        font-size: 0.8rem;
        padding: 0;
        height: 38px;
        border-radius: 10px;
        text-align: center;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .btn-edit-staff:hover {
        background: #1f6fff;
        color: white;
        text-decoration: none;
    }

    .btn-delete-staff {
        background: #fee2e2;
        color: #dc2626;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }

    .btn-delete-staff:hover {
        background: #dc2626;
        color: white;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid py-4">

        {{-- Header — same as Services --}}
        <div class="services-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-user-md me-2"></i>Dentist Directory</h2>
                <p class="mb-0 text-white-50 small">Manage professional staff accounts</p>
            </div>
            <a href="{{ route('admin.dentists.create') }}" class="btn-add-service">
                <i class="fas fa-plus me-2"></i>Create Account
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- White Box --}}
        <div class="card card-white-box">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-users me-2 text-primary"></i>
                        @if(request('search'))
                            Results for "{{ request('search') }}"
                        @else
                            Active Professional Staff
                        @endif
                    </h5>
                    <span class="badge bg-primary-subtle text-primary ms-3 px-3 rounded-pill">{{ $dentists->count() }} Total</span>
                </div>

                {{-- Search --}}
                <div class="d-flex align-items-center gap-2">
                    @if(request('search'))
                        <a href="{{ route('admin.dentists') }}" class="text-primary small fw-bold text-decoration-none">
                            <i class="fas fa-times-circle me-1"></i>Clear
                        </a>
                    @endif
                    <form action="{{ route('admin.dentists') }}" method="GET" class="m-0">
                        <div class="input-group" style="max-width: 280px;">
                            <input type="text"
                                   name="search"
                                   class="form-control search-input"
                                   placeholder="Search by name…"
                                   value="{{ request('search') }}">
                            <button class="btn btn-search" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body p-0">
                @if($dentists->count() > 0)
                    <div class="dentist-grid">
                        @foreach($dentists as $dentist)
                            <div class="dentist-card shadow-sm">
                                <div class="avatar-sq">
                                    <i class="fas {{ $dentist->gender == 'Female' ? 'fa-user-nurse' : 'fa-user-md' }} fa-2x text-primary"></i>
                                </div>

                                <h6 class="fw-bold text-dark mb-1 text-center">{{ $dentist->name }}</h6>
                                <span class="spec-tag">{{ $dentist->specialization ?? 'General' }}</span>

                                <div class="small text-muted mb-1 text-truncate w-100 text-center">
                                    <i class="fas fa-envelope me-1 opacity-50"></i>{{ $dentist->email }}
                                </div>
                                <div class="small text-muted mb-2 text-center">
                                    <i class="fas fa-phone me-1 opacity-50"></i>{{ $dentist->phone_number ?? 'No Phone' }}
                                </div>

                                <div class="btn-action-group">
                                    <a href="{{ route('admin.dentists.edit', $dentist->id) }}" class="btn-edit-staff">
                                        <i class="fas fa-pen me-1"></i>Edit
                                    </a>
                                    <form action="{{ route('admin.dentists.destroy', $dentist->id) }}" method="POST" onsubmit="return confirm('Delete this staff record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete-staff" title="Delete">
                                            <i class="fas fa-trash fa-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3 opacity-25"></i>
                        <h5 class="text-muted">
                            @if(request('search'))
                                No dentists found matching your search.
                            @else
                                No dentists in the directory yet.
                            @endif
                        </h5>
                        @if(request('search'))
                            <a href="{{ route('admin.dentists') }}" class="btn btn-primary btn-sm rounded-pill px-4 mt-2">View All Staff</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection