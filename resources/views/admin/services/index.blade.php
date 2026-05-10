@extends('layouts.app')

@section('content')
<style>
    /* 0. Page Background to make the White Box pop */
    .content-wrapper { 
        min-height: calc(100vh - 80px);
        background-color: #f8fbff; /* Soft clinic blue/grey background */
        padding-bottom: 3rem;
    }

    /* 1. Slimmer & Modern Header */
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
    }

    /* 2. THE WHITE BOX (The Main Container) */
    .card-white-box {
        border: none;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 15px 40px rgba(20, 33, 61, 0.06); /* Soft elegant shadow */
        overflow: hidden;
    }

    /* 3. Refined Price Styling */
    .price-static {
        font-size: 1.15rem;
        font-weight: 800;
        color: #1f6fff;
    }

    .price-static .currency {
        font-size: 0.8rem;
        margin-right: 2px;
        opacity: 0.8;
    }

    /* 4. Table UI Enhancements */
    .table thead th {
        background-color: #fcfdfe;
        border-bottom: 1px solid #f1f4f9;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        font-weight: 700;
        color: #6c7a92;
        padding: 1.2rem 1rem;
    }

    .table tbody td {
        padding: 1.2rem 1rem;
        border-bottom: 1px solid #f8faff;
    }

    .duration-chip {
        background: #eef5ff;
        color: #1f6fff;
        padding: 0.4rem 0.8rem;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
    }
    
    .status-pill {
        padding: 0.4rem 0.9rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #f1f5f9; color: #64748b; }

    /* 5. Action Buttons */
    .btn-edit-square {
        background: #f1f5f9;
        color: #1f6fff;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: 0.2s;
    }

    .btn-edit-square:hover {
        background: #1f6fff;
        color: white;
    }

    .btn-delete-square {
        background: #fee2e2;
        color: #dc2626;
        width: 38px;
        height: 38px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        border: none;
        transition: 0.2s;
    }

    .btn-delete-square:hover {
        background: #dc2626;
        color: white;
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid py-4">
        {{-- Header Section --}}
        <div class="services-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1"><i class="fas fa-tooth me-2"></i>Dental Services</h2>
                <p class="mb-0 text-white-50 small">Manage clinical procedures and pricing</p>
            </div>
            <a href="{{ route('admin.services.create') }}" class="btn-add-service">
                <i class="fas fa-plus me-2"></i>Add New Service
            </a>
        </div>
        
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        {{-- THE WHITE BOX CONTAINER --}}
        <div class="card card-white-box">
            <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-list-ul me-2 text-primary"></i>Service Catalog
                </h5>
                <span class="badge bg-primary-subtle text-primary ms-3 px-3 rounded-pill">{{ $services->count() }} Total</span>
            </div>
            
            <div class="card-body p-0">
                @if($services->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">#</th>
                                    <th>Service Details</th>
                                    <th>Price</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th class="pe-4 text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $service)
                                <tr>
                                    <td class="ps-4 text-muted fw-bold">{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $service->name }}</div>
                                        <div class="text-muted small">{{ Str::limit($service->description, 60) }}</div>
                                    </td>
                                    <td>
                                        <div class="price-static">
                                            <span class="currency">RM</span>{{ number_format($service->price, 2) }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="duration-chip">
                                            <i class="far fa-clock me-1"></i>{{ $service->duration_minutes }}m
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill {{ $service->is_active ? 'status-active' : 'status-inactive' }}">
                                            {{ $service->is_active ? 'ACTIVE' : 'INACTIVE' }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.services.edit', $service) }}" class="btn-edit-square" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-delete-square" onclick="return confirm('Archive this service?');" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="px-4 py-4 border-top bg-light-subtle">
                        {{ $services->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3 opacity-25"></i>
                        <h5 class="text-muted">No services found in your catalog.</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection