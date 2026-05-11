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

    /* --- The Blue Banner Header Styles --- */
    .banner-header {
        background-color: var(--brand-blue);
        border-radius: 12px;
        padding: 24px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(31, 111, 255, 0.15);
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
        color: var(--brand-blue);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-banner-action:hover {
        background-color: #f8fafc;
        color: var(--brand-blue-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* --- Form & Card Styles --- */
    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
        padding: 2.5rem; 
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
        border: 1px solid var(--card-border);
        border-radius: 12px;
        background: #f8fafc;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }

    .slot-preview-title {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 0.75rem;
    }

    .slot-preview-message {
        color: var(--text-muted);
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
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .slot-summary-value {
        font-weight: 700;
        color: var(--text-dark);
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

    .time-picker {
        position: relative;
    }

    .time-picker-trigger {
        width: 100%;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        background: #f8fafc;
        color: var(--text-dark);
        padding: 0.75rem 1rem;
        font-size: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        text-align: left;
        transition: all 0.2s ease;
    }

    .time-picker-trigger:hover,
    .time-picker-trigger[aria-expanded="true"] {
        border-color: var(--brand-blue);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.1);
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
        border-color: var(--brand-blue);
        color: var(--brand-blue);
        background: var(--brand-blue-light);
        outline: none;
    }

    .time-card.selected {
        border-color: var(--brand-blue);
        background: var(--brand-blue);
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
        background: var(--brand-blue);
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

<div class="banner-header">
    <div class="banner-left">
        <div class="banner-title-wrapper">
            <i class="fas fa-calendar-plus banner-icon"></i> 
            <h1 class="banner-title">Create Schedule</h1>
        </div>
        <p class="banner-subtitle">Set up working hours and generate time slots for a doctor.</p>
    </div>
    
    <a href="{{ route('admin.schedules.index') }}" class="btn-banner-action">
        <i class="fas fa-arrow-left"></i> Back to Schedules
    </a>
</div>

<div class="card-custom">
    <div class="card-body">
        <form action="{{ route('admin.schedules.store') }}" method="POST" id="schedule-form">
            @csrf

            <div class="form-section">
                <h5 class="form-section-title">Basic Information</h5>
                
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Select Doctor</label>
                    <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                        <option value="">Choose a doctor...</option>
                        @foreach($dentists as $dentist)
                            <option value="{{ $dentist->id }}" {{ old('doctor_id') == $dentist->id ? 'selected' : '' }}>
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
                    <input type="date" name="working_date" id="working_date" class="form-control @error('working_date') is-invalid @enderror" value="{{ old('working_date') }}" required min="{{ date('Y-m-d') }}">
                    @error('working_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Working Hours</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        @include('admin.schedules.partials.time-card-picker', [
                            'name' => 'start_time',
                            'label' => 'Start Time',
                            'value' => old('start_time'),
                            'placeholder' => 'Select start time',
                            'allowNoBreak' => false,
                            'labelClass' => 'form-label',
                            'timeOptions' => $timeOptions,
                        ])
                    </div>

                    <div class="col-md-6 mb-3">
                        @include('admin.schedules.partials.time-card-picker', [
                            'name' => 'end_time',
                            'label' => 'End Time',
                            'value' => old('end_time'),
                            'placeholder' => 'Select end time',
                            'allowNoBreak' => false,
                            'labelClass' => 'form-label',
                            'timeOptions' => $timeOptions,
                        ])
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Break Time (Optional)</h5>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        @include('admin.schedules.partials.time-card-picker', [
                            'name' => 'break_start',
                            'label' => 'Break Start',
                            'value' => old('break_start'),
                            'placeholder' => 'No break time',
                            'allowNoBreak' => true,
                            'labelClass' => 'form-label',
                            'timeOptions' => $timeOptions,
                        ])
                    </div>

                    <div class="col-md-6 mb-3">
                        @include('admin.schedules.partials.time-card-picker', [
                            'name' => 'break_end',
                            'label' => 'Break End',
                            'value' => old('break_end'),
                            'placeholder' => 'No break time',
                            'allowNoBreak' => true,
                            'labelClass' => 'form-label',
                            'timeOptions' => $timeOptions,
                        ])
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h5 class="form-section-title">Appointment Settings</h5>
                
                <div class="mb-3">
                    <label for="slot_duration" class="form-label">Slot Duration (minutes)</label>
                    <select name="slot_duration" id="slot_duration" class="form-select @error('slot_duration') is-invalid @enderror" required>
                        <option value="15" {{ old('slot_duration') == 15 ? 'selected' : '' }}>15 minutes</option>
                        <option value="30" {{ old('slot_duration') == 30 || !old('slot_duration') ? 'selected' : '' }}>30 minutes</option>
                        <option value="45" {{ old('slot_duration') == 45 ? 'selected' : '' }}>45 minutes</option>
                        <option value="60" {{ old('slot_duration') == 60 ? 'selected' : '' }}>60 minutes</option>
                    </select>
                    @error('slot_duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Schedule Status</label>
                    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                        @foreach($statusOptions as $statusValue => $statusLabel)
                            <option value="{{ $statusValue }}" {{ old('status', \App\Models\DoctorSchedule::STATUS_ACTIVE) === $statusValue ? 'selected' : '' }}>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (Optional)</label>
                    <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="slot-preview-panel" id="slot-preview-panel">
                <div class="slot-preview-title">Available Slot Preview</div>
                <div id="slot-preview-content">
                    <p class="slot-preview-message">Complete schedule details to preview available slots.</p>
                </div>
            </div>

            <div id="schedule-conflict-status" class="schedule-conflict-status" aria-live="polite"></div>

            <div class="btn-group-custom">
                <button type="submit" class="btn-primary-custom" id="schedule-submit-button">Create Schedule</button>
                <a href="{{ route('admin.schedules.index') }}" class="btn-secondary-custom">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
const scheduleConflictConfig = {
    endpoint: @json(route('admin.schedules.check-conflict')),
    previewEndpoint: @json(route('admin.schedules.preview-slots')),
    scheduleId: null,
};

const scheduleForm = document.getElementById('schedule-form');
const scheduleStatus = document.getElementById('schedule-conflict-status');
const scheduleSubmitButton = document.getElementById('schedule-submit-button');
const slotPreviewContent = document.getElementById('slot-preview-content');
const scheduleFields = ['doctor_id', 'working_date', 'start_time', 'end_time', 'break_start', 'break_end', 'slot_duration'];
const timePickerFields = ['start_time', 'end_time', 'break_start', 'break_end'];
let scheduleConflictTimer = null;
let scheduleConflictController = null;
let slotPreviewTimer = null;
let slotPreviewController = null;

scheduleFields.forEach((fieldName) => {
    const field = scheduleForm.querySelector(`[name="${fieldName}"]`);

    if (field) {
        field.addEventListener('input', queueScheduleFormChecks);
        field.addEventListener('change', queueScheduleFormChecks);
    }
});

setupTimeCardPickers();
queueScheduleSlotPreview();

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
</script>
@endsection