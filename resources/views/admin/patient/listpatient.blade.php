@extends('layouts.app')

@section('content')
<style>
    .admin-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        background: white;
    }
    .table thead th {
        background-color: #f8fafc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        color: #64748b;
        border-top: none;
    }
    .patient-avatar {
        width: 38px;
        height: 38px;
        background-color: #eef2ff;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .search-input {
        border-radius: 12px;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }
</style>

<div class="container-fluid py-4">
    {{-- Header Section --}}
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold mb-0" style="color: #1e293b;">Manage Patients</h2>
            <p class="text-muted small mb-0">Total Patients: <span class="fw-bold text-primary">{{ $patients->count() }}</span></p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.patients.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; font-weight: 600;">
                <i class="fas fa-plus-circle me-2"></i>Add New Patient
            </a>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card admin-card">
        <div class="card-header bg-white border-0 py-4">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="fw-bold mb-0">Registered Patients List</h5>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 search-input" placeholder="Search by name or email...">
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Patient</th>
                            <th>Email Address</th>
                            <th>Registration Date</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patients as $patient)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="patient-avatar">
                                            {{ substr($patient->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $patient->name }}</div>
                                            <div class="text-muted small">ID: #CS-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $patient->email }}</span>
                                </td>
                                <td>
                                    <div class="text-dark">{{ $patient->created_at->format('d M Y') }}</div>
                                    <div class="text-muted x-small" style="font-size: 0.7rem;">{{ $patient->created_at->format('h:i A') }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" style="font-weight: 500;">Active</span>
                                </td>
                                <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-1">
                                    {{-- 1. VIEW BUTTON --}}
                                    <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn btn-action btn-outline-primary" title="View Profile">
                                        <i class="fas fa-eye fa-sm"></i>
                                    </a>

                                    {{-- 2. EDIT BUTTON --}}
                                    <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn btn-action btn-outline-secondary" title="Edit Patient">
                                        <i class="fas fa-pen fa-sm"></i>
                                    </a>

                                    {{-- 3. DELETE BUTTON --}}
                                    <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this patient? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt fa-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">No patients found.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- Footer --}}
        <div class="card-footer bg-white border-0 py-3 text-center">
            <p class="text-muted small mb-0">CareSmile Dental Management System v1.0</p>
        </div>
    </div>
</div>
@endsection