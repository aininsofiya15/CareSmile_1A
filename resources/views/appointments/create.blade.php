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

    .btn-primary-custom:disabled {
        background: #94a3b8;
        cursor: not-allowed;
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

    .slot-section {
        border: 1px solid rgba(31, 111, 255, 0.08);
        border-radius: 14px;
        background: #f8fafc;
        padding: 1rem;
    }

    .slot-state-message {
        color: var(--text-muted);
        margin: 0;
    }

    .slot-state-message.is-error {
        color: #b91c1c;
        font-weight: 600;
    }

    .slot-session {
        margin-top: 1rem;
    }

    .slot-session:first-child {
        margin-top: 0;
    }

    .slot-session-header {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        align-items: baseline;
        margin-bottom: 0.75rem;
    }

    .slot-session-title {
        color: var(--text-dark);
        font-size: 1rem;
        font-weight: 700;
        margin: 0;
    }

    .slot-session-range {
        color: var(--text-muted);
        font-size: 0.85rem;
        white-space: nowrap;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        gap: 0.75rem;
    }

    .slot-card {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #fff;
        color: var(--text-dark);
        padding: 0.9rem;
        text-align: left;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.05);
        transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease, background 0.18s ease;
    }

    .slot-card.available {
        cursor: pointer;
    }

    .slot-card.available:hover,
    .slot-card.available:focus {
        border-color: var(--brand-blue);
        box-shadow: 0 10px 24px rgba(31, 111, 255, 0.14);
        outline: none;
        transform: translateY(-1px);
    }

    .slot-card.selected {
        border-color: var(--brand-blue);
        background: var(--brand-blue-light);
        box-shadow: 0 10px 26px rgba(31, 111, 255, 0.18);
    }

    .slot-card.disabled,
    .slot-card:disabled {
        background: #f1f5f9;
        color: #94a3b8;
        cursor: not-allowed;
        box-shadow: none;
    }

    .slot-dentist {
        display: block;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .slot-time {
        display: block;
        color: var(--text-muted);
        font-size: 0.93rem;
        margin-bottom: 0.65rem;
    }

    .slot-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
    }

    .slot-badge.available {
        background: #dcfce7;
        color: #15803d;
    }

    .slot-badge.booked,
    .slot-badge.unavailable,
    .slot-badge.fully-booked {
        background: #e2e8f0;
        color: #64748b;
    }

    .selected-slot-summary {
        display: none;
        margin-top: 1rem;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        padding: 0.85rem 1rem;
        font-weight: 600;
    }

    .selected-slot-summary.is-visible {
        display: block;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Book Appointment</h1>
</div>

<div class="card-custom">
    <div class="card-body p-4">

        {{-- ERROR MESSAGES --}}
        @if($errors->any())
            <div class="alert alert-danger mb-3">
                @foreach($errors->all() as $error)
                    <p class="mb-0">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form action="{{ route('patient.appointments.store') }}" method="POST" id="appointment-form">
            @csrf

            {{-- STEP 1: SERVICE --}}
            <div class="mb-3">
                <label class="form-label">Service</label>
                <select name="service_id" id="service-select" class="form-select" required>
                    <option value="">Select Service</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->name }} - RM{{ number_format($service->price, 2) }}
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
            <div class="mb-3" id="slot-section">
                <label class="form-label">Available Time Slot</label>
                <input type="hidden" name="slot_id" id="selected-slot-id" value="">

                <div class="slot-section">
                    <div id="slot-feedback" class="slot-state-message">
                        Select a service and date to view available appointment slots.
                    </div>
                    <div id="slot-card-groups"></div>
                </div>

                <div id="selected-slot-summary" class="selected-slot-summary" aria-live="polite"></div>
            </div>

            <div class="mt-3 d-flex gap-2">
                <button type="submit" class="btn-primary-custom" id="book-appointment-button" @disabled($services->isEmpty() || $availableSchedules->isEmpty())>
                    Book Appointment
                </button>
                <a href="{{ route('patient.appointments') }}" class="btn-secondary-custom">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

{{-- JAVASCRIPT: when service or date changes, load valid appointment starts --}}
<script>
const serviceSelect = document.getElementById('service-select');
const dateSelect = document.getElementById('date-select');
const appointmentForm = document.getElementById('appointment-form');
const selectedSlotInput = document.getElementById('selected-slot-id');
const slotCardGroups = document.getElementById('slot-card-groups');
const slotFeedback = document.getElementById('slot-feedback');
const slotSection = document.getElementById('slot-section');
const selectedSlotSummary = document.getElementById('selected-slot-summary');
const bookAppointmentButton = document.getElementById('book-appointment-button');
const canLoadBookableSlots = @json($services->isNotEmpty() && $availableSchedules->isNotEmpty());

serviceSelect.addEventListener('change', loadSlots);
dateSelect.addEventListener('change', loadSlots);
appointmentForm.addEventListener('submit', validateSelectedSlot);

function loadSlots() {
    const serviceId = serviceSelect.value;
    const date = dateSelect.value;

    clearSelectedSlot();
    renderSlotMessage('Loading appointment slots...');

    if (!serviceId) {
        renderSlotMessage('Select a service to view available appointment slots.');
        return;
    }

    if (!date) {
        renderSlotMessage('Select an available date to view appointment slots.');
        return;
    }

    fetch(`/get-slots/${date}?service_id=${serviceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                renderSlotMessage('No available appointment slots at the moment. Please try another date or contact the clinic.', false, true);
                return;
            }

            const groupedSlots = buildSessionGroups(data);
            renderSlotCards(groupedSlots);
        })
        .catch(() => {
            renderSlotMessage('Error loading slots. Try again.', true);
        });
}

function buildSessionGroups(data) {
    const groups = {
        morning: {
            title: 'Morning Session',
            range: '08:00 AM - 12:00 PM',
            slots: [],
        },
        afternoon: {
            title: 'Afternoon Session',
            range: '12:00 PM - 02:00 PM',
            slots: [],
        },
        evening: {
            title: 'Evening Session',
            range: '02:00 PM - Clinic Closing',
            slots: [],
        },
    };

    data.forEach(schedule => {
        const dentistName = schedule.doctor?.name || 'Dentist';
        const scheduleStatus = schedule.status || 'active';
        const scheduleIsBookable = scheduleStatus === 'active' && schedule.is_active !== false;

        (schedule.slots || []).forEach(slot => {
            const startTime = formatTime(slot.start_time);
            const endTime = formatTime(slot.appointment_end_time || slot.end_time);
            const session = sessionForTime(slot.start_time);
            const status = slotStatus(slot, scheduleStatus, scheduleIsBookable);

            groups[session].slots.push({
                id: slot.id,
                dentistName,
                displayTime: `${startTime} - ${endTime}`,
                label: `Dr. ${dentistName} - ${startTime} to ${endTime}`,
                status: status.label,
                statusClass: status.className,
                isAvailable: status.isAvailable,
            });
        });
    });

    return groups;
}

function slotStatus(slot, scheduleStatus, scheduleIsBookable) {
    if (!scheduleIsBookable) {
        if (scheduleStatus === 'fully_booked') {
            return { label: 'Fully Booked', className: 'fully-booked', isAvailable: false };
        }

        return { label: 'Unavailable', className: 'unavailable', isAvailable: false };
    }

    if (slot.is_available === false) {
        return { label: 'Booked', className: 'booked', isAvailable: false };
    }

    return { label: 'Available', className: 'available', isAvailable: true };
}

function renderSlotCards(groups) {
    slotFeedback.textContent = '';
    slotFeedback.classList.remove('is-error');
    slotCardGroups.innerHTML = '';

    const availableSessions = Object.values(groups).filter(group => group.slots.length > 0);

    if (availableSessions.length === 0) {
        renderSlotMessage('No available appointment slots at the moment. Please try another date or contact the clinic.', false, true);
        return;
    }

    bookAppointmentButton.disabled = !canLoadBookableSlots;

    availableSessions.forEach(group => {
        const sessionElement = document.createElement('section');
        sessionElement.className = 'slot-session';

        const headerElement = document.createElement('div');
        headerElement.className = 'slot-session-header';
        headerElement.innerHTML = `
            <h3 class="slot-session-title">${group.title}</h3>
            <span class="slot-session-range">${group.range}</span>
        `;

        const gridElement = document.createElement('div');
        gridElement.className = 'slot-grid';

        group.slots.forEach(slot => {
            const card = document.createElement('button');
            card.type = 'button';
            card.className = `slot-card ${slot.isAvailable ? 'available' : 'disabled'}`;
            card.dataset.slotId = slot.id;
            card.dataset.slotLabel = slot.label;
            card.setAttribute('aria-pressed', 'false');

            if (!slot.isAvailable) {
                card.disabled = true;
            }

            card.innerHTML = `
                <span class="slot-dentist">Dr. ${slot.dentistName}</span>
                <span class="slot-time">${slot.displayTime}</span>
                <span class="slot-badge ${slot.statusClass}">${slot.status}</span>
            `;

            card.addEventListener('click', () => selectSlotCard(card));
            gridElement.appendChild(card);
        });

        sessionElement.appendChild(headerElement);
        sessionElement.appendChild(gridElement);
        slotCardGroups.appendChild(sessionElement);
    });
}

function selectSlotCard(card) {
    document.querySelectorAll('.slot-card.selected').forEach(selectedCard => {
        selectedCard.classList.remove('selected');
        selectedCard.setAttribute('aria-pressed', 'false');
    });

    card.classList.add('selected');
    card.setAttribute('aria-pressed', 'true');
    selectedSlotInput.value = card.dataset.slotId;
    slotFeedback.textContent = '';
    slotFeedback.classList.remove('is-error');

    selectedSlotSummary.textContent = `Selected Slot: ${card.dataset.slotLabel}`;
    selectedSlotSummary.classList.add('is-visible');
}

function clearSelectedSlot() {
    selectedSlotInput.value = '';
    selectedSlotSummary.textContent = '';
    selectedSlotSummary.classList.remove('is-visible');
    document.querySelectorAll('.slot-card.selected').forEach(selectedCard => {
        selectedCard.classList.remove('selected');
        selectedCard.setAttribute('aria-pressed', 'false');
    });
}

function validateSelectedSlot(event) {
    if (selectedSlotInput.value) {
        return;
    }

    event.preventDefault();
    renderSlotMessage('Please select an available appointment slot before booking.', true);
    slotSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function renderSlotMessage(message, isError = false, disableBooking = false) {
    slotCardGroups.innerHTML = '';
    slotFeedback.textContent = message;
    slotFeedback.classList.toggle('is-error', isError);
    bookAppointmentButton.disabled = !canLoadBookableSlots || disableBooking;
}

function sessionForTime(timeStr) {
    const [hours, minutes] = timeStr.split(':').map(Number);
    const totalMinutes = (hours * 60) + minutes;

    if (totalMinutes < 12 * 60) {
        return 'morning';
    }

    if (totalMinutes < 14 * 60) {
        return 'afternoon';
    }

    return 'evening';
}

function formatTime(timeStr) {
    let [hours, minutes] = timeStr.split(':');
    let h = parseInt(hours);
    let ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return `${String(h).padStart(2, '0')}:${minutes} ${ampm}`;
}
</script>

@endsection
