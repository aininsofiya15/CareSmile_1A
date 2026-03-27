@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Manage Patients</h1>
    <a href="#" class="btn btn-primary disabled">Add New Patient</a>
</div>

<div class="card">
    <div class="card-header">
        <h5>All Patients</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info mb-0">
            <strong>Coming Soon!</strong> Patient management features will be implemented in the next module.
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Sample Data Table (Placeholder)</h5>
    </div>
    <div class="card-body">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center text-muted">No patients found</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
