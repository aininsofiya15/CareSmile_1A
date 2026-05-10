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

    .page-header { margin-bottom: 20px; }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #1f2937;
    }

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .form-label { font-weight: 600; color: var(--text-dark); }

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
        cursor: pointer;
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

    .btn-secondary-custom:hover { opacity: 0.9; }
</style>

<div class="page-header">
    <h1 class="page-title">Book Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body p-4">

        {{-- ERROR MESSAGES --}}
        {{-- @if($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif --}}

        <form action="{{ route('patient.appointments.store') }}" method="POST">
            @csrf

            {{-- STEP 1: SERVICE --}}
            <div class="mb-3">
                <label class="form-label">Service</label>
                <select name="service" class="form-select" required>
                    <option value="">Select Service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->name }}" {{ old('service') == $service->name ? 'selected' : '' }}>
                            {{ $service->name }} — RM{{ number_format($service->price, 2) }}
                            ({{ $service->duration_minutes }} mins)
                        </option>
                    @endforeach
                </select>
                @if($services->isEmpty())
                    <small class="text-danger">No services available at the moment.</small>
                @endif
            </div>

            {{-- STEP 2: SELECT DATE --}}
            <div class="mb-3">
                <label class="form-label">Select Date</label>
                <select id="date-select" class="form-select">
                    <option value="">-- Select Available Date --</option>
                    @foreach($availableSchedules->groupBy('working_date') as $date => $schedules)
                        <option value="{{ $date }}">
                            {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }}
                        </option>
                    @endforeach
                </select>
                @if($availableSchedules->isEmpty())
                    <small class="text-danger">No available dates at the moment. Please check back later.</small>
                @endif
            </div>

            {{-- STEP 3: SELECT TIME SLOT (populated by JS) --}}
            <div class="mb-3">
                <label class="form-label">Available Time Slot</label>
                <select name="slot_id" id="slot-select" class="form-select" required>
                    <option value="">-- Select Date First --</option>
                </select>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn-primary-custom">
                    Book Appointment
                </button>
                <a href="{{ route('patient.appointments') }}" class="btn-secondary-custom">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

{{-- JAVASCRIPT: when date changes, load time slots --}}
<script>
document.getElementById('date-select').addEventListener('change', function () {

    let date = this.value;
    let slotSelect = document.getElementById('slot-select');

    // Reset slot dropdown
    slotSelect.innerHTML = '<option value="">Loading...</option>';

    if (!date) {
        slotSelect.innerHTML = '<option value="">-- Select Date First --</option>';
        return;
    }

    // Call backend to get slots for selected date
    fetch(`/get-slots/${date}`)
        .then(response => response.json())
        .then(data => {

            let options = '<option value="">-- Select Time Slot --</option>';

            if (data.length === 0) {
                options = '<option value="">No slots available for this date</option>';
            }

            data.forEach(schedule => {
                schedule.slots.forEach(slot => {

                    // Format time: "09:00:00" → "09:00 AM"
                    let startTime = formatTime(slot.start_time);
                    let endTime   = formatTime(slot.end_time);

                    options += `
                        <option value="${slot.id}">
                            Dr. ${schedule.doctor.name} — ${startTime} to ${endTime}
                        </option>
                    `;
                });
            });

            slotSelect.innerHTML = options;
        })
        .catch(() => {
            slotSelect.innerHTML = '<option value="">Error loading slots. Try again.</option>';
        });
});

// Helper: format "09:00:00" to "09:00 AM"
function formatTime(timeStr) {
    let [hours, minutes] = timeStr.split(':');
    let h = parseInt(hours);
    let ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${String(h).padStart(2, '0')}:${minutes} ${ampm}`;
}
</script>

@endsection
