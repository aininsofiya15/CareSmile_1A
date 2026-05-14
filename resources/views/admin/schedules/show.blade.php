@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-dark: #1456cc;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.1);
        --shadow-soft: 0 16px 38px rgba(20, 33, 61, 0.09);
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

    /* --- Schedule Details Card Styles --- */
    .schedule-shell {
        padding: 1.5rem 0;
    }

    .schedule-card {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 22px;
        box-shadow: var(--shadow-soft);
        padding: 1.35rem;
    }

    .schedule-header {
        align-items: flex-start;
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        flex-wrap: wrap;
    }

    .schedule-date-label {
        color: var(--brand-blue);
        font-size: 0.78rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .schedule-date {
        color: var(--text-dark);
        font-size: 1.45rem;
        font-weight: 850;
        margin: 0.12rem 0;
    }

    .schedule-summary {
        color: var(--text-muted);
        margin: 0;
    }

    .schedule-badges {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .schedule-chip,
    .status-badge,
    .utilization-badge {
        align-items: center;
        border-radius: 999px;
        display: inline-flex;
        font-size: 0.8rem;
        font-weight: 850;
        gap: 0.35rem;
        padding: 0.42rem 0.75rem;
        white-space: nowrap;
    }

    .schedule-chip {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #475569;
    }

    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #f1f5f9; color: #64748b; }
    .status-fully-booked { background: #ffedd5; color: #c2410c; }
    .status-unavailable { background: #fee2e2; color: #991b1b; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1.2rem;
    }

    .stat-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        padding: 0.95rem;
    }

    .stat-label {
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .stat-value {
        color: var(--text-dark);
        font-size: 1.25rem;
        font-weight: 850;
        margin-top: 0.2rem;
    }

    .utilization-progress {
        background: #e2e8f0;
        border-radius: 999px;
        height: 9px;
        margin: 1rem 0 0.55rem;
        overflow: hidden;
    }

    .utilization-progress-fill {
        background: #22c55e;
        border-radius: inherit;
        height: 100%;
    }

    .utilization-low,
    .utilization-moderate { background: #22c55e; }
    .utilization-high { background: #f97316; }
    .utilization-full { background: #dc2626; }
    .utilization-none { background: #94a3b8; }

    .utilization-badge.utilization-none { background: #f1f5f9; color: #64748b; }
    .utilization-badge.utilization-low { background: #fef3c7; color: #b45309; }
    .utilization-badge.utilization-moderate { background: #dbeafe; color: #1d4ed8; }
    .utilization-badge.utilization-high { background: #ffedd5; color: #c2410c; }
    .utilization-badge.utilization-full { background: #fee2e2; color: #dc2626; }

    .break-card {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 15px;
        color: #92400e;
        margin-top: 1rem;
        padding: 0.9rem 1rem;
    }

    .break-title {
        font-size: 0.78rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .break-time {
        font-weight: 850;
        margin-top: 0.12rem;
    }

    .slot-toolbar {
        align-items: center;
        border-top: 1px dashed #dbe4f0;
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        margin-top: 1.1rem;
        padding-top: 1.1rem;
    }

    .slot-title {
        color: var(--text-dark);
        font-weight: 850;
    }

    .slot-subtitle {
        color: var(--text-muted);
        font-size: 0.86rem;
        margin-top: 0.15rem;
    }

    .slot-collapse-btn {
        background: var(--brand-blue-light);
        border: 0;
        border-radius: 999px;
        color: var(--brand-blue);
        font-weight: 850;
        padding: 0.58rem 0.95rem;
    }

    .slot-panel {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.25s ease;
    }

    .slot-panel.is-open {
        max-height: 900px;
    }

    .slot-grid {
        display: grid;
        gap: 0.62rem;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        padding-top: 1rem;
    }

    .slot-pill {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 12px;
        color: #166534;
        font-size: 0.82rem;
        font-weight: 850;
        padding: 0.6rem;
        text-align: center;
    }

    .slot-pill.booked {
        background: #f0f4ff;
        border-color: #c7d2fe;
        color: #4338ca;
    }

    .slot-state {
        display: block;
        font-size: 0.68rem;
        font-weight: 800;
        margin-top: 0.15rem;
        text-transform: uppercase;
    }

    .btn-slot-details {
        background: #4f46e5;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-size: 0.7rem;
        font-weight: 800;
        margin-top: 0.35rem;
        padding: 0.28rem 0.6rem;
        transition: background 0.15s;
    }

    .btn-slot-details:hover { background: #4338ca; }

    /* Admin Slot Detail Modal */
    .slot-modal-overlay {
        align-items: center;
        background: rgba(14, 23, 55, 0.5);
        bottom: 0;
        display: none;
        justify-content: center;
        left: 0;
        position: fixed;
        right: 0;
        top: 0;
        z-index: 9999;
    }

    .slot-modal-overlay.is-open { display: flex; }

    .slot-modal-box {
        background: white;
        border-radius: 18px;
        box-shadow: 0 24px 60px rgba(14, 23, 55, 0.22);
        max-width: 460px;
        padding: 1.6rem;
        position: relative;
        width: calc(100% - 2rem);
    }

    .slot-modal-close {
        background: #f1f5f9;
        border: none;
        border-radius: 50%;
        color: #475569;
        cursor: pointer;
        font-size: 1.1rem;
        height: 32px;
        line-height: 1;
        position: absolute;
        right: 1rem;
        top: 1rem;
        width: 32px;
    }

    .slot-modal-title {
        color: var(--text-dark);
        font-size: 1rem;
        font-weight: 850;
        margin-bottom: 1rem;
        padding-right: 2rem;
    }

    .slot-detail-row {
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        gap: 0.5rem;
        padding: 0.55rem 0;
    }

    .slot-detail-row:last-child { border-bottom: none; }

    .slot-detail-label {
        color: #64748b;
        flex-shrink: 0;
        font-size: 0.78rem;
        font-weight: 700;
        min-width: 100px;
        text-transform: uppercase;
    }

    .slot-detail-value {
        color: var(--text-dark);
        font-size: 0.88rem;
        font-weight: 600;
        word-break: break-word;
    }

    .modal-status-badge {
        border-radius: 999px;
        display: inline-block;
        font-size: 0.75rem;
        font-weight: 800;
        padding: 0.22rem 0.65rem;
    }

    .modal-status-scheduled { background: #dbeafe; color: #1d4ed8; }
    .modal-status-completed  { background: #dcfce7; color: #166534; }
    .modal-status-cancelled  { background: #fee2e2; color: #dc2626; }
    .modal-status-no_show    { background: #ffedd5; color: #c2410c; }

    .empty-slot-state {
        color: var(--text-muted);
        padding-top: 1rem;
    }

    .footer-note {
        border-top: 1px solid #e2e8f0;
        color: var(--text-muted);
        font-size: 0.82rem;
        margin-top: 1.35rem;
        padding-top: 1rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .schedule-card { padding: 1rem; }
        .schedule-badges { justify-content: flex-start; }
        .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .slot-toolbar { align-items: stretch; flex-direction: column; }
        .slot-collapse-btn { width: 100%; }
    }
</style>

@php
    $workingDate = \Carbon\Carbon::parse($schedule->working_date);
    $slotsPanelId = 'admin-schedule-slots-'.$schedule->id;
@endphp

<div class="container schedule-shell">
    
    <div class="banner-header">
        <div class="banner-left">
            <div class="banner-title-wrapper">
                <i class="fas fa-calendar-day banner-icon"></i> 
                <h1 class="banner-title">Schedule Details</h1>
            </div>
            <p class="banner-subtitle">Review schedule status, utilization, break time, and generated slots.</p>
        </div>
        
        <a href="{{ route('admin.schedules.index') }}" class="btn-banner-action">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <article class="schedule-card">
        <div class="schedule-header">
            <div>
                <div class="schedule-date-label">{{ $workingDate->format('l') }}</div>
                <h2 class="schedule-date">{{ $workingDate->format('M d, Y') }}</h2>
                <p class="schedule-summary">
                    {{ $schedule->doctor->name }} &bull;
                    {{ $utilizationSummary['remaining_slots'] }} remaining of {{ $utilizationSummary['total_slots'] }} generated slots
                </p>
            </div>

            <div class="schedule-badges">
                <span class="status-badge {{ $schedule->statusBadgeClass() }}">
                    {{ $schedule->statusLabel() }}
                </span>
                <span class="schedule-chip">
                    <i class="fas fa-clock"></i>
                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                </span>
                <span class="schedule-chip">
                    <i class="fas fa-stopwatch"></i>
                    {{ $schedule->slot_duration }} min
                </span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total Slots</div>
                <div class="stat-value">{{ $utilizationSummary['total_slots'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Booked</div>
                <div class="stat-value">{{ $utilizationSummary['booked_slots'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Available</div>
                <div class="stat-value">{{ $utilizationSummary['remaining_slots'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Utilized</div>
                <div class="stat-value">{{ $utilizationSummary['utilization_percentage'] }}%</div>
            </div>
        </div>

        <div class="utilization-progress" aria-label="Utilization {{ $utilizationSummary['utilization_percentage'] }}%">
            <div class="utilization-progress-fill {{ $utilizationSummary['utilization_class'] }}" style="width: {{ $utilizationSummary['utilization_percentage'] }}%;"></div>
        </div>
        <span class="utilization-badge {{ $utilizationSummary['utilization_class'] }}">
            {{ $utilizationSummary['utilization_percentage'] }}% {{ $utilizationSummary['utilization_label'] }}
        </span>

        @if($schedule->break_start && $schedule->break_end)
            <div class="break-card">
                <div class="break-title"><i class="fas fa-coffee"></i> Break Time</div>
                <div class="break-time">
                    {{ \Carbon\Carbon::parse($schedule->break_start)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('h:i A') }}
                </div>
            </div>
        @endif

        <div class="slot-toolbar">
            <div>
                <div class="slot-title">Generated Slots ({{ $schedule->slots->count() }})</div>
                <div class="slot-subtitle">Slot list is collapsed by default to reduce visual clutter.</div>
            </div>
            <button type="button" class="slot-collapse-btn js-slot-toggle" data-target="{{ $slotsPanelId }}" aria-expanded="false">
                View Slots
            </button>
        </div>

        <div class="slot-panel" id="{{ $slotsPanelId }}">
            @if($schedule->slots->isEmpty())
                <div class="empty-slot-state">No slots generated for this schedule.</div>
            @else
                <div class="slot-grid">
                    @foreach($schedule->slots as $slot)
                        @php
                            $isBooked = ! $slot->is_available;
                            $appt     = $isBooked ? ($slotAppointmentMap[$slot->id] ?? null) : null;
                            $statusLabels = [
                                'scheduled'  => 'Scheduled',
                                'completed'  => 'Completed',
                                'cancelled'  => 'Cancelled',
                                'no_show'    => 'No Show',
                            ];
                        @endphp
                        <div class="slot-pill {{ $isBooked ? 'booked' : '' }}">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                            <span class="d-block small">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                            <span class="slot-state">{{ $isBooked ? 'Booked' : 'Available' }}</span>

                            @if($isBooked && $appt)
                                <button type="button" class="btn-slot-details"
                                    data-slot-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}"
                                    data-date="{{ \Carbon\Carbon::parse($schedule->working_date)->format('d M Y') }}"
                                    data-doctor="{{ $schedule->doctor->name }}"
                                    data-patient="{{ $appt->patient?->name ?? 'N/A' }}"
                                    data-service="{{ $appt->service ?? 'N/A' }}"
                                    data-status="{{ $appt->status }}"
                                    data-status-label="{{ $statusLabels[$appt->status] ?? ucfirst($appt->status) }}"
                                    data-created="{{ $appt->created_at?->format('d M Y, h:i A') ?? 'N/A' }}"
                                    data-notes="{{ $appt->notes ?? '' }}"
                                    onclick="openAdminSlotModal(this)">
                                    View Details
                                </button>
                            @elseif($isBooked)
                                <span class="slot-state" style="font-size:0.65rem; color:#94a3b8;">No record</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="footer-note">
            CareSmile Management &bull; Generated via Admin Control Panel
        </div>
    </article>
</div>

{{-- Admin Booked Slot Detail Modal --}}
<div class="slot-modal-overlay" id="adminSlotModal" role="dialog" aria-modal="true" aria-labelledby="adminSlotModalTitle">
    <div class="slot-modal-box">
        <button type="button" class="slot-modal-close" onclick="closeAdminSlotModal()" aria-label="Close">&times;</button>
        <div class="slot-modal-title" id="adminSlotModalTitle">
            <i class="fas fa-calendar-check" style="color:#4f46e5; margin-right:6px;"></i>
            Booked Slot Details
        </div>

        <div class="slot-detail-row">
            <span class="slot-detail-label">Slot Time</span>
            <span class="slot-detail-value" id="am-slot-time">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Date</span>
            <span class="slot-detail-value" id="am-date">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Doctor</span>
            <span class="slot-detail-value" id="am-doctor">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Patient</span>
            <span class="slot-detail-value" id="am-patient">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Service</span>
            <span class="slot-detail-value" id="am-service">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Status</span>
            <span class="slot-detail-value">
                <span class="modal-status-badge" id="am-status-badge">—</span>
            </span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Booked On</span>
            <span class="slot-detail-value" id="am-created">—</span>
        </div>
        <div class="slot-detail-row" id="am-notes-row">
            <span class="slot-detail-label">Notes</span>
            <span class="slot-detail-value" id="am-notes">—</span>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.js-slot-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const panel = document.getElementById(button.dataset.target);
        const isOpen = panel.classList.toggle('is-open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        button.textContent = isOpen ? 'Hide Slots' : 'View Slots';
    });
});

function openAdminSlotModal(btn) {
    const d = btn.dataset;
    document.getElementById('am-slot-time').textContent = d.slotTime  || '—';
    document.getElementById('am-date').textContent      = d.date       || '—';
    document.getElementById('am-doctor').textContent    = d.doctor     || '—';
    document.getElementById('am-patient').textContent   = d.patient    || '—';
    document.getElementById('am-service').textContent   = d.service    || '—';
    document.getElementById('am-created').textContent   = d.created    || '—';

    const badge = document.getElementById('am-status-badge');
    badge.textContent  = d.statusLabel || d.status || '—';
    badge.className    = 'modal-status-badge modal-status-' + (d.status || '');

    const notesRow = document.getElementById('am-notes-row');
    const notes    = d.notes ? d.notes.trim() : '';
    document.getElementById('am-notes').textContent = notes || 'No notes available.';
    notesRow.style.display = 'flex';

    document.getElementById('adminSlotModal').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeAdminSlotModal() {
    document.getElementById('adminSlotModal').classList.remove('is-open');
    document.body.style.overflow = '';
}

document.getElementById('adminSlotModal').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeAdminSlotModal();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAdminSlotModal();
});
</script>
@endsection