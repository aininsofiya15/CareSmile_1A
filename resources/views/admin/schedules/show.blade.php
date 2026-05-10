@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.1);
        --shadow-soft: 0 16px 38px rgba(20, 33, 61, 0.09);
    }

    .schedule-shell {
        padding: 1.5rem 0;
    }

    .page-header {
        align-items: flex-start;
        display: flex;
        gap: 1rem;
        justify-content: space-between;
        margin-bottom: 1.2rem;
        flex-wrap: wrap;
    }

    .page-title {
        color: var(--text-dark);
        font-size: 1.8rem;
        font-weight: 850;
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-muted);
        margin: 0.25rem 0 0;
    }

    .back-button {
        align-items: center;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 6px 16px rgba(20, 33, 61, 0.06);
        color: var(--text-dark);
        display: inline-flex;
        font-weight: 800;
        gap: 0.45rem;
        padding: 0.7rem 1rem;
        text-decoration: none;
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
        grid-template-columns: repeat(auto-fill, minmax(116px, 1fr));
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
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #64748b;
    }

    .slot-state {
        display: block;
        font-size: 0.68rem;
        font-weight: 800;
        margin-top: 0.15rem;
        text-transform: uppercase;
    }

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
        .schedule-card {
            padding: 1rem;
        }

        .schedule-badges {
            justify-content: flex-start;
        }

        .stats-grid {
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

@php
    $workingDate = \Carbon\Carbon::parse($schedule->working_date);
    $slotsPanelId = 'admin-schedule-slots-'.$schedule->id;
@endphp

<div class="container schedule-shell">
    <div class="page-header">
        <div>
            <h1 class="page-title">Schedule Details</h1>
            <p class="page-subtitle">Review schedule status, utilization, break time, and generated slots.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to List
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
                        <div class="slot-pill {{ !$slot->is_available ? 'booked' : '' }}">
                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }}
                            <span class="d-block small">{{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}</span>
                            <span class="slot-state">{{ $slot->is_available ? 'Available' : 'Booked' }}</span>
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
