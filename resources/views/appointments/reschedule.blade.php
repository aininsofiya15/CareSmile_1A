@extends('layouts.app')

@section('content')

<style>
    .page-header {
        margin-bottom: 20px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #1f2937;
    }

    .card-custom {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    .btn-primary-custom {
        background-color: #3b82f6;
        color: white;
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-primary-custom:hover {
        opacity: 0.9;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Reschedule Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body">

        <form method="POST" action="{{ url('/appointments/'.$appointment->id.'/reschedule') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">New Date</label>
                {{-- <input type="date" class="form-control"> --}}
                <input type="date" name="date" class="form-control" min="{{ date('Y-m-d') }}"
                    value="{{ $appointment->appointment_date }}">
            </div>

            <div class="mb-3">
                <label class="form-label">New Time</label>
                {{-- <input type="time" class="form-control"> --}}
                <input type="time" name="time" class="form-control"
                    value="{{ $appointment->appointment_time }}">
            </div>

            <button class="btn-primary-custom">
                Submit
            </button>
        </form>

    </div>
</div>

@endsection
