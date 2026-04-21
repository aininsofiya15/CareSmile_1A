@extends('layouts.app')

@section('content')
<style>
    .form-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .form-header {
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        padding: 1.5rem;
        color: white;
    }
    
    .form-body {
        padding: 2rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #14213d;
    }
    
    .required-star {
        color: #dc2626;
    }
    
    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.7rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #1f6fff;
        box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.1);
    }
    
    .warning-box {
        background: #fef3c7;
        border-left: 4px solid #f59e0b;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }
    
    .btn-update {
        background: #1f6fff;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.8rem;
        font-weight: 600;
    }
    
    .btn-update:hover {
        background: #1557d6;
    }
    
    .btn-cancel {
        background: #f3f4f6;
        color: #4b5563;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.8rem;
        font-weight: 600;
        text-decoration: none;
    }
</style>

<div class="container py-3">
    <div class="form-card">
        <div class="form-header">
            <h3 class="fw-bold mb-1"><i class="fas fa-edit me-2"></i>Edit Dental Service</h3>
            <p class="mb-0 text-white-50">Update the details of {{ $service->name }}</p>
        </div>
        
        <div class="form-body">
            <form action="{{ route('admin.services.update', $service) }}" method="POST">
                @csrf
                @method('PUT')  {{-- THIS IS IMPORTANT FOR UPDATE --}}
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Service Name <span class="required-star">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $service->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Duration <span class="required-star">*</span></label>
                        <select name="duration_minutes" class="form-select @error('duration_minutes') is-invalid @enderror" required>
                            <option value="15" {{ old('duration_minutes', $service->duration_minutes) == 15 ? 'selected' : '' }}>15 minutes</option>
                            <option value="30" {{ old('duration_minutes', $service->duration_minutes) == 30 ? 'selected' : '' }}>30 minutes</option>
                            <option value="45" {{ old('duration_minutes', $service->duration_minutes) == 45 ? 'selected' : '' }}>45 minutes</option>
                            <option value="60" {{ old('duration_minutes', $service->duration_minutes) == 60 ? 'selected' : '' }}>1 hour</option>
                            <option value="90" {{ old('duration_minutes', $service->duration_minutes) == 90 ? 'selected' : '' }}>1 hour 30 minutes</option>
                            <option value="120" {{ old('duration_minutes', $service->duration_minutes) == 120 ? 'selected' : '' }}>2 hours</option>
                        </select>
                        @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Price (RM) <span class="required-star">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                   step="0.01" min="0" value="{{ old('price', $service->price) }}" required>
                        </div>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $service->is_active) == 1 ? 'selected' : '' }}>✅ Active (Available for booking)</option>
                            <option value="0" {{ old('is_active', $service->is_active) == 0 ? 'selected' : '' }}>⛔ Inactive (Temporarily unavailable)</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Description <span class="required-star">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="4" required>{{ old('description', $service->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                
                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.services.index') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn-update">
                        <i class="fas fa-save me-2"></i>Update Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection