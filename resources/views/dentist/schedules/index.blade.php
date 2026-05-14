@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.1);
        --shadow-soft: 0 14px 34px rgba(20, 33, 61, 0.08);
    }

    /* --- Blue Banner Header --- */
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

    .banner-left { display: flex; flex-direction: column; }

    .banner-title-wrapper { display: flex; align-items: center; gap: 12px; color: white; }

    .banner-icon { font-size: 26px; }

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

    /* --- Section Headings --- */
    .section-heading {
        align-items: center;
        display: flex;
        gap: 10px;
        margin: 0 0 14px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #e2e8f0;
    }

    .section-heading-title {
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.07em;
        text-transform: uppercase;
        margin: 0;
    }

    .section-heading-title.active  { color: #1d4ed8; }
    .section-heading-title.history { color: #94a3b8; }

    .section-count {
        background: #f1f5f9;
        border-radius: 999px;
        color: #64748b;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 2px 9px;
    }

    .history-toggle-btn {
        background: #f1f5f9;
        border: none;
        border-radius: 999px;
        color: #64748b;
        cursor: pointer;
        font-size: 0.78rem;
        font-weight: 700;
        margin-left: auto;
        padding: 4px 14px;
        transition: background 0.15s;
    }

    .history-toggle-btn:hover { background: #e2e8f0; }

    .history-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s ease;
    }

    .history-content.is-open { max-height: 99999px; }

    .section-gap { margin-top: 28px; }

    /* --- Schedule Card --- */
    .schedule-stack { display: grid; gap: 1rem; }

    .schedule-card {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        padding: 1.25rem;
    }

    .schedule-card.is-past {
        border-color: #e2e8f0;
        box-shadow: 0 4px 12px rgba(20, 33, 61, 0.04);
        opacity: 0.88;
    }

    .schedule-header-inner {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .schedule-date-label {
        color: var(--brand-blue);
        font-size: 0.78rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .schedule-date-label.past { color: #94a3b8; }

    .schedule-date {
        color: var(--text-dark);
        font-size: 1.28rem;
        font-weight: 850;
        margin: 0.15rem 0;
    }

    .schedule-summary {
        color: var(--text-muted);
        font-size: 0.92rem;
        margin: 0;
    }

    .schedule-badges {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .schedule-chip,
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 800;
        padding: 0.42rem 0.72rem;
        white-space: nowrap;
    }

    .schedule-chip { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }

    .status-active       { background: #dcfce7; color: #15803d; }
    .status-inactive     { background: #f1f5f9; color: #64748b; }
    .status-fully-booked { background: #ffedd5; color: #c2410c; }
    .status-unavailable  { background: #fee2e2; color: #991b1b; }

    /* Past history badge */
    .status-past-history { background: #f1f5f9; color: #94a3b8; border: 1px solid #e2e8f0; }

    .schedule-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .schedule-stat {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.85rem;
    }

    .stat-label { color: var(--text-muted); font-size: 0.72rem; font-weight: 800; text-transform: uppercase; }
    .stat-value { color: var(--text-dark); font-size: 1.2rem; font-weight: 850; margin-top: 0.2rem; }

    .utilization-progress {
        height: 8px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 1rem;
    }

    .utilization-progress-fill { height: 100%; border-radius: inherit; background: #22c55e; }
    .utilization-high { background: #f97316; }
    .utilization-full { background: #dc2626; }
    .utilization-none { background: #94a3b8; }

    .break-card {
        margin-top: 1rem;
        border: 1px solid #fde68a;
        background: #fffbeb;
        color: #92400e;
        border-radius: 14px;
        padding: 0.85rem 1rem;
    }

    .break-title { font-size: 0.78rem; font-weight: 850; text-transform: uppercase; }
    .break-time  { font-weight: 800; margin-top: 0.15rem; }

    .slot-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #dbe4f0;
    }

    .slot-title    { color: var(--text-dark); font-weight: 850; }
    .slot-subtitle { color: var(--text-muted); font-size: 0.85rem; margin-top: 0.15rem; }

    .slot-collapse-btn {
        border: 0;
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        border-radius: 999px;
        font-weight: 850;
        padding: 0.55rem 0.9rem;
    }

    .slot-panel { max-height: 0; overflow: hidden; transition: max-height 0.25s ease; }
    .slot-panel.is-open { max-height: 900px; }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(118px, 1fr));
        gap: 0.6rem;
        padding-top: 1rem;
    }

    /* --- Slot Pill Colours --- */
    .slot-pill {
        border: 1px solid #dbe4f0;
        border-radius: 12px;
        background: #fff;
        padding: 0.58rem 0.65rem;
        color: var(--text-dark);
        font-size: 0.82rem;
        font-weight: 800;
        text-align: center;
    }

    .slot-pill.available   { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
    .slot-pill.booked      { background: #f0f4ff; border-color: #c7d2fe; color: #4338ca; }
    .slot-pill.unavailable { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

    /* Past slot — gray/faded; applies to all past slots regardless of appointment status */
    .slot-pill.past {
        background: #f1f5f9;
        border-color: #e2e8f0;
        color: #94a3b8;
        opacity: 0.72;
    }

    /* Past booked: completed appointment — soft green, faded */
    .slot-pill.past-completed {
        background: #f0fdf4;
        border-color: #d1fae5;
        color: #6b9e7a;
        opacity: 0.75;
    }

    /* Past booked: no-show — soft amber, faded */
    .slot-pill.past-no_show {
        background: #fff7ed;
        border-color: #fed7aa;
        color: #b45309;
        opacity: 0.75;
    }

    /* Past booked: still scheduled (overdue) — soft yellow, faded */
    .slot-pill.past-scheduled {
        background: #fefce8;
        border-color: #fde68a;
        color: #92400e;
        opacity: 0.75;
    }

    /* Slot status label shown inside every pill */
    .slot-status-label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-top: 3px;
        text-transform: uppercase;
    }

    /* --- Slot Details Button --- */
    .btn-slot-details {
        background: #4f46e5;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-size: 0.7rem;
        font-weight: 800;
        margin-top: 0.3rem;
        padding: 0.26rem 0.55rem;
        transition: background 0.15s;
    }

    .btn-slot-details:hover { background: #4338ca; }

    /* Past booked slot details button — muted styling */
    .btn-slot-details.past-btn {
        background: #94a3b8;
    }

    .btn-slot-details.past-btn:hover { background: #64748b; }

    /* --- Dentist Slot Detail Modal --- */
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
        max-height: 92vh;
        max-width: 500px;
        overflow-y: auto;
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

    .slot-detail-value { color: var(--text-dark); font-size: 0.88rem; font-weight: 600; word-break: break-word; }

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

    .patient-reminder {
        background: #fffbeb;
        border: 1px solid #fcd34d;
        border-left: 4px solid #f59e0b;
        border-radius: 10px;
        margin-top: 1rem;
        padding: 0.85rem 1rem;
    }

    .patient-reminder-title {
        color: #92400e;
        font-size: 0.8rem;
        font-weight: 800;
        margin-bottom: 0.35rem;
        text-transform: uppercase;
    }

    .patient-reminder-text { color: #78350f; font-size: 0.84rem; line-height: 1.55; margin: 0; }

    /* --- Empty States --- */
    .empty-state {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        color: var(--text-muted);
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-state i { color: #cbd5e1; font-size: 2.5rem; margin-bottom: 1rem; }

    .empty-section {
        background: #f8fafc;
        border: 1px dashed #e2e8f0;
        border-radius: 14px;
        color: #94a3b8;
        font-size: 0.88rem;
        padding: 1.4rem 1rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .schedule-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .slot-toolbar   { flex-direction: column; align-items: stretch; }
    }
</style>

<div class="banner-header">
    <div class="banner-left">
        <div class="banner-title-wrapper">
            <i class="fas fa-calendar-alt banner-icon"></i>
            <h1 class="banner-title">My Schedule</h1>
        </div>
        <p class="banner-subtitle">Overview of your working hours, utilization, and generated slots.</p>
    </div>
</div>

@php
    $hasAny = $activeSchedules->isNotEmpty() || $pastSchedules->isNotEmpty();
    $today  = \Carbon\Carbon::today();
    $statusLabels = [
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'cancelled'  => 'Cancelled',
        'no_show'    => 'No-show',
    ];
@endphp

@if(! $hasAny)
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p class="mb-1 font-weight-bold">No schedules available.</p>
        <p class="mb-0">Please contact administration if you believe this is an error.</p>
    </div>
@else

    {{-- ================================================================
         SECTION 1 — ACTIVE & UPCOMING SCHEDULES (today + future)
    ================================================================ --}}
    <div class="section-heading">
        <i class="fas fa-calendar-check" style="color:#1d4ed8; font-size:1rem;"></i>
        <h2 class="section-heading-title active">Active &amp; Upcoming</h2>
        <span class="section-count">{{ $activeSchedules->count() }}</span>
    </div>

    @if($activeSchedules->isEmpty())
        <div class="empty-section">
            <i class="fas fa-calendar-minus" style="margin-right:6px; opacity:.5;"></i>
            No active or upcoming schedules.
        </div>
    @else
        <div class="schedule-stack">
            @foreach($activeSchedules as $schedule)
                @php
                    $summary = $scheduleUtilizationSummaries[$schedule->id] ?? [
                        'total_slots'           => $schedule->slots->count(),
                        'booked_slots'          => $schedule->slots->where('is_available', false)->count(),
                        'remaining_slots'       => $schedule->slots->where('is_available', true)->count(),
                        'utilization_percentage'=> 0,
                        'utilization_label'     => 'No Bookings',
                        'utilization_class'     => 'utilization-none',
                    ];
                    $workingDate          = \Carbon\Carbon::parse($schedule->working_date);
                    $isTodaySchedule      = $workingDate->isToday();
                    $slotsPanelId         = 'schedule-slots-'.$schedule->id;
                    $scheduleAppointments = $appointmentsByDate->get($workingDate->toDateString(), collect());
                @endphp

                <article class="schedule-card">
                    <div class="schedule-header-inner">
                        <div>
                            <div class="schedule-date-label">
                                {{ $isTodaySchedule ? 'Today — ' : '' }}{{ $workingDate->format('l') }}
                            </div>
                            <h2 class="schedule-date">{{ $workingDate->format('M d, Y') }}</h2>
                            <p class="schedule-summary">
                                {{ $summary['remaining_slots'] }} remaining of {{ $summary['total_slots'] }} generated slots
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

                    <div class="schedule-stats">
                        <div class="schedule-stat"><div class="stat-label">Total</div><div class="stat-value">{{ $summary['total_slots'] }}</div></div>
                        <div class="schedule-stat"><div class="stat-label">Booked</div><div class="stat-value">{{ $summary['booked_slots'] }}</div></div>
                        <div class="schedule-stat"><div class="stat-label">Available Slots</div><div class="stat-value">{{ $summary['remaining_slots'] }}</div></div>
                        <div class="schedule-stat"><div class="stat-label">Utilized</div><div class="stat-value">{{ $summary['utilization_percentage'] }}%</div></div>
                    </div>

                    <div class="utilization-progress">
                        <div class="utilization-progress-fill {{ $summary['utilization_class'] }}" style="width: {{ $summary['utilization_percentage'] }}%;"></div>
                    </div>

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
                            <div class="slot-subtitle">View the individual time slots for this day.</div>
                        </div>
                        <button type="button" class="slot-collapse-btn js-slot-toggle" data-target="{{ $slotsPanelId }}" aria-expanded="false">
                            View Slots
                        </button>
                    </div>

                    <div class="slot-panel" id="{{ $slotsPanelId }}">
                        <div class="slot-grid">
                            @foreach($schedule->slots as $slot)
                                @php
                                    // Determine if this slot's time has already passed
                                    // Carbon::parse(time-only string) defaults to today's date
                                    $slotEndCarbon  = \Carbon\Carbon::parse($slot->end_time);
                                    $isSlotTimePast = $isTodaySchedule && $slotEndCarbon->lte($now);

                                    // Resolve booked appointment via overlap
                                    $isBookedSlot = !$slot->is_available;
                                    $slotAppt     = null;
                                    if ($isBookedSlot) {
                                        $slotStart = \Carbon\Carbon::parse($slot->start_time);
                                        $slotEnd   = \Carbon\Carbon::parse($slot->end_time);
                                        foreach ($scheduleAppointments as $appt) {
                                            $apptStart = \Carbon\Carbon::parse($appt->appointment_time);
                                            $apptEnd   = $appt->end_time
                                                ? \Carbon\Carbon::parse($appt->end_time)
                                                : $apptStart->copy()->addMinutes(30);
                                            if ($apptStart->lt($slotEnd) && $apptEnd->gt($slotStart)) {
                                                $slotAppt = $appt;
                                                break;
                                            }
                                        }
                                    }

                                    // Classify slot
                                    if ($isSlotTimePast) {
                                        if ($isBookedSlot && $slotAppt) {
                                            $apptStatus  = $slotAppt->status;
                                            $slotClass   = 'past past-'.$apptStatus;
                                            $slotLabel   = $statusLabels[$apptStatus] ?? 'Past';
                                            $showDetails = true;
                                        } else {
                                            $slotClass   = 'past';
                                            $slotLabel   = 'Past';
                                            $showDetails = false;
                                        }
                                    } else {
                                        if (! $schedule->isBookable()) {
                                            $slotClass   = 'unavailable';
                                            $slotLabel   = 'Unavailable';
                                            $showDetails = false;
                                        } elseif ($slot->is_available) {
                                            $slotClass   = 'available';
                                            $slotLabel   = 'Available';
                                            $showDetails = false;
                                        } else {
                                            $slotClass   = 'booked';
                                            $slotLabel   = 'Booked';
                                            $showDetails = (bool) $slotAppt;
                                        }
                                    }
                                @endphp

                                <div class="slot-pill {{ $slotClass }}">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                    <span class="d-block small">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                                    <span class="slot-status-label">{{ $slotLabel }}</span>

                                    @if($showDetails && $slotAppt)
                                        <button type="button" class="btn-slot-details {{ $isSlotTimePast ? 'past-btn' : '' }}"
                                            data-slot-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}"
                                            data-date="{{ \Carbon\Carbon::parse($schedule->working_date)->format('d M Y') }}"
                                            data-patient="{{ $slotAppt->patient?->name ?? 'N/A' }}"
                                            data-email="{{ $slotAppt->patient?->email ?? '' }}"
                                            data-phone="{{ $slotAppt->patient?->phone_number ?? '' }}"
                                            data-service="{{ $slotAppt->service ?? 'N/A' }}"
                                            data-start="{{ \Carbon\Carbon::parse($slotAppt->appointment_time)->format('h:i A') }}"
                                            data-end="{{ $slotAppt->end_time ? \Carbon\Carbon::parse($slotAppt->end_time)->format('h:i A') : '' }}"
                                            data-status="{{ $slotAppt->status }}"
                                            data-status-label="{{ $statusLabels[$slotAppt->status] ?? ucfirst($slotAppt->status) }}"
                                            data-notes="{{ $slotAppt->notes ?? '' }}"
                                            onclick="openDentistSlotModal(this)">
                                            View Details
                                        </button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    {{-- ================================================================
         SECTION 2 — SCHEDULE HISTORY (past dates, collapsed by default)
    ================================================================ --}}
    <div class="section-gap">
        <div class="section-heading">
            <i class="fas fa-history" style="color:#94a3b8; font-size:1rem;"></i>
            <h2 class="section-heading-title history">Schedule History</h2>
            <span class="section-count">{{ $pastSchedules->count() }}</span>
            <button type="button" class="history-toggle-btn" id="history-toggle-btn" aria-expanded="false">
                Show History
            </button>
        </div>

        <div class="history-content" id="history-content">
            @if($pastSchedules->isEmpty())
                <div class="empty-section">
                    <i class="fas fa-archive" style="margin-right:6px; opacity:.5;"></i>
                    No past schedules.
                </div>
            @else
                <div class="schedule-stack">
                    @foreach($pastSchedules as $schedule)
                        @php
                            $summary = $scheduleUtilizationSummaries[$schedule->id] ?? [
                                'total_slots'           => $schedule->slots->count(),
                                'booked_slots'          => $schedule->slots->where('is_available', false)->count(),
                                'remaining_slots'       => $schedule->slots->where('is_available', true)->count(),
                                'utilization_percentage'=> 0,
                                'utilization_label'     => 'No Bookings',
                                'utilization_class'     => 'utilization-none',
                            ];
                            $workingDate          = \Carbon\Carbon::parse($schedule->working_date);
                            $slotsPanelId         = 'schedule-slots-'.$schedule->id;
                            $scheduleAppointments = $appointmentsByDate->get($workingDate->toDateString(), collect());
                        @endphp

                        <article class="schedule-card is-past">
                            <div class="schedule-header-inner">
                                <div>
                                    <div class="schedule-date-label past">
                                        {{ $workingDate->format('l') }}
                                    </div>
                                    <h2 class="schedule-date">{{ $workingDate->format('M d, Y') }}</h2>
                                    <p class="schedule-summary">
                                        History — {{ $summary['booked_slots'] }} of {{ $summary['total_slots'] }} slots were booked
                                    </p>
                                </div>

                                <div class="schedule-badges">
                                    {{-- Show "History" badge instead of live status for past schedules --}}
                                    <span class="status-badge status-past-history">
                                        <i class="fas fa-history"></i> History
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

                            <div class="schedule-stats">
                                <div class="schedule-stat"><div class="stat-label">Total</div><div class="stat-value">{{ $summary['total_slots'] }}</div></div>
                                <div class="schedule-stat"><div class="stat-label">Was Booked</div><div class="stat-value">{{ $summary['booked_slots'] }}</div></div>
                                <div class="schedule-stat"><div class="stat-label">Available Slots</div><div class="stat-value">{{ $summary['remaining_slots'] }}</div></div>
                                <div class="schedule-stat"><div class="stat-label">Utilized</div><div class="stat-value">{{ $summary['utilization_percentage'] }}%</div></div>
                            </div>

                            <div class="utilization-progress">
                                <div class="utilization-progress-fill {{ $summary['utilization_class'] }}" style="width: {{ $summary['utilization_percentage'] }}%;"></div>
                            </div>

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
                                    <div class="slot-title">Slot History ({{ $schedule->slots->count() }})</div>
                                    <div class="slot-subtitle">Past slots — faded to indicate historical data.</div>
                                </div>
                                <button type="button" class="slot-collapse-btn js-slot-toggle" data-target="{{ $slotsPanelId }}" aria-expanded="false">
                                    View Slots
                                </button>
                            </div>

                            <div class="slot-panel" id="{{ $slotsPanelId }}">
                                <div class="slot-grid">
                                    @foreach($schedule->slots as $slot)
                                        @php
                                            // All slots in a past schedule are past
                                            $isBookedSlot = !$slot->is_available;
                                            $slotAppt     = null;

                                            if ($isBookedSlot) {
                                                $slotStart = \Carbon\Carbon::parse($slot->start_time);
                                                $slotEnd   = \Carbon\Carbon::parse($slot->end_time);
                                                foreach ($scheduleAppointments as $appt) {
                                                    $apptStart = \Carbon\Carbon::parse($appt->appointment_time);
                                                    $apptEnd   = $appt->end_time
                                                        ? \Carbon\Carbon::parse($appt->end_time)
                                                        : $apptStart->copy()->addMinutes(30);
                                                    if ($apptStart->lt($slotEnd) && $apptEnd->gt($slotStart)) {
                                                        $slotAppt = $appt;
                                                        break;
                                                    }
                                                }
                                            }

                                            // All past-schedule slots are "past"
                                            if ($isBookedSlot && $slotAppt) {
                                                $apptStatus  = $slotAppt->status;
                                                $slotClass   = 'past past-'.$apptStatus;
                                                $slotLabel   = $statusLabels[$apptStatus] ?? 'Past';
                                                $showDetails = true;
                                            } else {
                                                $slotClass   = 'past';
                                                $slotLabel   = 'Past';
                                                $showDetails = false;
                                            }
                                        @endphp

                                        <div class="slot-pill {{ $slotClass }}">
                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                            <span class="d-block small">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                                            <span class="slot-status-label">{{ $slotLabel }}</span>

                                            @if($showDetails && $slotAppt)
                                                <button type="button" class="btn-slot-details past-btn"
                                                    data-slot-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}"
                                                    data-date="{{ \Carbon\Carbon::parse($schedule->working_date)->format('d M Y') }}"
                                                    data-patient="{{ $slotAppt->patient?->name ?? 'N/A' }}"
                                                    data-email="{{ $slotAppt->patient?->email ?? '' }}"
                                                    data-phone="{{ $slotAppt->patient?->phone_number ?? '' }}"
                                                    data-service="{{ $slotAppt->service ?? 'N/A' }}"
                                                    data-start="{{ \Carbon\Carbon::parse($slotAppt->appointment_time)->format('h:i A') }}"
                                                    data-end="{{ $slotAppt->end_time ? \Carbon\Carbon::parse($slotAppt->end_time)->format('h:i A') : '' }}"
                                                    data-status="{{ $slotAppt->status }}"
                                                    data-status-label="{{ $statusLabels[$slotAppt->status] ?? ucfirst($slotAppt->status) }}"
                                                    data-notes="{{ $slotAppt->notes ?? '' }}"
                                                    onclick="openDentistSlotModal(this)">
                                                    View Details
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>{{-- /history-content --}}
    </div>{{-- /section-gap --}}

@endif

{{-- Dentist Booked Slot Detail Modal --}}
<div class="slot-modal-overlay" id="dentistSlotModal" role="dialog" aria-modal="true" aria-labelledby="dentistSlotModalTitle">
    <div class="slot-modal-box">
        <button type="button" class="slot-modal-close" onclick="closeDentistSlotModal()" aria-label="Close">&times;</button>
        <div class="slot-modal-title" id="dentistSlotModalTitle">
            <i class="fas fa-user-injured" style="color:#4f46e5; margin-right:6px;"></i>
            Patient Appointment Details
        </div>

        <div class="slot-detail-row">
            <span class="slot-detail-label">Patient</span>
            <span class="slot-detail-value" id="dm-patient">—</span>
        </div>
        <div class="slot-detail-row" id="dm-email-row">
            <span class="slot-detail-label">Email</span>
            <span class="slot-detail-value" id="dm-email">—</span>
        </div>
        <div class="slot-detail-row" id="dm-phone-row">
            <span class="slot-detail-label">Phone</span>
            <span class="slot-detail-value" id="dm-phone">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Date</span>
            <span class="slot-detail-value" id="dm-date">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Start Time</span>
            <span class="slot-detail-value" id="dm-start">—</span>
        </div>
        <div class="slot-detail-row" id="dm-end-row">
            <span class="slot-detail-label">End Time</span>
            <span class="slot-detail-value" id="dm-end">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Service</span>
            <span class="slot-detail-value" id="dm-service">—</span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Status</span>
            <span class="slot-detail-value">
                <span class="modal-status-badge" id="dm-status-badge">—</span>
            </span>
        </div>
        <div class="slot-detail-row">
            <span class="slot-detail-label">Notes</span>
            <span class="slot-detail-value" id="dm-notes">—</span>
        </div>

        <div class="patient-reminder">
            <div class="patient-reminder-title"><i class="fas fa-clock"></i> Arrival Reminder</div>
            <p class="patient-reminder-text">
                Patient should arrive <strong>15 minutes before</strong> the appointment time.
                If the patient does not show up within <strong>20 minutes</strong>,
                the appointment may be marked as No-show or rescheduled.
            </p>
        </div>
    </div>
</div>

<script>
// --- Slot panel toggle (View Slots / Hide Slots) ---
document.querySelectorAll('.js-slot-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const panel  = document.getElementById(button.dataset.target);
        const isOpen = panel.classList.toggle('is-open');
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        button.textContent = isOpen ? 'Hide Slots' : 'View Slots';
    });
});

// --- Schedule History section toggle (collapsed by default) ---
const historyToggleBtn = document.getElementById('history-toggle-btn');
const historyContent   = document.getElementById('history-content');
if (historyToggleBtn && historyContent) {
    historyToggleBtn.addEventListener('click', () => {
        const isOpen = historyContent.classList.toggle('is-open');
        historyToggleBtn.textContent = isOpen ? 'Hide History' : 'Show History';
        historyToggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
}

// --- Dentist Slot Detail Modal ---
function openDentistSlotModal(btn) {
    const d = btn.dataset;

    document.getElementById('dm-patient').textContent = d.patient  || '—';
    document.getElementById('dm-date').textContent    = d.date      || '—';
    document.getElementById('dm-start').textContent   = d.start     || '—';
    document.getElementById('dm-service').textContent = d.service   || '—';
    document.getElementById('dm-notes').textContent   = (d.notes && d.notes.trim()) ? d.notes.trim() : 'No notes available.';

    const emailRow = document.getElementById('dm-email-row');
    const email    = d.email ? d.email.trim() : '';
    document.getElementById('dm-email').textContent = email || 'N/A';
    emailRow.style.display = 'flex';

    const phoneRow = document.getElementById('dm-phone-row');
    const phone    = d.phone ? d.phone.trim() : '';
    document.getElementById('dm-phone').textContent = phone || 'N/A';
    phoneRow.style.display = 'flex';

    const endRow = document.getElementById('dm-end-row');
    const end    = d.end ? d.end.trim() : '';
    document.getElementById('dm-end').textContent = end || '—';
    endRow.style.display = end ? 'flex' : 'none';

    const badge = document.getElementById('dm-status-badge');
    badge.textContent = d.statusLabel || d.status || '—';
    badge.className   = 'modal-status-badge modal-status-' + (d.status || '');

    document.getElementById('dentistSlotModal').classList.add('is-open');
    document.body.style.overflow = 'hidden';
}

function closeDentistSlotModal() {
    document.getElementById('dentistSlotModal').classList.remove('is-open');
    document.body.style.overflow = '';
}

document.getElementById('dentistSlotModal').addEventListener('click', (e) => {
    if (e.target === e.currentTarget) closeDentistSlotModal();
});

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDentistSlotModal();
});
</script>
@endsection
