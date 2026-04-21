@extends('layouts.app')

@section('content')
<style>
    /* 1. Workspace Wrapper */
    .workspace-wrapper {
        background: #ffffff;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-top: 20px;
    }

    /* 2. Form Styling */
    .form-section-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #94a3b8;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .form-section-title::after {
        content: "";
        height: 1px;
        background: #f1f5f9;
        flex-grow: 1;
    }

    .form-label-custom {
        font-weight: 700;
        color: #334155;
        font-size: 0.85rem;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-custom {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
        background: #f8fafc !important;
        font-size: 0.95rem;
        transition: 0.2s;
    }

    .form-control-custom:focus {
        background: #ffffff !important;
        border-color: #1f6fff !important;
        box-shadow: 0 0 0 4px rgba(31, 111, 255, 0.1) !important;
    }

    /* 3. Checkbox Styling */
    .custom-checkbox-wrapper {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    /* 4. Action Buttons */
    .btn-update {
        background: #1f6fff;
        color: white;
        font-weight: 700;
        border-radius: 12px;
        padding: 12px 30px;
        border: none;
        transition: 0.2s;
    }

    .btn-update:hover {
        background: #1456cc;
        box-shadow: 0 8px 20px rgba(31, 111, 255, 0.2);
    }
</style>

<div class="container py-4">
    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="font-weight-bold text-dark mb-1">Edit Schedule</h2>
            <p class="text-muted small mb-0">Modify the existing working hours or break times.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-light px-4 shadow-sm" style="border-radius: 12px; font-weight: 700; border: 1px solid #e2e8f0;">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    {{-- Main Workspace --}}
    <div class="workspace-wrapper">
        <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Section 1: Basic Info --}}
            <div class="form-section-title">Basic Information</div>
            <div class="row mb-5">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Select Doctor</label>
                    <select name="doctor_id" class="form-control form-control-custom w-100 @error('doctor_id') is-invalid @enderror">
                        @foreach($dentists as $dentist)
                            <option value="{{ $dentist->id }}" {{ $schedule->doctor_id == $dentist->id ? 'selected' : '' }}>
                                {{ $dentist->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Working Date</label>
                    <input type="date" name="working_date" class="form-control form-control-custom w-100" value="{{ old('working_date', $schedule->working_date->toDateString()) }}">
                </div>
            </div>

            {{-- Section 2: Working Hours --}}
            <div class="form-section-title">Working Hours</div>
            <div class="row mb-5">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Start Time</label>
                    <input type="time" name="start_time" class="form-control form-control-custom w-100" value="{{ old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')) }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">End Time</label>
                    <input type="time" name="end_time" class="form-control form-control-custom w-100" value="{{ old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')) }}">
                </div>
            </div>

            {{-- Section 3: Break Time --}}
            <div class="form-section-title">Break Time (Optional)</div>
            <div class="row mb-5">
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Break Start</label>
                    <input type="time" name="break_start" class="form-control form-control-custom w-100" value="{{ old('break_start', $schedule->break_start ? \Carbon\Carbon::parse($schedule->break_start)->format('H:i') : '') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label-custom">Break End</label>
                    <input type="time" name="break_end" class="form-control form-control-custom w-100" value="{{ old('break_end', $schedule->break_end ? \Carbon\Carbon::parse($schedule->break_end)->format('H:i') : '') }}">
                </div>
            </div>

            {{-- Section 4: Settings --}}
            <div class="form-section-title">Appointment Settings</div>
            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <label class="form-label-custom">Slot Duration (minutes)</label>
                    <select name="slot_duration" class="form-control form-control-custom w-100">
                        <option value="15" {{ $schedule->slot_duration == 15 ? 'selected' : '' }}>15 Minutes</option>
                        <option value="30" {{ $schedule->slot_duration == 30 ? 'selected' : '' }}>30 Minutes</option>
                        <option value="45" {{ $schedule->slot_duration == 45 ? 'selected' : '' }}>45 Minutes</option>
                        <option value="60" {{ $schedule->slot_duration == 60 ? 'selected' : '' }}>60 Minutes</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-4 d-flex align-items-end">
                    <div class="custom-checkbox-wrapper w-100">
                        <input type="checkbox" name="is_active" id="is_active" class="mr-3" value="1" {{ $schedule->is_active ? 'checked' : '' }} style="width: 20px; height: 20px;">
                        <label for="is_active" class="mb-0 font-weight-bold text-dark">Currently Active</label>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label-custom">Additional Notes</label>
                    <textarea name="notes" class="form-control form-control-custom w-100" rows="3">{{ old('notes', $schedule->notes) }}</textarea>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-light px-4 mr-2" style="border-radius: 12px; font-weight: 700;">Cancel</a>
                <button type="submit" class="btn-update">Update Schedule Details</button>
            </div>
        </form>
    </div>
</div>
@endsection