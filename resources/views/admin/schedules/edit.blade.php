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

    .schedule-conflict-status {
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
        display: none;
        border: 1px solid transparent;
    }

    .schedule-conflict-status.is-loading {
        display: block;
        color: #475569;
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .schedule-conflict-status.is-valid {
        display: block;
        color: #166534;
        background: #f0fdf4;
        border-color: #bbf7d0;
    }

    .schedule-conflict-status.is-invalid {
        display: block;
        color: #991b1b;
        background: #fef2f2;
        border-color: #fecaca;
    }

    .schedule-conflict-list {
        margin: 0.5rem 0 0;
        padding-left: 1.25rem;
    }

    .slot-preview-panel {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }

    .slot-preview-title {
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.75rem;
    }

    .slot-preview-message {
        color: #64748b;
        margin-bottom: 0;
    }

    .slot-preview-message.is-invalid {
        color: #991b1b;
    }

    .slot-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .slot-summary-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
    }

    .slot-summary-label {
        display: block;
        font-size: 0.78rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .slot-summary-value {
        font-weight: 700;
        color: #334155;
    }

    .slot-preview-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .slot-preview-badge {
        border-radius: 999px;
        padding: 0.45rem 0.7rem;
        font-size: 0.85rem;
        font-weight: 600;
        border: 1px solid transparent;
    }

    .slot-preview-badge.is-available {
        color: #166534;
        background: #dcfce7;
        border-color: #bbf7d0;
    }

    .slot-preview-badge.is-blocked {
        color: #991b1b;
        background: #fee2e2;
        border-color: #fecaca;
    }

    .impact-warning {
        border: 1px solid #fed7aa;
        border-radius: 14px;
        background: #fff7ed;
        color: #9a3412;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .impact-warning-title {
        font-weight: 800;
        margin-bottom: 0.35rem;
    }

    .impact-appointment-list {
        margin: 0.75rem 0 0;
        padding-left: 1.1rem;
    }

    .impact-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1050;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.52);
        padding: 1rem;
    }

    .impact-modal-backdrop.is-visible {
        display: flex;
    }

    .impact-modal {
        width: min(560px, 100%);
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 22px 55px rgba(15, 23, 42, 0.28);
        overflow: hidden;
    }

    .impact-modal-header {
        padding: 1.25rem 1.5rem;
        background: #fff7ed;
        color: #9a3412;
        border-bottom: 1px solid #fed7aa;
    }

    .impact-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .impact-modal-body {
        padding: 1.5rem;
        color: #334155;
    }

    .impact-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem 1.5rem;
    }

    .btn-modal-cancel,
    .btn-modal-confirm {
        border: 0;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        font-weight: 700;
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-modal-confirm {
        background: #ea580c;
        color: #fff;
    }

    .time-picker {
        position: relative;
    }

    .time-picker-trigger {
        width: 100%;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 12px 16px !important;
        background: #f8fafc !important;
        color: #334155;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        text-align: left;
        transition: 0.2s;
    }

    .time-picker-trigger:hover,
    .time-picker-trigger[aria-expanded="true"] {
        background: #ffffff !important;
        border-color: #1f6fff !important;
        box-shadow: 0 0 0 4px rgba(31, 111, 255, 0.1) !important;
    }

    .time-picker-panel {
        display: none;
        position: absolute;
        z-index: 30;
        top: calc(100% + 0.5rem);
        left: 0;
        right: 0;
        border: 1px solid #dbeafe;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 18px 42px rgba(15, 23, 42, 0.14);
        padding: 0.85rem;
    }

    .time-picker.is-open .time-picker-panel {
        display: block;
    }

    .time-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(92px, 1fr));
        gap: 0.5rem;
        max-height: 280px;
        overflow-y: auto;
        padding-right: 0.15rem;
    }

    .time-card {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: #fff;
        color: #334155;
        padding: 0.55rem 0.45rem;
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.18s ease;
    }

    .time-card:hover,
    .time-card:focus {
        border-color: #1f6fff;
        color: #1f6fff;
        background: #eef5ff;
        outline: none;
    }

    .time-card.selected {
        border-color: #1f6fff;
        background: #1f6fff;
        color: #fff;
    }

    .time-card.disabled,
    .time-card:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        border-color: #e2e8f0;
        cursor: not-allowed;
    }

    .time-card.no-break {
        grid-column: 1 / -1;
        background: #f8fafc;
    }

    .time-card.no-break.selected {
        background: #1f6fff;
        color: #fff;
    }

    .time-picker-hint {
        min-height: 1.1rem;
        margin-top: 0.65rem;
        color: #b45309;
        font-size: 0.82rem;
        font-weight: 600;
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
        <form action="{{ route('admin.schedules.update', $schedule->id) }}" method="POST" id="schedule-form">
            @csrf
            @method('PUT')
            <input type="hidden" name="impact_confirmed" id="impact-confirmed" value="0">

            @if($impactSummary['count'] > 0)
                <div class="impact-warning">
                    <div class="impact-warning-title">
                        <i class="fas fa-triangle-exclamation"></i>
                        This schedule has {{ $impactSummary['count'] }} existing appointment{{ $impactSummary['count'] === 1 ? '' : 's' }}.
                    </div>
                    <div>
                        Major changes to the dentist, date, time, break, slot duration, or status may affect patient bookings.
                    </div>
                    <ul class="impact-appointment-list">
                        @foreach($impactSummary['appointments'] as $appointment)
                            <li>
                                {{ $appointment['patient_name'] }} -
                                {{ \Carbon\Carbon::parse($appointment['appointment_date'])->format('d M Y') }}
                                at {{ $appointment['appointment_time'] }}
                                ({{ $appointment['status'] }})
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

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
                    @php($selectedStartTime = old('start_time', \Carbon\Carbon::parse($schedule->start_time)->format('H:i')))
                    @include('admin.schedules.partials.time-card-picker', [
                        'name' => 'start_time',
                        'label' => 'Start Time',
                        'value' => $selectedStartTime,
                        'placeholder' => 'Select start time',
                        'allowNoBreak' => false,
                        'labelClass' => 'form-label-custom',
                        'timeOptions' => $timeOptions,
                    ])
                </div>
                <div class="col-md-6 mb-3">
                    @php($selectedEndTime = old('end_time', \Carbon\Carbon::parse($schedule->end_time)->format('H:i')))
                    @include('admin.schedules.partials.time-card-picker', [
                        'name' => 'end_time',
                        'label' => 'End Time',
                        'value' => $selectedEndTime,
                        'placeholder' => 'Select end time',
                        'allowNoBreak' => false,
                        'labelClass' => 'form-label-custom',
                        'timeOptions' => $timeOptions,
                    ])
                </div>
            </div>

            {{-- Section 3: Break Time --}}
            <div class="form-section-title">Break Time (Optional)</div>
            <div class="row mb-5">
                <div class="col-md-6 mb-3">
                    @php($selectedBreakStart = old('break_start', $schedule->break_start ? \Carbon\Carbon::parse($schedule->break_start)->format('H:i') : ''))
                    @include('admin.schedules.partials.time-card-picker', [
                        'name' => 'break_start',
                        'label' => 'Break Start',
                        'value' => $selectedBreakStart,
                        'placeholder' => 'No break time',
                        'allowNoBreak' => true,
                        'labelClass' => 'form-label-custom',
                        'timeOptions' => $timeOptions,
                    ])
                </div>
                <div class="col-md-6 mb-3">
                    @php($selectedBreakEnd = old('break_end', $schedule->break_end ? \Carbon\Carbon::parse($schedule->break_end)->format('H:i') : ''))
                    @include('admin.schedules.partials.time-card-picker', [
                        'name' => 'break_end',
                        'label' => 'Break End',
                        'value' => $selectedBreakEnd,
                        'placeholder' => 'No break time',
                        'allowNoBreak' => true,
                        'labelClass' => 'form-label-custom',
                        'timeOptions' => $timeOptions,
                    ])
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
                
                <div class="col-md-6 mb-4">
                    <label class="form-label-custom">Schedule Status</label>
                    <select name="status" class="form-control form-control-custom w-100 @error('status') is-invalid @enderror">
                        @foreach($statusOptions as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" {{ old('status', $schedule->status) === $statusValue ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12">
                    <label class="form-label-custom">Additional Notes</label>
                    <textarea name="notes" class="form-control form-control-custom w-100" rows="3">{{ old('notes', $schedule->notes) }}</textarea>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="slot-preview-panel" id="slot-preview-panel">
                <div class="slot-preview-title">Available Slot Preview</div>
                <div id="slot-preview-content">
                    <p class="slot-preview-message">Complete schedule details to preview available slots.</p>
                </div>
            </div>

            <div id="schedule-conflict-status" class="schedule-conflict-status" aria-live="polite"></div>

            <div class="mt-5 pt-4 border-top d-flex justify-content-end gap-3">
                <a href="{{ route('admin.schedules.index') }}" class="btn btn-light px-4 mr-2" style="border-radius: 12px; font-weight: 700;">Cancel</a>
                <button type="submit" class="btn-update" id="schedule-submit-button">Update Schedule Details</button>
            </div>
        </form>
    </div>
</div>

<div class="impact-modal-backdrop" id="edit-impact-modal" aria-hidden="true">
    <div class="impact-modal" role="dialog" aria-modal="true" aria-labelledby="edit-impact-title">
        <div class="impact-modal-header">
            <h2 class="impact-modal-title" id="edit-impact-title">Confirm Schedule Changes</h2>
        </div>
        <div class="impact-modal-body">
            <p id="edit-impact-message">
                This schedule already has patient appointments. Are you sure you want to save these changes?
            </p>
            <p class="mb-0">
                Existing appointment records will be preserved, but patients may be affected if the schedule details no longer match their booking.
            </p>
        </div>
        <div class="impact-modal-actions">
            <button type="button" class="btn-modal-cancel" id="cancel-impact-update">Cancel</button>
            <button type="button" class="btn-modal-confirm" id="confirm-impact-update">I Understand, Save Changes</button>
        </div>
    </div>
</div>

<?php
    $initialMajorValues = [
        'doctor_id' => (string) $schedule->doctor_id,
        'working_date' => $schedule->working_date->toDateString(),
        'start_time' => \Carbon\Carbon::parse($schedule->start_time)->format('H:i'),
        'end_time' => \Carbon\Carbon::parse($schedule->end_time)->format('H:i'),
        'break_start' => $schedule->break_start ? \Carbon\Carbon::parse($schedule->break_start)->format('H:i') : '',
        'break_end' => $schedule->break_end ? \Carbon\Carbon::parse($schedule->break_end)->format('H:i') : '',
        'slot_duration' => (string) $schedule->slot_duration,
        'status' => (string) $schedule->status,
    ];
?>

<script>
const scheduleConflictConfig = {
    endpoint: @json(route('admin.schedules.check-conflict')),
    previewEndpoint: @json(route('admin.schedules.preview-slots')),
    scheduleId: @json($schedule->id),
};

const scheduleForm = document.getElementById('schedule-form');
const scheduleStatus = document.getElementById('schedule-conflict-status');
const scheduleSubmitButton = document.getElementById('schedule-submit-button');
const slotPreviewContent = document.getElementById('slot-preview-content');
const scheduleFields = ['doctor_id', 'working_date', 'start_time', 'end_time', 'break_start', 'break_end', 'slot_duration'];
const timePickerFields = ['start_time', 'end_time', 'break_start', 'break_end'];
const impactConfirmedInput = document.getElementById('impact-confirmed');
const editImpactModal = document.getElementById('edit-impact-modal');
const editImpactMessage = document.getElementById('edit-impact-message');
const hasAffectedAppointments = @json($impactSummary['count'] > 0);
const initialMajorValues = @json($initialMajorValues);
let scheduleConflictTimer = null;
let scheduleConflictController = null;
let slotPreviewTimer = null;
let slotPreviewController = null;

scheduleForm.addEventListener('submit', confirmImpactBeforeSubmit);

document.getElementById('cancel-impact-update').addEventListener('click', closeEditImpactModal);
document.getElementById('confirm-impact-update').addEventListener('click', () => {
    impactConfirmedInput.value = '1';
    closeEditImpactModal();
    scheduleForm.requestSubmit();
});

editImpactModal.addEventListener('click', (event) => {
    if (event.target === editImpactModal) {
        closeEditImpactModal();
    }
});

scheduleFields.forEach((fieldName) => {
    const field = scheduleForm.querySelector(`[name="${fieldName}"]`);

    if (field) {
        field.addEventListener('input', queueScheduleFormChecks);
        field.addEventListener('change', queueScheduleFormChecks);
    }
});

setupTimeCardPickers();
queueScheduleFormChecks();

function setupTimeCardPickers() {
    document.querySelectorAll('[data-time-picker]').forEach((picker) => {
        const trigger = picker.querySelector('.time-picker-trigger');

        trigger.addEventListener('click', (event) => {
            event.stopPropagation();
            closeTimePickers(picker);
            picker.classList.toggle('is-open');
            trigger.setAttribute('aria-expanded', picker.classList.contains('is-open') ? 'true' : 'false');
        });

        picker.querySelectorAll('.time-card').forEach((card) => {
            card.addEventListener('click', () => {
                if (card.disabled) {
                    return;
                }

                setTimePickerValue(picker.dataset.field, card.dataset.value, card.dataset.display);
                closeTimePickers();
                applyTimePickerConstraints(picker.dataset.field);
            });
        });
    });

    document.addEventListener('click', () => closeTimePickers());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeTimePickers();
        }
    });

    timePickerFields.forEach((fieldName) => {
        const picker = getTimePicker(fieldName);
        const input = getTimeInput(fieldName);
        const selectedCard = getTimeCard(fieldName, input.value);

        if (selectedCard) {
            setTimePickerValue(fieldName, input.value, selectedCard.dataset.display, false);
        }

        updateSelectedTimeCard(picker, input.value);
    });

    applyTimePickerConstraints();
}

function closeTimePickers(exceptPicker = null) {
    document.querySelectorAll('[data-time-picker].is-open').forEach((picker) => {
        if (picker === exceptPicker) {
            return;
        }

        picker.classList.remove('is-open');
        picker.querySelector('.time-picker-trigger').setAttribute('aria-expanded', 'false');
    });
}

function setTimePickerValue(fieldName, value, display, dispatchEvents = true) {
    const picker = getTimePicker(fieldName);
    const input = getTimeInput(fieldName);
    const triggerText = picker.querySelector('.time-picker-trigger-text');

    input.value = value;
    triggerText.textContent = display || picker.querySelector('.time-picker-trigger').dataset.placeholder;
    updateSelectedTimeCard(picker, value);

    if (dispatchEvents) {
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function updateSelectedTimeCard(picker, value) {
    picker.querySelectorAll('.time-card').forEach((card) => {
        card.classList.toggle('selected', card.dataset.value === value);
    });
}

function applyTimePickerConstraints(changedField = null) {
    const startMinutes = minutesFromValue(getTimeInput('start_time').value);
    const endMinutes = minutesFromValue(getTimeInput('end_time').value);
    const breakStartMinutes = minutesFromValue(getTimeInput('break_start').value);
    const breakEndMinutes = minutesFromValue(getTimeInput('break_end').value);

    setHint('end_time', startMinutes !== null ? 'End time must be after start time.' : '');
    setHint('break_start', startMinutes !== null && endMinutes !== null ? 'Break time must be within working hours.' : '');
    setHint('break_end', breakStartMinutes !== null ? 'Break end must be after break start.' : '');

    updateDisabledCards('end_time', (minutes) => startMinutes !== null && minutes <= startMinutes);
    updateDisabledCards('break_start', (minutes) => {
        if (startMinutes === null || endMinutes === null) {
            return true;
        }

        return minutes < startMinutes || minutes >= endMinutes;
    });
    updateDisabledCards('break_end', (minutes) => {
        if (startMinutes === null || endMinutes === null || breakStartMinutes === null) {
            return true;
        }

        return minutes <= breakStartMinutes || minutes > endMinutes;
    });

    if (startMinutes !== null && endMinutes !== null && endMinutes <= startMinutes) {
        clearTimePickerValue('end_time');
    }

    if (startMinutes === null || endMinutes === null) {
        clearTimePickerValue('break_start');
        clearTimePickerValue('break_end');
    } else {
        if (breakStartMinutes !== null && (breakStartMinutes < startMinutes || breakStartMinutes >= endMinutes)) {
            clearTimePickerValue('break_start');
        }

        if (breakEndMinutes !== null && (breakEndMinutes <= (minutesFromValue(getTimeInput('break_start').value) ?? breakEndMinutes) || breakEndMinutes > endMinutes)) {
            clearTimePickerValue('break_end');
        }
    }

    if (changedField === 'break_start' && getTimeInput('break_start').value === '') {
        clearTimePickerValue('break_end');
    }
}

function updateDisabledCards(fieldName, shouldDisable) {
    getTimePicker(fieldName).querySelectorAll('.time-card').forEach((card) => {
        if (card.dataset.value === '') {
            card.disabled = false;
            card.classList.remove('disabled');
            return;
        }

        const disabled = shouldDisable(Number(card.dataset.minutes));
        card.disabled = disabled;
        card.classList.toggle('disabled', disabled);
    });
}

function clearTimePickerValue(fieldName) {
    const picker = getTimePicker(fieldName);
    const input = getTimeInput(fieldName);

    if (input.value === '') {
        return;
    }

    const noBreakCard = getTimeCard(fieldName, '');
    setTimePickerValue(
        fieldName,
        '',
        noBreakCard ? noBreakCard.dataset.display : picker.querySelector('.time-picker-trigger').dataset.placeholder
    );
}

function setHint(fieldName, message) {
    const hint = document.getElementById(`${fieldName}_hint`);

    if (hint) {
        hint.textContent = message;
    }
}

function getTimePicker(fieldName) {
    return document.querySelector(`[data-time-picker][data-field="${fieldName}"]`);
}

function getTimeInput(fieldName) {
    return document.getElementById(fieldName);
}

function getTimeCard(fieldName, value) {
    return getTimePicker(fieldName).querySelector(`.time-card[data-value="${value}"]`);
}

function minutesFromValue(value) {
    if (!value) {
        return null;
    }

    const [hours, minutes] = value.split(':').map(Number);

    return (hours * 60) + minutes;
}

function queueScheduleFormChecks() {
    queueScheduleConflictCheck();
    queueScheduleSlotPreview();
}

function queueScheduleConflictCheck() {
    window.clearTimeout(scheduleConflictTimer);
    scheduleConflictTimer = window.setTimeout(checkScheduleConflict, 450);
}

function queueScheduleSlotPreview() {
    window.clearTimeout(slotPreviewTimer);
    slotPreviewTimer = window.setTimeout(previewScheduleSlots, 450);
}

function checkScheduleConflict() {
    const formData = new FormData(scheduleForm);
    const requiredFields = ['doctor_id', 'working_date', 'start_time', 'end_time', 'slot_duration'];

    if (requiredFields.some((fieldName) => !formData.get(fieldName))) {
        hideScheduleConflictStatus();
        return;
    }

    if (scheduleConflictController) {
        scheduleConflictController.abort();
    }

    scheduleConflictController = new AbortController();
    showScheduleConflictStatus('is-loading', 'Checking schedule availability...');

    const params = new URLSearchParams();
    scheduleFields.forEach((fieldName) => {
        const value = formData.get(fieldName);

        if (value) {
            params.append(fieldName, value);
        }
    });
    params.append('schedule_id', scheduleConflictConfig.scheduleId);

    fetch(`${scheduleConflictConfig.endpoint}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' },
        signal: scheduleConflictController.signal,
    })
        .then((response) => response.json())
        .then((data) => renderScheduleConflictStatus(data))
        .catch((error) => {
            if (error.name !== 'AbortError') {
                showScheduleConflictStatus('is-invalid', 'Unable to check schedule availability right now.');
            }
        });
}

function renderScheduleConflictStatus(data) {
    if (!data.hasConflict) {
        scheduleSubmitButton.disabled = false;
        showScheduleConflictStatus('is-valid', data.message || 'No schedule conflict detected.');
        return;
    }

    scheduleSubmitButton.disabled = true;

    let details = '';
    if (Array.isArray(data.conflicts) && data.conflicts.length > 0) {
        details = '<ul class="schedule-conflict-list">';
        data.conflicts.forEach((conflict) => {
            details += `<li>Existing schedule: ${conflict.display_start_time} - ${conflict.display_end_time}</li>`;
        });
        details += '</ul>';
    }

    showScheduleConflictStatus('is-invalid', `Schedule conflict detected: ${data.message}${details}`);
}

function showScheduleConflictStatus(stateClass, message) {
    scheduleStatus.className = `schedule-conflict-status ${stateClass}`;
    scheduleStatus.innerHTML = message;
}

function hideScheduleConflictStatus() {
    scheduleSubmitButton.disabled = false;
    scheduleStatus.className = 'schedule-conflict-status';
    scheduleStatus.innerHTML = '';
}

function previewScheduleSlots() {
    const formData = new FormData(scheduleForm);
    const requiredFields = ['doctor_id', 'working_date', 'start_time', 'end_time', 'slot_duration'];

    if (requiredFields.some((fieldName) => !formData.get(fieldName))) {
        renderSlotPreviewIncomplete();
        return;
    }

    if (slotPreviewController) {
        slotPreviewController.abort();
    }

    slotPreviewController = new AbortController();
    slotPreviewContent.innerHTML = '<p class="slot-preview-message">Generating slot preview...</p>';

    const params = new URLSearchParams();
    scheduleFields.forEach((fieldName) => {
        const value = formData.get(fieldName);

        if (value) {
            params.append(fieldName, value);
        }
    });
    params.append('schedule_id', scheduleConflictConfig.scheduleId);

    fetch(`${scheduleConflictConfig.previewEndpoint}?${params.toString()}`, {
        headers: { 'Accept': 'application/json' },
        signal: slotPreviewController.signal,
    })
        .then((response) => response.json())
        .then((data) => renderSlotPreview(data))
        .catch((error) => {
            if (error.name !== 'AbortError') {
                slotPreviewContent.innerHTML = '<p class="slot-preview-message is-invalid">Unable to generate slot preview right now.</p>';
            }
        });
}

function renderSlotPreview(data) {
    if (!data.success) {
        slotPreviewContent.innerHTML = `<p class="slot-preview-message is-invalid">${data.message}</p>`;
        return;
    }

    const summary = data.summary;
    const breakTime = summary.display_break_start && summary.display_break_end
        ? `${summary.display_break_start} - ${summary.display_break_end}`
        : 'No break';
    const warning = data.warning
        ? `<p class="slot-preview-message is-invalid mb-3">${data.warning}</p>`
        : '';

    let slots = '<div class="slot-preview-list">';
    data.slots.forEach((slot) => {
        const stateClass = slot.status === 'available' ? 'is-available' : 'is-blocked';
        const reason = slot.reason ? ` (${slot.reason})` : '';
        slots += `<span class="slot-preview-badge ${stateClass}">${slot.display_start_time} - ${slot.display_end_time}${reason}</span>`;
    });
    slots += '</div>';

    slotPreviewContent.innerHTML = `
        ${warning}
        <div class="slot-summary-grid">
            <div class="slot-summary-item"><span class="slot-summary-label">Working Date</span><span class="slot-summary-value">${summary.display_working_date}</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Working Hours</span><span class="slot-summary-value">${summary.display_working_start} - ${summary.display_working_end}</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Break Time</span><span class="slot-summary-value">${breakTime}</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Slot Duration</span><span class="slot-summary-value">${summary.slot_duration} minutes</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Total Slots</span><span class="slot-summary-value">${summary.total_slots}</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Available Slots</span><span class="slot-summary-value">${summary.available_slots}</span></div>
            <div class="slot-summary-item"><span class="slot-summary-label">Blocked Slots</span><span class="slot-summary-value">${summary.blocked_slots}</span></div>
        </div>
        ${slots}
    `;
}

function renderSlotPreviewIncomplete() {
    slotPreviewContent.innerHTML = '<p class="slot-preview-message">Complete schedule details to preview available slots.</p>';
}

function confirmImpactBeforeSubmit(event) {
    if (!hasAffectedAppointments || impactConfirmedInput.value === '1' || !hasMajorScheduleChanges()) {
        return;
    }

    event.preventDefault();

    const statusField = scheduleForm.querySelector('[name="status"]');
    const selectedStatus = statusField ? statusField.value : initialMajorValues.status;

    if (['inactive', 'unavailable', 'fully_booked'].includes(selectedStatus) && selectedStatus !== initialMajorValues.status) {
        editImpactMessage.textContent = 'This schedule already has patient appointments. Changing the status may prevent new bookings and may affect existing patient expectations. Are you sure you want to save these changes?';
    } else {
        editImpactMessage.textContent = 'This schedule already has patient appointments. Are you sure you want to save these changes?';
    }

    editImpactModal.classList.add('is-visible');
    editImpactModal.setAttribute('aria-hidden', 'false');
}

function hasMajorScheduleChanges() {
    return Object.keys(initialMajorValues).some((fieldName) => {
        const field = scheduleForm.querySelector(`[name="${fieldName}"]`);
        const currentValue = field ? field.value : '';

        return currentValue !== initialMajorValues[fieldName];
    });
}

function closeEditImpactModal() {
    editImpactModal.classList.remove('is-visible');
    editImpactModal.setAttribute('aria-hidden', 'true');
}
</script>
@endsection
