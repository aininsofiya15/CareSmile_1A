@extends('layouts.app')

@section('content')

<style>
    .banner-header {
        background-color: #2563eb;
        border-radius: 12px;
        padding: 40px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
    }

    .banner-left {
        display: flex;
        flex-direction: column;
    }

    .banner-title-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        color: white;
    }

    .banner-icon {
        font-size: 26px;
    }

    .banner-title {
        font-size: 26px;
        font-weight: 700;
        margin: 0;
        color: white;
        letter-spacing: -0.5px;
    }

    .banner-subtitle {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.85);
        margin: 6px 0 0 0;
        font-weight: 400;
    }

    .btn-banner-action {
        background-color: white;
        color: #2563eb;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-banner-action:hover {
        background-color: #f8fafc;
        color: #1d4ed8;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Reminder Notice */
    .reminder-notice {
        background-color: #fffbeb;
        border: 1px solid #fcd34d;
        border-left: 5px solid #f59e0b;
        border-radius: 10px;
        padding: 18px 22px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }

    .reminder-icon {
        font-size: 22px;
        color: #d97706;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .reminder-title {
        font-weight: 700;
        color: #92400e;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .reminder-text {
        color: #78350f;
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    /* Details Card */
    .details-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        overflow: hidden;
        margin-bottom: 24px;
    }

    .details-card-header {
        padding: 20px 28px;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .details-card-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
    }

    .details-card-header i {
        color: #2563eb;
        font-size: 18px;
    }

    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 0;
    }

    .detail-item {
        padding: 22px 28px;
        border-bottom: 1px solid #f9fafb;
        border-right: 1px solid #f9fafb;
    }

    .detail-item:last-child {
        border-right: none;
    }

    .detail-label {
        font-size: 12px;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .detail-label i {
        font-size: 13px;
        color: #9ca3af;
    }

    .detail-value {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    .detail-value.muted {
        color: #9ca3af;
        font-weight: 400;
        font-style: italic;
    }

    /* Status Badges */
    .status-badge {
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        display: inline-block;
    }

    .status-scheduled  { background-color: #dbeafe; color: #1d4ed8; }
    .status-completed  { background-color: #dcfce7; color: #16a34a; }
    .status-cancelled  { background-color: #fee2e2; color: #dc2626; }
    .status-no_show    { background-color: #ffedd5; color: #c2410c; }

    /* Notes section */
    .notes-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        padding: 22px 28px;
        margin-bottom: 24px;
    }

    .notes-section h3 {
        font-size: 15px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .notes-section p {
        font-size: 14px;
        color: #6b7280;
        margin: 0;
        line-height: 1.7;
    }

    /* Schedule Info section (clinic hours) */
    .schedule-section {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 12px;
        padding: 22px 28px;
        margin-bottom: 24px;
    }

    .schedule-section h3 {
        font-size: 15px;
        font-weight: 700;
        color: #075985;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .schedule-info-row {
        display: flex;
        gap: 32px;
        flex-wrap: wrap;
    }

    .schedule-info-item label {
        font-size: 12px;
        color: #0369a1;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
    }

    .schedule-info-item span {
        font-size: 14px;
        font-weight: 600;
        color: #0c4a6e;
    }

    /* Action buttons */
    .action-row {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }

    .btn-action-outline {
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 2px solid;
        transition: all 0.2s ease;
        cursor: pointer;
        background: transparent;
    }

    .btn-back {
        border-color: #d1d5db;
        color: #374151;
    }

    .btn-back:hover {
        background-color: #f9fafb;
        color: #111827;
        border-color: #9ca3af;
    }

    .btn-reschedule {
        border-color: #c7d2fe;
        color: #4f46e5;
        background-color: #eef2ff;
    }

    .btn-reschedule:hover {
        background-color: #e0e7ff;
        color: #4338ca;
    }
</style>

@php
    $statusLabels = [
        'scheduled' => 'Scheduled',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
        'no_show'    => 'No Show',
    ];
    $statusLabel = $statusLabels[$appointment->status] ?? ucfirst(str_replace('_', ' ', $appointment->status));

    $startFormatted = \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A');
    $endFormatted   = $appointment->end_time
        ? \Carbon\Carbon::parse($appointment->end_time)->format('h:i A')
        : null;

    $timeDisplay = $endFormatted ? "{$startFormatted} – {$endFormatted}" : $startFormatted;
    $dentistName = $appointment->doctor?->name ?? 'N/A';
@endphp

{{-- Banner --}}
<div class="banner-header">
    <div class="banner-left">
        <div class="banner-title-wrapper">
            <i class="fas fa-calendar-check banner-icon"></i>
            <h1 class="banner-title">Appointment Details</h1>
        </div>
        <p class="banner-subtitle">View your appointment schedule information</p>
    </div>
    <a href="{{ route('patient.appointments') }}" class="btn-banner-action">
        <i class="fas fa-arrow-left" style="margin-right: 8px;"></i> Back to Appointments
    </a>
</div>

{{-- Reminder Notice --}}
<div class="reminder-notice">
    <i class="fas fa-clock reminder-icon"></i>
    <div>
        <p class="reminder-title">Appointment Reminder</p>
        <p class="reminder-text">
            Please come <strong>15 minutes before</strong> your appointment time.
            Patients who do not show up within <strong>20 minutes</strong> may need to reschedule their appointment.
        </p>
    </div>
</div>

{{-- Main Details Card --}}
<div class="details-card">
    <div class="details-card-header">
        <i class="fas fa-clipboard-list"></i>
        <h2>Appointment Information</h2>
    </div>

    <div class="details-grid">

        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-calendar-day"></i> Date
            </div>
            <div class="detail-value">
                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="far fa-clock"></i> Appointment Time
            </div>
            <div class="detail-value">
                {{ $timeDisplay }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-tooth"></i> Service
            </div>
            <div class="detail-value">
                {{ $appointment->service ?? 'N/A' }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-user-md"></i> Dentist
            </div>
            <div class="detail-value">
                {{ $dentistName }}
            </div>
        </div>

        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-info-circle"></i> Status
            </div>
            <div class="detail-value">
                <span class="status-badge status-{{ $appointment->status }}">
                    {{ $statusLabel }}
                </span>
            </div>
        </div>

        @if($appointment->end_time)
        <div class="detail-item">
            <div class="detail-label">
                <i class="fas fa-hourglass-end"></i> Duration
            </div>
            <div class="detail-value">
                @php
                    $start = \Carbon\Carbon::parse($appointment->appointment_time);
                    $end   = \Carbon\Carbon::parse($appointment->end_time);
                    $mins  = $start->diffInMinutes($end);
                    $hours = intdiv($mins, 60);
                    $rem   = $mins % 60;
                    $durationLabel = $hours > 0
                        ? ($hours . 'h' . ($rem > 0 ? " {$rem}m" : ''))
                        : "{$mins}m";
                @endphp
                {{ $durationLabel }}
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Clinic Schedule Info (if found) --}}
@if($schedule)
<div class="schedule-section">
    <h3>
        <i class="fas fa-hospital-alt"></i> Clinic Schedule Information
    </h3>
    <div class="schedule-info-row">
        <div class="schedule-info-item">
            <label>Working Hours</label>
            <span>
                {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }}
                –
                {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
            </span>
        </div>
        @if($schedule->break_start && $schedule->break_end)
        <div class="schedule-info-item">
            <label>Break Time</label>
            <span>
                {{ \Carbon\Carbon::parse($schedule->break_start)->format('h:i A') }}
                –
                {{ \Carbon\Carbon::parse($schedule->break_end)->format('h:i A') }}
            </span>
        </div>
        @endif
        <div class="schedule-info-item">
            <label>Slot Duration</label>
            <span>{{ $schedule->slot_duration }} minutes</span>
        </div>
    </div>
</div>
@endif

{{-- Notes (if any) --}}
@if(!empty($appointment->notes))
<div class="notes-section">
    <h3><i class="fas fa-sticky-note"></i> Notes</h3>
    <p>{{ $appointment->notes }}</p>
</div>
@endif

{{-- Action Buttons --}}
<div class="action-row">
    <a href="{{ route('patient.appointments') }}" class="btn-action-outline btn-back">
        <i class="fas fa-arrow-left"></i> Back to My Appointments
    </a>

    @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->status !== 'no_show')
        <a href="{{ route('patient.appointments.reschedule', $appointment->id) }}" class="btn-action-outline btn-reschedule">
            <i class="fas fa-calendar-alt"></i> Reschedule
        </a>
    @endif
</div>

@endsection
