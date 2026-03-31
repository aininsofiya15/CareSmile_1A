@extends('layouts.app')

@section('content')

<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.08);
        --shadow-soft: 0 10px 30px rgba(20, 33, 61, 0.08);
        --radius-lg: 16px;
    }

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
    }

    .form-control, .form-select {
        border-radius: 12px;
        padding: 0.75rem 1rem;
    }

    .btn-primary-custom {
        background: var(--brand-blue);
        color: white;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        border: none;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Book Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body">

        <form action="/appointments/store" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Time</label>
                <input type="time" name="time" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Service</label>
                <select name="service" class="form-select">
                    <option>Scaling</option>
                    <option>Extraction</option>
                    <option>Whitening</option>
                </select>
            </div>

            <button type="submit" class="btn-primary-custom">
                Book Appointment
            </button>
        </form>

    </div>
</div>

@endsection
