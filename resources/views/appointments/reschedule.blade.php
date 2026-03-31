@extends('layouts.app')

@section('content')

<style>
    .card-custom {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Reschedule Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body">

        <form>
            <div class="mb-3">
                <label class="form-label">New Date</label>
                <input type="date" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">New Time</label>
                <input type="time" class="form-control">
            </div>

            <button class="btn-primary-custom">
                Submit Request
            </button>
        </form>

    </div>
</div>

@endsection
