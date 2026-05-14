@extends('layouts.app')

@section('content')

<style>
    /* 1. The New Blue Banner Header Styles */
    .banner-header {
        background-color: #2563eb; /* Matches the vibrant blue in your screenshot */
        border-radius: 12px;
        padding: 40px 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
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
        color: rgba(255, 255, 255, 0.85); /* Slightly faded white for contrast */
        margin: 6px 0 0 0;
        font-weight: 400;
    }

    /* 2. The White Button Styles */
    .btn-banner-action {
        background-color: white;
        color: #2563eb; /* Blue text to match the background */
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-banner-action:hover {
        background-color: #f8fafc;
        color: #1d4ed8; /* Darker blue on hover */
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* --- Existing Table Styles Below --- */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        background: white;
    }

    .table-custom th,
    .table-custom td {
        padding: 16px;
        border-bottom: 1px solid #e5e7eb;
        text-align: left;
    }

    .table-custom th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #4b5563;
        font-size: 14px;
    }

    .table-custom tr:hover {
        background-color: #f8fafc;
    }

    .table-custom tr:last-child td {
        border-bottom: none;
    }

    .card-custom {
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        border: 1px solid #f3f4f6;
        padding: 0;
        overflow: hidden;
    }

    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        margin-right: 5px;
        display: inline-block;
        border: none;
        cursor: pointer;
    }

    .btn-view   { background-color: #e0f2fe; color: #0369a1; }
    .btn-view:hover { background-color: #bae6fd; }
    .btn-edit   { background-color: #e0e7ff; color: #4f46e5; }
    .btn-edit:hover { background-color: #c7d2fe; }
    .btn-delete { background-color: #fee2e2; color: #dc2626; }
    .btn-delete:hover { background-color: #fecaca; }

    .status-badge {
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-scheduled { background-color: #dbeafe; color: #1d4ed8; }
    .status-completed { background-color: #dcfce7; color: #16a34a; }
    .status-cancelled { background-color: #fee2e2; color: #dc2626; }
    .status-no_show { background-color: #ffedd5; color: #c2410c; }
</style>

<div class="banner-header">
    <div class="banner-left">
        <div class="banner-title-wrapper">
            <i class="fas fa-calendar-alt banner-icon"></i> 
            <h1 class="banner-title">My Appointments</h1>
        </div>
        <p class="banner-subtitle">Manage your scheduled clinic visits and bookings</p>
    </div>
    
    <a href="{{ route('patient.appointments.create') }}" class="btn-banner-action">
        + Book New Appointment
    </a>
</div>

<div class="card-custom">
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
                
                <td>
                    <span class="status-badge status-{{ strtolower($a->status) }}">
                        {{ ucfirst($a->status) }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('patient.appointments.show', $a->id) }}" class="btn-action btn-view">
                        View Details
                    </a>
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
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 40px; color: #6b7280;">
                    <i class="fas fa-calendar-times" style="font-size: 24px; margin-bottom: 10px; opacity: 0.5;"></i><br>
                    No appointments found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection