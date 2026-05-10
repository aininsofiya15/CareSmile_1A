@extends('layouts.app')

@section('content')
<style>
    :root {
        --brand-blue: #1f6fff;
        --brand-blue-light: #eef5ff;
        --text-dark: #14213d;
        --text-muted: #6c7a92;
        --card-border: rgba(31, 111, 255, 0.1);
        --shadow-soft: 0 12px 30px rgba(20, 33, 61, 0.08);
    }

    .page-header {
        margin-bottom: 1.5rem;
    }

    .page-title {
        color: var(--text-dark);
        font-size: 1.75rem;
        font-weight: 800;
        margin: 0;
    }

    .page-subtitle {
        color: var(--text-muted);
        margin: 0.25rem 0 0;
    }

    .appointments-card {
        background: #fff;
        border: 1px solid var(--card-border);
        border-radius: 18px;
        box-shadow: var(--shadow-soft);
        overflow: hidden;
    }

    .appointments-table {
        border-collapse: collapse;
        width: 100%;
    }

    .appointments-table th {
        background: #f8fafc;
        color: var(--text-muted);
        font-size: 0.78rem;
        font-weight: 800;
        padding: 0.9rem 1rem;
        text-align: left;
        text-transform: uppercase;
    }

    .appointments-table td {
        border-top: 1px solid #eef2f7;
        color: var(--text-dark);
        padding: 1rem;
        vertical-align: middle;
    }

    .status-badge {
        border-radius: 999px;
        display: inline-flex;
        font-size: 0.78rem;
        font-weight: 800;
        padding: 0.35rem 0.7rem;
    }

    .status-scheduled {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .status-completed {
        background: #dcfce7;
        color: #15803d;
    }

    .status-cancelled,
    .status-no-show {
        background: #fee2e2;
        color: #991b1b;
    }

    .empty-state {
        color: var(--text-muted);
        padding: 3rem 1rem;
        text-align: center;
    }

    .empty-state i {
        color: #cbd5e1;
        font-size: 2.5rem;
        margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
        .appointments-table thead {
            display: none;
        }

        .appointments-table,
        .appointments-table tbody,
        .appointments-table tr,
        .appointments-table td {
            display: block;
            width: 100%;
        }

        .appointments-table tr {
            border-top: 1px solid #eef2f7;
            padding: 0.75rem 0;
        }

        .appointments-table td {
            border: 0;
            padding: 0.45rem 1rem;
        }

        .appointments-table td::before {
            color: var(--text-muted);
            content: attr(data-label);
            display: block;
            font-size: 0.72rem;
            font-weight: 800;
            margin-bottom: 0.15rem;
            text-transform: uppercase;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">My Appointments</h1>
    <p class="page-subtitle">Appointments assigned to your dentist account.</p>
</div>

<div class="appointments-card">
    @if($appointments->isEmpty())
        <div class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <p class="mb-0">No appointments assigned yet.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="appointments-table">
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Service</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($appointments as $appointment)
                        @php
                            $statusClass = 'status-'.str_replace('_', '-', $appointment->status ?: 'scheduled');
                        @endphp
                        <tr>
                            <td data-label="Patient">{{ $appointment->patient?->name ?? 'N/A' }}</td>
                            <td data-label="Date">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}</td>
                            <td data-label="Time">
                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                @if($appointment->end_time)
                                    - {{ \Carbon\Carbon::parse($appointment->end_time)->format('h:i A') }}
                                @endif
                            </td>
                            <td data-label="Service">{{ $appointment->service ?: 'N/A' }}</td>
                            <td data-label="Status">
                                <span class="status-badge {{ $statusClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $appointment->status ?: 'scheduled')) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
