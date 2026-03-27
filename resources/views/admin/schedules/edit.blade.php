@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-dark: #1456cc;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.08);
        --shadow-soft: 0 10px 30px rgba(20, 33, 61, 0.08);
        --radius-lg: 16px;
    }

    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
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
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.1);
        outline: none;
    }

    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--card-border);
    }

    .btn-primary-custom {
        background: var(--brand-blue);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-primary-custom:hover {
        background: var(--brand-blue-dark);
        color: #fff;
    }

    .btn-secondary-custom {
        background: #f1f5f9;
        color: var(--text-muted);
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-secondary-custom:hover {
        background: #e2e8f0;
        color: var(--text-dark);
    }

    .btn-group-custom {
        display: flex;
        gap: 1rem;
    }

    .form-check-input:checked {
        background-color: var(--brand-blue);
        border-color: var(--brand-blue);
    }
</style>

<div class="page-header">
    <h1 class="page-title">Edit Schedule</h1>
</div>

<div class="card-custom">
    <div class="card-body">
        <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-section">
                <h5 class="form-section-title">Basic Information</h5>
                
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor</label>
                    <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                        <option value="">Choose a doctor...</option>
                        @foreach($dentists as $dentist)
                            <option value="{{ $dentist->id }}" {{ $schedule->doctor_id == $dentist->id ? 'selected' : '' }}>
                                {{ $dentist->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="working_date" class="form-label">Working Date</label>
                    <input type="date" name="working_date" id="working_date" class="form-control @error('working_date') is-invalid @enderror" value="{{ old('working_date', $schedule->working_date->toDateString()) }}" required>
                    @error('working_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Working Hours</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time', $schedule->start_time) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time', $schedule->end_time) }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Break Time (Optional)</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="break_start" class="form-label">Break Start</label>
                        <input type="time" name="break_start" id="break_start" class="form-control @error('break_start') is-invalid @enderror" value="{{ old('break_start', $schedule->break_start) }}">
                        @error('break_start')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="break_end" class="form-label">Break End</label>
                        <input type="time" name="break_end" id="break_end" class="form-control @error('break_end') is-invalid @enderror" value="{{ old('break_end', $schedule->break_end) }}">
                        @error('break_end')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Appointment Settings</h5>
                
                <div class="mb-3">
                    <label for="slot_duration" class="form-label">Slot Duration (minutes)</label>
                    <select name="slot_duration" id="slot_duration" class="form-select @error('slot_duration') is-invalid @enderror" required>
                        <option value="15" {{ $schedule->slot_duration == 15 ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ $schedule->slot_duration == 30 ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ $schedule->slot_duration == 45 ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ $schedule->slot_duration == 60 ? 'selected' : '' }}>60 minutes</option>
                    </select>
                    @error('slot_duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ $schedule->is_active ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Active</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Any additional notes...">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="btn-group-custom">
                <button type="submit" class="btn-primary-custom">Update Schedule</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn-secondary-custom">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
