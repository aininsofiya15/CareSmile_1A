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
        margin-bottom: 0.5rem;
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
    
    .btn-submit {
        background: #1f6fff;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1.8rem;
        font-weight: 600;
    }
    
    .btn-submit:hover {
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
    
    .helper-text {
        font-size: 0.7rem;
        color: #6c7a92;
        margin-top: 0.3rem;
    }
</style>

<div class="container py-3">
    <div class="form-card">
        <div class="form-header">
            <h3 class="fw-bold mb-1"><i class="fas fa-plus-circle me-2"></i>Add New Dental Service</h3>
            <p class="mb-0 text-white-50">Create a new service to be offered at CareSmile Clinic</p>
        </div>
        
        <div class="form-body">
            <form action="{{ route('admin.services.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Service Name <span class="required-star">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g., Scaling & Polishing, Teeth Whitening" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="helper-text">Enter a unique name for the dental service.</div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Duration <span class="required-star">*</span></label>
                        <select name="duration_minutes" class="form-select @error('duration_minutes') is-invalid @enderror" required>
                            <option value="">Select duration</option>
                            <option value="15">15 minutes</option>
                            <option value="30">30 minutes</option>
                            <option value="45">45 minutes</option>
                            <option value="60">1 hour</option>
                            <option value="90">1 hour 30 minutes</option>
                            <option value="120">2 hours</option>
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
                                   step="0.01" min="0" placeholder="0.00" value="{{ old('price') }}" required>
                        </div>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="helper-text">Example: 120.00 for scaling, 350.00 for whitening</div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-select">
                            <option value="1" selected>✅ Active (Available for booking)</option>
                            <option value="0">⛔ Inactive (Temporarily unavailable)</option>
                        </select>
                        <div class="helper-text">Inactive services will not appear for patient booking.</div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Description <span class="required-star">*</span></label>
                    <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                              rows="4" placeholder="Describe the dental service, what it includes, benefits, etc." required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="helper-text">Provide a clear description to help patients understand the service.</div>
                </div>
                
                <div class="d-flex justify-content-end gap-3 mt-4 pt-3 border-top">
                    <a href="{{ route('admin.services.index') }}" class="btn-cancel">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn-submit">
                        <i class="fas fa-save me-2"></i>Save Service
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection