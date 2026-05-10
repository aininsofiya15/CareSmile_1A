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

    .table-custom {
        width: 100%;
        border-collapse: collapse;
        background: white;
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

    .card-custom {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 20px;
    }

    .card-body {
        padding: 16px;
    }

    .p-0 {
        padding: 0;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        margin-right: 5px;
        display: inline-block;
    }

    .btn-edit {
        background-color: #3b82f6;
        color: white;
    }

    .btn-delete {
        background-color: #ef4444;
        color: white;
    }

    .btn-action:hover {
        opacity: 0.8;
    }

    .status-scheduled {
        color: blue;
        font-weight: 500;
    }

    .status-completed {
        color: green;
    }

    .status-cancelled {
        color: red;
    }

    .status-no_show {
        color: orange;
        font-weight: 500;
    }
</style>

<div class="page-header">
    <h1 class="page-title">My Appointments</h1>
</div>

<div class="card-custom">
    <div class="card-body p-0">

        <div style="margin-bottom: 15px;">
            <a href="{{ route('patient.appointments.create') }}" class="btn-action btn-edit">
                + Book New Appointment
            </a>
        </div>

        <table class="table-custom">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($appointments as $a)
                <tr>
                    <td>{{ $a->appointment_date }}</td>
                    <td>
                        {{ \Carbon\Carbon::parse($a->appointment_time)->format('h:i A') }}
                        @if($a->end_time)
                            - {{ \Carbon\Carbon::parse($a->end_time)->format('h:i A') }}
                        @endif
                    </td>
                    <td>{{ $a->service }}</td>
                    <td class="status-{{ $a->status }}">
                        {{ $a->status }}
                    </td>
                    {{-- <td> --}}
                        {{-- <a href="#" class="btn-action btn-edit">Reschedule</a> --}}
                        {{-- <a href="{{ route('appointments.reschedule', $a->id) }}" class="btn-action btn-edit">
                            Reschedule
                        </a> --}}


                        {{-- <a href="#" class="btn-action btn-delete">Cancel</a> --}}
                        {{-- <form action="{{ route('appointments.cancel', $a->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn-action btn-delete">
                                Cancel
                            </button>
                        </form>
                    </td> --}}

                    <td>
                        @if($a->status !== 'cancelled' && $a->status !== 'completed' && $a->status !== 'no_show')

                            <a href="{{ route('patient.appointments.reschedule', $a->id) }}" class="btn-action btn-edit">
                                Reschedule
                            </a>

                            <form action="{{ route('patient.appointments.cancel', $a->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                    Cancel
                                </button>
                            </form>

                        @else
                            <span style="color: gray;">No actions</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        No appointments found
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>
</div>

@endsection
