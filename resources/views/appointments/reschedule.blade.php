@extends('layouts.app')

@section('content')

<style>
.page-header { margin-bottom: 20px; }
.page-title { font-size: 28px; font-weight: 600; color: #1f2937; }

.card-custom {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    padding: 20px;
}

.form-label { font-weight: 500; }
.form-select { padding: 8px 10px; border-radius: 6px; }

.btn-primary-custom {
    background-color: #3b82f6;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    border: none;
}

.btn-secondary-custom {
    background-color: #6c757d;
    color: white;
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    text-decoration: none;
}

.current-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 8px;
    padding: 12px 16px;
    margin-bottom: 20px;
    color: #0369a1;
}
</style>

<div class="page-header">
    <h1 class="page-title">Reschedule Appointment</h1>
</div>

<div class="card-custom">

    {{-- CURRENT INFO --}}
    <div class="current-info">
        <strong>Current Appointment:</strong>
        {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M Y') }}
        at {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
        @if($appointment->end_time)
            to {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
        @endif
        - {{ $appointment->service }}
    </div>

    {{-- ERROR --}}
    @if($errors->any())
        <div style="background:#fee2e2; padding:10px; margin-bottom:10px;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    @if(! $service)
        <div style="background:#fee2e2; padding:10px; margin-bottom:10px;">
            <p>Unable to find the service duration for this appointment.</p>
        </div>
    @endif

    <form method="POST" action="{{ route('patient.appointments.reschedule.submit', $appointment->id) }}">
        @csrf

        {{-- STEP 1: DATE --}}
        <div class="mb-3">
            <label class="form-label">Select New Date</label>
            <select id="date-select" class="form-select">
                <option value="">-- Select Date --</option>
                @foreach($availableSchedules->groupBy('working_date') as $date => $schedules)
                    <option value="{{ $date }}">
                        {{ \Carbon\Carbon::parse($date)->format('d M Y (l)') }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- STEP 2: SLOT --}}
        <div class="mb-3">
            <label class="form-label">Available Time Slot</label>
            <select name="slot_id" id="slot-select" class="form-select" required>
                <option value="">-- Select Date First --</option>
            </select>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn-primary-custom" @disabled(! $service)>
                Confirm Reschedule
            </button>
            <a href="{{ route('patient.appointments') }}" class="btn-secondary-custom">
                Cancel
            </a>
        </div>

    </form>
</div>

<script>
const dateSelect = document.getElementById('date-select');
const slotSelect = document.getElementById('slot-select');
const serviceId = @json($service?->id);
const appointmentId = @json($appointment->id);

dateSelect.addEventListener('change', loadSlots);

function loadSlots() {
    let date = dateSelect.value;

    slotSelect.innerHTML = '<option value="">Loading...</option>';

    if (!serviceId) {
        slotSelect.innerHTML = '<option value="">Service duration is unavailable</option>';
        return;
    }

    if (!date) {
        slotSelect.innerHTML = '<option value="">-- Select Date First --</option>';
        return;
    }

    fetch(`/get-slots/${date}?service_id=${serviceId}&appointment_id=${appointmentId}`)
        .then(res => res.json())
        .then(data => {
            let options = '<option value="">-- Select Time Slot --</option>';

            if (data.length === 0) {
                options = '<option value="">No slots available</option>';
            }

            data.forEach(schedule => {
                schedule.slots.forEach(slot => {
                    let start = formatTime(slot.start_time);
                    let end = formatTime(slot.appointment_end_time);

                    options += `
                        <option value="${slot.id}">
                            Dr. ${schedule.doctor.name} - ${start} to ${end}
                        </option>
                    `;
                });
            });

            slotSelect.innerHTML = options;
        })
        .catch(() => {
            slotSelect.innerHTML = '<option>Error loading slots</option>';
        });
}

function formatTime(timeStr) {
    let [h, m] = timeStr.split(':');
    let hour = parseInt(h);
    let ampm = hour >= 12 ? 'PM' : 'AM';
    hour = hour % 12 || 12;
    return `${String(hour).padStart(2, '0')}:${m} ${ampm}`;
}
</script>

@endsection
