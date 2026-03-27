@extends('layouts.app')

@section('content')
<h1 class="mb-4">Dentist Dashboard</h1>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Today's Appointments</h5>
                <p class="card-text display-4">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">This Week</h5>
                <p class="card-text display-4">0</p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Total Patients</h5>
                <p class="card-text display-4">0</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Today's Schedule</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">No appointments scheduled for today</p>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Quick Actions</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-2">
                <a href="#" class="btn btn-outline-primary w-100 disabled">Manage Schedule</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="#" class="btn btn-outline-primary w-100 disabled">View Patients</a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="#" class="btn btn-outline-primary w-100 disabled">Consultation Records</a>
            </div>
        </div>
    </div>
</div>
@endsection
