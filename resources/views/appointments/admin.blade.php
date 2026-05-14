@extends('layouts.app')

@section('content')

<style>
    .page-header {
        margin-bottom: 20px;
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #1f2937;
    }

    .card-custom {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 20px;
    }

    /* New styles for the card header and dropdown */
    .card-header-custom {
        display: flex;
        justify-content: flex-end; /* Aligns the dropdown to the right */
        align-items: center;
        margin-bottom: 15px;
    }

    .form-select-custom {
        padding: 8px 16px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background-color: #f9fafb;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-select-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th,
    .table-custom td {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .table-custom th {
        background-color: #f9fafb;
        font-weight: 600;
    }

    .table-custom tr:hover {
        background-color: #f3f4f6;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        border: none;
        font-size: 14px;
        cursor: pointer;
    }

    .btn-complete {
        background-color: #10b981;
        color: white;
    }

    .btn-complete:hover {
        opacity: 0.85;
    }
    
    .btn-delete {
        background-color: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-delete:hover {
        background-color: #e5e7eb;
    }

    .status-scheduled {
        color: #2563eb;
        font-weight: 500;
    }

    .status-completed {
        color: #16a34a;
        font-weight: 500;
    }

    .status-cancelled {
        color: #dc2626;
        font-weight: 500;
    }

    .status-no_show {
        color: orange;
        font-weight: 500;
    }

    .text-muted {
        color: gray;
    }

    .services-header {
        background: linear-gradient(135deg, #1f6fff 0%, #1557d6 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px rgba(31, 111, 255, 0.2);
    }

    .overdue-banner {
        background: #fff7ed;
        border: 1px solid #fdba74;
        border-left: 5px solid #f97316;
        border-radius: 10px;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 18px;
        margin-bottom: 18px;
    }

    .overdue-banner-icon { color: #ea580c; font-size: 20px; flex-shrink: 0; }

    .overdue-banner-text { flex: 1; }

    .overdue-banner-title { font-weight: 700; color: #9a3412; font-size: 14px; }

    .overdue-banner-body { color: #7c2d12; font-size: 13px; margin: 2px 0 0; }

    .btn-mark-overdue {
        background: #ea580c;
        border: none;
        border-radius: 7px;
        color: white;
        cursor: pointer;
        font-size: 13px;
        font-weight: 700;
        padding: 8px 14px;
        white-space: nowrap;
        transition: background 0.15s;
    }

    .btn-mark-overdue:hover { background: #c2410c; }

    .reminder-banner {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-left: 5px solid #3b82f6;
        border-radius: 10px;
        padding: 12px 18px;
        margin-bottom: 18px;
        font-size: 13px;
        color: #1e3a5f;
    }

    .overdue-row { background: #fff7ed !important; }

    .overdue-badge {
        background: #ffedd5;
        border-radius: 999px;
        color: #c2410c;
        display: inline-block;
        font-size: 11px;
        font-weight: 800;
        margin-left: 6px;
        padding: 2px 8px;
        vertical-align: middle;
    }

</style>

<div class="services-header">
    <h2 class="fw-bold mb-1"><i class="fas fa-calendar-check me-2"></i>Appointments List</h2>
    <p class="mb-0 text-white-50 small">View and manage all scheduled clinic appointments</p>
</div>

{{-- Overdue Warning Banner --}}
@if($overdueCount > 0)
    <div class="overdue-banner">
        <i class="fas fa-exclamation-triangle overdue-banner-icon"></i>
        <div class="overdue-banner-text">
            <div class="overdue-banner-title">
                {{ $overdueCount }} appointment(s) are overdue and need status review.
            </div>
            <div class="overdue-banner-body">
                These appointments have passed their scheduled time and are still marked as Scheduled.
                Please update their status or use the bulk action below.
            </div>
        </div>
        <form action="{{ route('admin.appointments.mark_overdue') }}" method="POST" style="flex-shrink:0;">
            @csrf
            <button type="button" class="btn-mark-overdue"
                onclick="if(confirm('Mark all {{ $overdueCount }} overdue appointment(s) as No-show?')) this.closest('form').submit()">
                Mark All as No-show
            </button>
        </form>
    </div>
@else
    <div class="reminder-banner">
        <i class="fas fa-bell" style="margin-right:8px;"></i>
        <strong>Reminder:</strong> Please update appointment status on time.
        Appointments that pass their scheduled time without action may be marked as No-show automatically.
    </div>
@endif

<div class="card-custom">

    <div class="card-header-custom">
        <form method="GET" action="{{ route('admin.appointments') }}">
            <select name="status" onchange="this.form.submit()" class="form-select-custom">
                <option value="all"       {{ request('status') == 'all'       ? 'selected' : '' }}>All</option>
                <option value="scheduled" {{ request('status') == 'scheduled' || !request('status') ? 'selected' : '' }}>Scheduled</option>
                <option value="overdue"   {{ request('status') == 'overdue'   ? 'selected' : '' }}>
                    Overdue{{ $overdueCount > 0 ? " ({$overdueCount})" : '' }}
                </option>
                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="no_show"   {{ request('status') == 'no_show'   ? 'selected' : '' }}>No-show</option>
            </select>
        </form>
    </div>

    <table class="table-custom">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Date</th>
                <th>Time</th>
                <th>Service</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>

        <tbody>
            @forelse($appointments as $a)
                @php
                    $apptDateTime  = \Carbon\Carbon::parse($a->appointment_date . ' ' . $a->appointment_time);
                    $apptEndTime   = $a->end_time
                        ? \Carbon\Carbon::parse($a->appointment_date . ' ' . $a->end_time)
                        : $apptDateTime;
                    $isOverdue     = $a->status === 'scheduled' && $apptEndTime->lt($now);
                    $statusLabels  = [
                        'scheduled' => 'Scheduled',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled',
                        'no_show'   => 'No Show',
                    ];
                @endphp
                <tr class="{{ $isOverdue ? 'overdue-row' : '' }}">
                    <td>{{ $a->patient->name ?? 'N/A' }}</td>

                    <td>{{ \Carbon\Carbon::parse($a->appointment_date)->format('d M Y') }}</td>

                    <td>
                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                        @if($a->end_time)
                            - {{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }}
                        @endif
                    </td>

                    <td>{{ $a->service }}</td>

                    <td class="status-{{ $a->status }}">
                        {{ $statusLabels[$a->status] ?? ucfirst(str_replace('_', ' ', $a->status)) }}
                        @if($isOverdue)
                            <span class="overdue-badge">Overdue</span>
                        @endif
                    </td>

                    <td>
                        @if($a->status === 'scheduled')
                            {{-- Complete --}}
                            <form action="{{ route('admin.appointments.complete', $a->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn-action btn-complete"
                                    onclick="return confirm('Mark this appointment as completed?')">
                                    Complete
                                </button>
                            </form>
                            {{-- No-show --}}
                            <form action="{{ route('admin.appointments.no_show', $a->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="btn-action btn-delete"
                                    onclick="return confirm('Mark as no-show?')">
                                    No-show
                                </button>
                            </form>
                        @else
                            <span class="text-muted">No action</span>
                        @endif
                    </td>
                </tr>

            @empty
                <tr>
                    <td colspan="6" class="text-muted" style="text-align: center; padding: 20px;">
                        No appointments found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection