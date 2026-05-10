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

    .schedule-page-header {
        margin-bottom: 1.4rem;
    }

    .schedule-page-title {
        color: var(--text-dark);
        font-size: 1.8rem;
        font-weight: 850;
        margin: 0;
    }

    .schedule-page-subtitle {
        color: var(--text-muted);
        margin: 0.3rem 0 0;
    }

    .schedule-stack {
        display: grid;
        gap: 1rem;
    }

    .schedule-card {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        padding: 1.25rem;
    }

    .schedule-header {
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

    .schedule-chip {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #f1f5f9; color: #64748b; }
    .status-fully-booked { background: #ffedd5; color: #c2410c; }
    .status-unavailable { background: #fee2e2; color: #991b1b; }

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

    .stat-label {
        color: var(--text-muted);
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .stat-value {
        color: var(--text-dark);
        font-size: 1.2rem;
        font-weight: 850;
        margin-top: 0.2rem;
    }

    .utilization-progress {
        height: 8px;
        background: #e2e8f0;
        border-radius: 999px;
        overflow: hidden;
        margin-top: 1rem;
    }

    .utilization-progress-fill {
        height: 100%;
        border-radius: inherit;
        background: #22c55e;
    }

    .utilization-low,
    .utilization-moderate { background: #22c55e; }
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

    .break-title {
        font-size: 0.78rem;
        font-weight: 850;
        text-transform: uppercase;
    }

    .break-time {
        font-weight: 800;
        margin-top: 0.15rem;
    }

    .slot-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #dbe4f0;
    }

    .slot-title {
        color: var(--text-dark);
        font-weight: 850;
    }

    .slot-subtitle {
        color: var(--text-muted);
        font-size: 0.85rem;
        margin-top: 0.15rem;
    }

    .slot-collapse-btn {
        border: 0;
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        border-radius: 999px;
        font-weight: 850;
        padding: 0.55rem 0.9rem;
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
        grid-template-columns: repeat(auto-fill, minmax(118px, 1fr));
        gap: 0.6rem;
        padding-top: 1rem;
    }

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

    .slot-pill.available {
        background: #f0fdf4;
        border-color: #bbf7d0;
        color: #166534;
    }

    .slot-pill.booked {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .slot-pill.unavailable {
        background: #fef2f2;
        border-color: #fecaca;
        color: #991b1b;
    }

    .empty-state {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        color: var(--text-muted);
        padding: 3rem 1.5rem;
        text-align: center;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    .pagination-custom {
        display: flex;
        justify-content: center;
        padding: 1rem;
    }

    @media (max-width: 768px) {
        .schedule-card {
            padding: 1rem;
        }

        .schedule-badges {
            justify-content: flex-start;
        }

        .schedule-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .slot-toolbar {
            align-items: stretch;
            flex-direction: column;
        }

        .slot-collapse-btn {
            width: 100%;
        }
    }
</style>

<div class="schedule-page-header">
    <h1 class="schedule-page-title">My Schedule</h1>
    <p class="schedule-page-subtitle">A cleaner overview of your working hours, utilization, and generated slots.</p>
</div>

@if($schedules->isEmpty())
    <div class="empty-state">
        <i class="fas fa-calendar-times"></i>
        <p class="mb-1 font-weight-bold">No schedules available.</p>
        <p class="mb-0">Create a new doctor schedule to begin managing appointments.</p>
    </div>
@else
    <div class="schedule-stack">
        @foreach($schedules as $schedule)
            @php
                $summary = $scheduleUtilizationSummaries[$schedule->id] ?? [
                    'total_slots' => $schedule->slots->count(),
                    'booked_slots' => $schedule->slots->where('is_available', false)->count(),
                    'remaining_slots' => $schedule->slots->where('is_available', true)->count(),
                    'utilization_percentage' => 0,
                    'utilization_label' => 'No Bookings',
                    'utilization_class' => 'utilization-none',
                ];
                $workingDate = \Carbon\Carbon::parse($schedule->working_date);
                $slotsPanelId = 'schedule-slots-'.$schedule->id;
            @endphp

            <article class="schedule-card">
                <div class="schedule-header">
                    <div>
                        <div class="schedule-date-label">{{ $workingDate->format('l') }}</div>
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
                    <div class="schedule-stat">
                        <div class="stat-label">Total</div>
                        <div class="stat-value">{{ $summary['total_slots'] }}</div>
                    </div>
                    <div class="schedule-stat">
                        <div class="stat-label">Booked</div>
                        <div class="stat-value">{{ $summary['booked_slots'] }}</div>
                    </div>
                    <div class="schedule-stat">
                        <div class="stat-label">Available</div>
                        <div class="stat-value">{{ $summary['remaining_slots'] }}</div>
                    </div>
                    <div class="schedule-stat">
                        <div class="stat-label">Utilized</div>
                        <div class="stat-value">{{ $summary['utilization_percentage'] }}%</div>
                    </div>
                </div>

                <div class="utilization-progress" aria-label="Utilization {{ $summary['utilization_percentage'] }}%">
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
                        <div class="slot-title">Available Slots ({{ $schedule->slots->count() }})</div>
                        <div class="slot-subtitle">Collapsed by default to keep the schedule easy to scan.</div>
                    </div>
                    <button type="button" class="slot-collapse-btn js-slot-toggle" data-target="{{ $slotsPanelId }}" aria-expanded="false">
                        View Slots
                    </button>
                </div>

                <div class="slot-panel" id="{{ $slotsPanelId }}">
                    @if($schedule->slots->isEmpty())
                        <div class="text-muted pt-3">No slots generated for this schedule.</div>
                    @else
                        <div class="slot-grid">
                            @foreach($schedule->slots as $slot)
                                @php
                                    $slotClass = $schedule->isBookable()
                                        ? ($slot->is_available ? 'available' : 'booked')
                                        : 'unavailable';
                                @endphp
                                <div class="slot-pill {{ $slotClass }}">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                                    <span class="d-block small">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
@endif

@if($schedules->hasPages())
    <div class="pagination-custom">
        {{ $schedules->links() }}
    </div>
@endif

<script>
document.querySelectorAll('.js-slot-toggle').forEach((button) => {
    button.addEventListener('click', () => {
        const panel = document.getElementById(button.dataset.target);
        const isOpen = panel.classList.toggle('is-open');

        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        button.textContent = isOpen ? 'Hide Slots' : 'View Slots';
    });
});
</script>
@endsection
