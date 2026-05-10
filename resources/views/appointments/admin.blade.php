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
</style>

<div class="page-header">
    <h1 class="page-title">Appointments List</h1>
</div>

<form method="GET" action="{{ route('admin.appointments') }}" style="margin-bottom: 15px;">
    <select name="status" onchange="this.form.submit()" style="padding:8px; border-radius:6px;">
        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All</option>

        <option value="scheduled" {{ request('status') == 'scheduled' || !request('status') ? 'selected' : '' }}>
            Scheduled
        </option>

        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>No-show</option>
    </select>
</form>

<div class="card-custom">

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
                <tr>
                    <td>
                        {{ $a->patient->name ?? 'N/A' }}
                    </td>

                    <td>{{ $a->appointment_date }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                        @if($a->end_time)
                            - {{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }}
                        @endif
                    </td>
                    <td>{{ $a->service }}</td>

                    <td class="status-{{ $a->status }}">
                        {{ ucfirst($a->status) }}
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
                    <td colspan="6" class="text-muted">
                        No appointments found
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

@endsection
