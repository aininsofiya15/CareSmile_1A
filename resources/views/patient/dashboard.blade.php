@extends('layouts.app')

@section('content')
<h1 class="mb-4">Patient Dashboard</h1>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">My Appointments</h5>
                <p class="card-text">View and manage your appointments</p>
                <a href="#" class="btn btn-primary disabled">Book Appointment</a>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">My Records</h5>
                <p class="card-text">View your medical records</p>
                <a href="#" class="btn btn-primary disabled">View Records</a>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5>Upcoming Appointments</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">No upcoming appointments</p>
    </div>
</div>
@endsection
