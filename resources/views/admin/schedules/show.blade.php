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

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .detail-label {
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }

    .detail-value {
        color: var(--text-dark);
        font-size: 1rem;
        margin-bottom: 1rem;
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

    .slots-title {
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 1rem;
    }

    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
        gap: 0.5rem;
    }

    .slot-item {
        background: #f8fafc;
        border: 1px solid var(--card-border);
        padding: 0.5rem;
        border-radius: 8px;
        text-align: center;
        font-size: 0.85rem;
    }

    .slot-item.unavailable {
        background: #fee2e2;
        color: #dc2626;
    }
</style>

<div class="page-header">
    <h1 class="page-title">Schedule Details</h1>
    <a href="{{ route('admin.schedules.index') }}" class="btn-secondary-custom">
        <i class="fas fa-arrow-left"></i> Back to List
    </a>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card-custom mb-4">
            <div class="card-body">
                <h5 class="slots-title">Schedule Information</h5>
                
                <div class="detail-label">Doctor</div>
                <div class="detail-value">{{ $schedule->doctor->name }}</div>

                <div class="detail-label">Working Date</div>
                <div class="detail-value">{{ \Carbon\Carbon::parse($schedule->working_date)->format('l, M d, Y') }}</div>

                <div class="detail-label">Working Hours</div>
                <div class="detail-value">
                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                </div>

                <div class="detail-label">Break Time</div>
                <div class="detail-value">
                    @if($schedule->break_start && $schedule->break_end)
                        {{ \Carbon\Carbon::parse($schedule->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('H:i') }}
                    @else
                        No break time
                    @endif
                </div>

                <div class="detail-label">Slot Duration</div>
                <div class="detail-value">{{ $schedule->slot_duration }} minutes</div>

                <div class="detail-label">Status</div>
                <div class="detail-value">
                    @if($schedule->is_active)
                        <span class="badge-active">Active</span>
                    @else
                        <span class="badge-inactive">Inactive</span>
                    @endif
                </div>

                @if($schedule->notes)
                    <div class="detail-label">Notes</div>
                    <div class="detail-value">{{ $schedule->notes }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-body">
                <h5 class="slots-title">Generated Time Slots ({{ $schedule->slots->count() }})</h5>
                
                @if($schedule->slots->isEmpty())
                    <p class="text-muted">No slots generated yet.</p>
                @else
                    <div class="slot-grid">
                        @foreach($schedule->slots as $slot)
                            <div class="slot-item {{ !$slot->is_available ? 'unavailable' : '' }}">
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                @if(!$slot->is_available)
                                    <br><small>Booked</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
