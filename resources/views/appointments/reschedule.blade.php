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

    .btn-secondary-custom {
        background-color: #6c757d;
        color: white;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        border: none;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary-custom:hover {
        opacity: 0.9;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Reschedule Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body">

        {{-- <form method="POST" action="{{ url('/appointments/'.$appointment->id.'/reschedule') }}"> --}}
        <form method="POST" action="{{ route('patient.appointments.reschedule.submit', $appointment->id) }}">
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
                <select name="time" class="form-select" required>
                        <option value="">Select Time</option>

                        <option value="09:00" {{ $appointment->appointment_time == '09:00:00' ? 'selected' : '' }}>09:00 AM</option>
                        <option value="09:30" {{ $appointment->appointment_time == '09:30:00' ? 'selected' : '' }}>09:30 AM</option>
                        <option value="10:00" {{ $appointment->appointment_time == '10:00:00' ? 'selected' : '' }}>10:00 AM</option>
                        <option value="10:30" {{ $appointment->appointment_time == '10:30:00' ? 'selected' : '' }}>10:30 AM</option>
                        <option value="11:00" {{ $appointment->appointment_time == '11:00:00' ? 'selected' : '' }}>11:00 AM</option>
                        <option value="11:30" {{ $appointment->appointment_time == '11:30:00' ? 'selected' : '' }}>11:30 AM</option>

                        <option value="14:00" {{ $appointment->appointment_time == '14:00:00' ? 'selected' : '' }}>02:00 PM</option>
                        <option value="14:30" {{ $appointment->appointment_time == '14:30:00' ? 'selected' : '' }}>02:30 PM</option>
                        <option value="15:00" {{ $appointment->appointment_time == '15:00:00' ? 'selected' : '' }}>03:00 PM</option>
                        <option value="15:30" {{ $appointment->appointment_time == '15:30:00' ? 'selected' : '' }}>03:30 PM</option>
                        <option value="16:00" {{ $appointment->appointment_time == '16:00:00' ? 'selected' : '' }}>04:00 PM</option>
                        <option value="16:30" {{ $appointment->appointment_time == '16:30:00' ? 'selected' : '' }}>04:30 PM</option>
                    </select>
            </div>

            {{-- <button class="btn-primary-custom">
                Submit
            </button> --}}
            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn-primary-custom">
                    Submit
                </button>

                <a href="{{ route('patient.appointments') }}" class="btn-secondary-custom">
                    Cancel
                </a>
            </div>
        </form>

    </div>
</div>

@endsection
