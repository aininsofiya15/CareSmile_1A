@extends('layouts.app')

@section('content')
<style>
    .services-header {
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        border-radius: 20px;
        padding: 1.8rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    
    .btn-add-service {
        background: white;
        color: #1f6fff;
        border: none;
        border-radius: 12px;
        padding: 0.6rem 1.2rem;
        font-weight: 600;
        transition: all 0.2s;
    }
    
    .btn-add-service:hover {
        transform: translateY(-2px);
        background: #f8f9fa;
    }
    
    .service-card {
        border: 1px solid rgba(31, 111, 255, 0.1);
        border-radius: 16px;
        transition: all 0.2s;
        margin-bottom: 1rem;
    }
    
    .service-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(31, 111, 255, 0.1);
    }
    
    .price-badge {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1f6fff;
    }
    
    .duration-chip {
        background: #eef5ff;
        color: #1f6fff;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-active {
        background: #10b981;
        color: white;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .badge-inactive {
        background: #9ca3af;
        color: white;
        padding: 0.2rem 0.7rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .btn-edit {
        background: #eef5ff;
        color: #1f6fff;
        border: none;
        border-radius: 8px;
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    
    .btn-edit:hover {
        background: #1f6fff;
        color: white;
    }
    
    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 8px;
        padding: 0.3rem 0.8rem;
        font-size: 0.8rem;
        transition: all 0.2s;
    }
    
    .btn-delete:hover {
        background: #dc2626;
        color: white;
    }
</style>

<div class="container-fluid py-3">
    {{-- Header --}}
    <div class="services-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1"><i class="fas fa-tooth me-2"></i>Dental Services</h2>
            <p class="mb-0 text-white-50">Manage all dental services offered at CareSmile Clinic</p>
        </div>
        <div>
            <a href="{{ route('admin.services.create') }}" class="btn-add-service">
                <i class="fas fa-plus me-2"></i>Add New Service
            </a>
        </div>
    </div>
    
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- Services Table --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0">All Services</h5>
        </div>
        <div class="card-body p-0">
            @if($services->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: #f8f9fa;">
                            <tr>
                                <th class="px-4">#</th>
                                <th>Service Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Duration</th>
                                <th>Status</th>
                                <th class="px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                            <tr>
                                <td class="px-4">{{ $loop->iteration }}</td>
                                <td><strong>{{ $service->name }}</strong></td>
                                <td>{{ Str::limit($service->description, 50) }}</td>
                                <td><span class="price-badge">RM {{ number_format($service->price, 2) }}</span></td>
                                <td><span class="duration-chip"><i class="far fa-clock me-1"></i>{{ $service->duration_minutes }} mins</span></td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge-active"><i class="fas fa-check-circle me-1"></i>Active</span>
                                    @else
                                        <span class="badge-inactive"><i class="fas fa-ban me-1"></i>Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    <a href="{{ route('admin.services.edit', $service) }}" class="btn-edit me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.services.destroy', $service) }}" method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure you want to delete {{ $service->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    }$
                </div>
                <div class="px-4 py-3">
                    {{ $services->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tooth fa-4x text-muted mb-3" style="opacity: 0.3;"></i>
                    <h5 class="text-muted">No dental services found</h5>
                    <p class="text-muted">Click "Add New Service" to create your first dental service.</p>
                    <a href="{{ route('admin.services.create') }}" class="btn-add-service mt-2">
                        <i class="fas fa-plus me-2"></i>Add Your First Service
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection