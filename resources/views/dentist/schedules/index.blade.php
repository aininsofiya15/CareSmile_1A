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
        margin-bottom: 1.5rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-dark);
        margin: 0;
    }

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
        margin-bottom: 1rem;
    }

    .card-custom .card-body {
        padding: 1.25rem;
    }

    .schedule-date {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
    }

    .schedule-day {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .time-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        padding: 0.5rem 1rem;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .slot-badge {
        background: #f1f5f9;
        color: var(--text-muted);
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 500;
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
        background: #fee2e2;
        color: #dc2626;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .slots-preview {
        margin-top: 0.75rem;
        padding-top: 0.75rem;
        border-top: 1px dashed var(--card-border);
    }

    .slots-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .slot-item {
        display: inline-block;
        background: #fff;
        border: 1px solid var(--card-border);
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        margin-right: 0.25rem;
        margin-bottom: 0.25rem;
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
    <h1 class="page-title">My Schedule</h1>
</div>

@if($schedules->isEmpty())
    <div class="card-custom">
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No schedules assigned yet. Please contact the admin.</p>
        </div>
    </div>
@else
    @foreach($schedules as $schedule)
        <div class="card-custom">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <div class="schedule-date">
                            {{ \Carbon\Carbon::parse($schedule->working_date)->format('l, M d, Y') }}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <span class="time-badge">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                        </span>
                        <span class="slot-badge">
                            {{ $schedule->slot_duration }} min slots
                        </span>
                        @if($schedule->is_active)
                            <span class="badge-active">Active</span>
                        @else
                            <span class="badge-inactive">Inactive</span>
                        @endif
                    </div>
                </div>

                @if($schedule->break_start && $schedule->break_end)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="fas fa-coffee"></i> Break: {{ \Carbon\Carbon::parse($schedule->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('H:i') }}
                        </small>
                    </div>
                @endif

                <div class="slots-preview">
                    <div class="slots-title">Available Time Slots:</div>
                    @forelse($schedule->slots as $slot)
                        <span class="slot-item">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
                    @empty
                        <span class="text-muted">No slots generated</span>
                    @endforelse
                </div>
            </div>
        </div>
    @endforeach
@endif

@if($schedules->hasPages())
    <div class="pagination-custom">
        {{ $schedules->links() }}
    </div>
@endif
@endsection
