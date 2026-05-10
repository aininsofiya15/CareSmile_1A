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
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .btn-primary-custom {
        background: var(--brand-blue);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary-custom:hover {
        background: var(--brand-blue-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .utilization-summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .utilization-summary-card {
        border: 1px solid var(--card-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: var(--shadow-soft);
        padding: 1rem;
    }

    .utilization-summary-label {
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .utilization-summary-value {
        color: var(--text-dark);
        font-size: 1.45rem;
        font-weight: 800;
        margin: 0;
    }

    .utilization-metrics {
        min-width: 180px;
    }

    .utilization-line {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        color: var(--text-muted);
        font-size: 0.8rem;
        font-weight: 700;
        margin-bottom: 0.35rem;
    }

    .utilization-progress {
        height: 8px;
        overflow: hidden;
        border-radius: 999px;
        background: #e2e8f0;
        margin-bottom: 0.45rem;
    }

    .utilization-progress-fill {
        height: 100%;
        border-radius: inherit;
        background: var(--brand-blue);
    }

    .utilization-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.25rem 0.6rem;
        font-size: 0.75rem;
        font-weight: 800;
    }

    .utilization-none {
        background: #f1f5f9;
        color: #64748b;
    }

    .utilization-low {
        background: #fef3c7;
        color: #b45309;
    }

    .utilization-moderate {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .utilization-high {
        background: #ffedd5;
        color: #c2410c;
    }

    .utilization-full {
        background: #fee2e2;
        color: #dc2626;
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.875rem;
        border-bottom: 1px solid var(--card-border);
    }

    .table-custom td {
        padding: 1rem;
        border-bottom: 1px solid var(--card-border);
        color: var(--text-dark);
    }

    .table-custom tr:last-child td {
        border-bottom: none;
    }

    .badge-dentist {
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .badge-active {
        background: #dcfce7;
        color: #16a34a;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-inactive {
        background: #f1f5f9;
        color: #64748b;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-fully-booked {
        background: #ffedd5;
        color: #c2410c;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-unavailable {
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: var(--brand-blue-light);
        color: var(--brand-blue);
    }

    .btn-edit:hover {
        background: var(--brand-blue);
        color: #fff;
    }

    .btn-view {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .btn-view:hover {
        background: #4f46e5;
        color: #fff;
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: #fff;
    }

    .impact-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.4rem;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        background: #fff7ed;
        color: #c2410c;
        font-size: 0.75rem;
        font-weight: 700;
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
        background: #fef2f2;
        color: #991b1b;
        border-bottom: 1px solid #fecaca;
    }

    .impact-modal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 800;
    }

    .impact-modal-body {
        padding: 1.5rem;
        color: var(--text-dark);
    }

    .impact-detail-list {
        padding-left: 1rem;
        margin: 0.75rem 0 0;
    }

    .impact-appointments {
        margin-top: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
    }

    .impact-appointment-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 0.75rem;
        padding: 0.7rem 0.85rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.9rem;
    }

    .impact-appointment-row:last-child {
        border-bottom: 0;
    }

    .impact-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        padding: 1rem 1.5rem 1.5rem;
    }

    .btn-modal-cancel,
    .btn-modal-danger {
        border: 0;
        border-radius: 10px;
        padding: 0.65rem 1rem;
        font-weight: 700;
    }

    .btn-modal-cancel {
        background: #f1f5f9;
        color: #475569;
    }

    .btn-modal-danger {
        background: #dc2626;
        color: #fff;
    }

    .btn-modal-danger:disabled {
        background: #94a3b8;
        cursor: not-allowed;
    }

    .empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .pagination-custom {
        display: flex;
        justify-content: center;
        padding: 1rem;
        gap: 0.5rem;
    }

    .pagination-custom .page-link {
        color: var(--brand-blue);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
    }

    .pagination-custom .page-link:hover {
        background: var(--brand-blue-light);
    }

    .pagination-custom .page-item.active .page-link {
        background: var(--brand-blue);
        border-color: var(--brand-blue);
    }
</style>

<div class="page-header">
    <h1 class="page-title">Doctor Schedules</h1>
    <a href="{{ route('admin.schedules.create') }}" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Add Schedule
    </a>
</div>

@if(isset($adminUtilizationSummary))
    <div class="utilization-summary-grid">
        <div class="utilization-summary-card">
            <div class="utilization-summary-label">Total Schedules</div>
            <p class="utilization-summary-value">{{ $adminUtilizationSummary['total_schedules'] }}</p>
        </div>
        <div class="utilization-summary-card">
            <div class="utilization-summary-label">Generated Slots</div>
            <p class="utilization-summary-value">{{ $adminUtilizationSummary['total_slots'] }}</p>
        </div>
        <div class="utilization-summary-card">
            <div class="utilization-summary-label">Booked Slots</div>
            <p class="utilization-summary-value">{{ $adminUtilizationSummary['booked_slots'] }}</p>
        </div>
        <div class="utilization-summary-card">
            <div class="utilization-summary-label">Average Utilization</div>
            <p class="utilization-summary-value">{{ $adminUtilizationSummary['average_utilization_percentage'] }}%</p>
            <span class="utilization-badge {{ $adminUtilizationSummary['utilization_class'] }}">
                {{ $adminUtilizationSummary['utilization_label'] }}
            </span>
        </div>
    </div>
@endif

<div class="card-custom">
    <div class="card-body p-0">
        @if($schedules->isEmpty())
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No schedules found. Create your first schedule.</p>
            </div>
        @else
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Dentist</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Break</th>
                        <th>Slot Duration</th>
                        <th>Status</th>
                        <th>Utilization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        @php($impactSummary = $scheduleImpactSummaries[$schedule->id] ?? ['count' => 0, 'appointments' => []])
                        @php($utilizationSummary = $scheduleUtilizationSummaries[$schedule->id] ?? ['total_slots' => 0, 'booked_slots' => 0, 'remaining_slots' => 0, 'utilization_percentage' => 0, 'utilization_label' => 'No Bookings', 'utilization_class' => 'utilization-none'])
                        <tr>
                            <td>
                                <span class="badge-dentist">
                                    {{ $schedule->doctor->name }}
                                </span>
                                @if($impactSummary['count'] > 0)
                                    <div class="impact-pill">
                                        <i class="fas fa-triangle-exclamation"></i>
                                        {{ $impactSummary['count'] }} appointment{{ $impactSummary['count'] === 1 ? '' : 's' }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::parse($schedule->working_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                            <td>
                                @if($schedule->break_start && $schedule->break_end)
                                    {{ \Carbon\Carbon::parse($schedule->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $schedule->slot_duration }} min</td>
                            <td>
                                <span class="badge-{{ str_replace('_', '-', $schedule->status) }}">
                                    {{ $schedule->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <div class="utilization-metrics">
                                    <div class="utilization-line">
                                        <span>Total {{ $utilizationSummary['total_slots'] }}</span>
                                        <span>Booked {{ $utilizationSummary['booked_slots'] }}</span>
                                    </div>
                                    <div class="utilization-progress" aria-label="Utilization {{ $utilizationSummary['utilization_percentage'] }}%">
                                        <div class="utilization-progress-fill" style="width: {{ $utilizationSummary['utilization_percentage'] }}%;"></div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <span class="utilization-badge {{ $utilizationSummary['utilization_class'] }}">
                                            {{ $utilizationSummary['utilization_percentage'] }}% {{ $utilizationSummary['utilization_label'] }}
                                        </span>
                                        <span class="text-muted small">Available {{ $utilizationSummary['remaining_slots'] }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button
                                        type="button"
                                        class="btn-action btn-delete js-delete-schedule"
                                        data-action="{{ route('admin.schedules.destroy', $schedule->id) }}"
                                        data-dentist="{{ $schedule->doctor->name }}"
                                        data-date="{{ \Carbon\Carbon::parse($schedule->working_date)->format('M d, Y') }}"
                                        data-time="{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}"
                                        data-impact-count="{{ $impactSummary['count'] }}"
                                        data-appointments='@json($impactSummary['appointments'])'
                                    >
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if($schedules->hasPages())
    <div class="pagination-custom">
        {{ $schedules->links() }}
    </div>
@endif

<div class="impact-modal-backdrop" id="delete-impact-modal" aria-hidden="true">
    <div class="impact-modal" role="dialog" aria-modal="true" aria-labelledby="delete-impact-title">
        <div class="impact-modal-header">
            <h2 class="impact-modal-title" id="delete-impact-title">Confirm Schedule Delete</h2>
        </div>
        <div class="impact-modal-body">
            <p id="delete-impact-message" class="mb-2"></p>
            <ul class="impact-detail-list">
                <li><strong>Dentist:</strong> <span id="delete-impact-dentist"></span></li>
                <li><strong>Date:</strong> <span id="delete-impact-date"></span></li>
                <li><strong>Working Time:</strong> <span id="delete-impact-time"></span></li>
            </ul>
            <div id="delete-impact-appointments" class="impact-appointments"></div>
        </div>
        <div class="impact-modal-actions">
            <button type="button" class="btn-modal-cancel js-close-delete-modal">Cancel</button>
            <form method="POST" id="delete-impact-form">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-modal-danger" id="delete-impact-submit">Delete Schedule</button>
            </form>
        </div>
    </div>
</div>

<script>
const deleteModal = document.getElementById('delete-impact-modal');
const deleteForm = document.getElementById('delete-impact-form');
const deleteSubmit = document.getElementById('delete-impact-submit');
const deleteMessage = document.getElementById('delete-impact-message');
const deleteAppointments = document.getElementById('delete-impact-appointments');

document.querySelectorAll('.js-delete-schedule').forEach((button) => {
    button.addEventListener('click', () => {
        const impactCount = Number(button.dataset.impactCount || 0);
        const appointments = JSON.parse(button.dataset.appointments || '[]');

        deleteForm.action = button.dataset.action;
        document.getElementById('delete-impact-dentist').textContent = button.dataset.dentist;
        document.getElementById('delete-impact-date').textContent = button.dataset.date;
        document.getElementById('delete-impact-time').textContent = button.dataset.time;

        if (impactCount > 0) {
            deleteMessage.textContent = 'This schedule cannot be deleted because it has existing patient appointments. Please cancel or reschedule the appointments first.';
            deleteSubmit.disabled = true;
            deleteSubmit.textContent = 'Cannot Delete - Appointments Exist';
            renderAffectedAppointments(appointments);
        } else {
            deleteMessage.textContent = 'This schedule has no affected appointments. Confirm deletion to remove it and its generated slots.';
            deleteSubmit.disabled = false;
            deleteSubmit.textContent = 'Delete Schedule';
            deleteAppointments.innerHTML = '';
        }

        deleteModal.classList.add('is-visible');
        deleteModal.setAttribute('aria-hidden', 'false');
    });
});

document.querySelectorAll('.js-close-delete-modal').forEach((button) => {
    button.addEventListener('click', closeDeleteModal);
});

deleteModal.addEventListener('click', (event) => {
    if (event.target === deleteModal) {
        closeDeleteModal();
    }
});

function closeDeleteModal() {
    deleteModal.classList.remove('is-visible');
    deleteModal.setAttribute('aria-hidden', 'true');
}

function renderAffectedAppointments(appointments) {
    if (!appointments.length) {
        deleteAppointments.innerHTML = '';
        return;
    }

    deleteAppointments.innerHTML = appointments.map((appointment) => `
        <div class="impact-appointment-row">
            <div>
                <strong>${appointment.patient_name}</strong><br>
                <span>${appointment.appointment_date} at ${appointment.appointment_time}</span>
            </div>
            <span>${appointment.status}</span>
        </div>
    `).join('');
}
</script>
@endsection
