@extends('layouts.app')

@section('content')
<style>
    /* 1. Base Workspace Theme */
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
        padding: 1.2rem 1rem;
    }

    .table tbody td {
        padding: 0.8rem 1rem;
        vertical-align: middle;
    }

    /* 2. Patient Identity Styling */
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
    
    /* 3. Search Bar Styling */
    .search-input {
        border-radius: 12px 0 0 12px !important;
        padding: 0.6rem 1rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        height: 45px;
    }

    .search-btn {
        border-radius: 0 12px 12px 0 !important;
        background: #1f6fff;
        border: 1px solid #1f6fff;
        padding: 0 1.2rem;
        color: white;
    }

    /* 4. Pill-Style Action Buttons */
    .btn-action-group {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .btn-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 0.75rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-view-pill { background-color: #eef2ff; color: #4f46e5; }
    .btn-edit-pill { background-color: #eff6ff; color: #2563eb; }
    .btn-delete-pill { background-color: #fef2f2; color: #dc2626; }

    /* Loading Spinner for Live Search */
    #searchSpinner {
        display: none;
        margin-left: -35px;
        z-index: 10;
        color: #1f6fff;
    }
</style>

<div class="container-fluid py-4">
    <div class="row align-items-center mb-4 px-2">
        <div class="col">
            <h2 class="fw-bold mb-0" style="color: #1e293b;">Manage Patients</h2>
            <p class="text-muted small mb-0">Clinic Directory &bull; <span id="patientCount" class="fw-bold text-primary">{{ $patients->count() }}</span> Records</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.patients.create') }}" class="btn btn-primary px-4 shadow-sm" style="border-radius: 12px; font-weight: 600; background: #1f6fff; border: none;">
                <i class="fas fa-plus-circle me-2"></i>Add New Patient
            </a>
        </div>
    </div>

    <div class="card admin-card">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="row align-items-center">
                <div class="col">
                    <h5 class="fw-bold mb-0" id="tableTitle">
                        {{ request('search') ? 'Search Results' : 'Active Patient Registry' }}
                    </h5>
                </div>
                <div class="col-md-4">
                    <form action="{{ route('admin.patients') }}" method="GET" id="searchForm" onsubmit="return false;">
                        <div class="input-group shadow-sm" style="border-radius: 12px; overflow: hidden;">
                            <input type="text" 
                                   name="search" 
                                   id="searchInput"
                                   class="form-control border-end-0 search-input" 
                                   placeholder="Type to filter..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-spinner fa-spin" id="searchSpinner"></i>
                            </div>
                            <button class="btn search-btn" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive" id="tableContainer">
                {{-- The table content --}}
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">Patient Profile</th>
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
                                        <div class="patient-avatar shadow-sm">
                                            {{ strtoupper(substr($patient->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $patient->name }}</div>
                                            <div class="text-muted small" style="font-size: 0.7rem;">#CS-{{ str_pad($patient->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="text-muted small">{{ $patient->email }}</span></td>
                                <td>
                                    <div class="text-dark small fw-bold">{{ $patient->created_at->format('d M Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $patient->created_at->format('h:i A') }}</div>
                                </td>
                                <td><span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill" style="font-size: 0.7rem; font-weight: 700;">ACTIVE</span></td>
                                <td class="pe-4">
                                    <div class="btn-action-group">
                                        <a href="{{ route('admin.patients.show', $patient->id) }}" class="btn-pill btn-view-pill"><i class="far fa-eye"></i> View</a>
                                        <a href="{{ route('admin.patients.edit', $patient->id) }}" class="btn-pill btn-edit-pill"><i class="far fa-edit"></i> Edit</a>
                                        <form action="{{ route('admin.patients.destroy', $patient->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive record?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn-pill btn-delete-pill"><i class="far fa-trash-alt"></i> Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5"><p class="text-muted mb-0">No patients found.</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchSpinner = document.getElementById('searchSpinner');
        let timeout = null;

        searchInput.addEventListener('keyup', function() {
            clearTimeout(timeout);
            searchSpinner.style.display = 'block';

            // Wait 300ms after user stops typing to submit
            timeout = setTimeout(() => {
                const searchValue = searchInput.value;
                const url = new URL(window.location.href);
                url.searchParams.set('search', searchValue);

                // Fetch the new content without reloading the whole page
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Update the table body and count
                        document.getElementById('tableContainer').innerHTML = doc.getElementById('tableContainer').innerHTML;
                        document.getElementById('patientCount').innerText = doc.getElementById('patientCount').innerText;
                        
                        searchSpinner.style.display = 'none';
                        // Update URL without refreshing
                        window.history.pushState({}, '', url);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchSpinner.style.display = 'none';
                    });
            }, 300);
        });
    });
</script>
@endsection