@extends('layouts.app')

@section('content')

<style>
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

    .status-pending {
        color: orange;
        font-weight: 500;
    }

    .status-approved {
        color: green;
    }

    .status-cancelleds{
        color: red;
    }
</style>

<div class="page-header">
    <h1 class="page-title">My Appointments</h1>
</div>

<div class="card-custom">
    <div class="card-body p-0">

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
                    <td>{{ $a->appointment_time }}</td>
                    <td>{{ $a->service }}</td>
                    <td class="status-{{ $a->status }}">
                        {{ $a->status }}
                    </td>
                    <td>
                        <a href="#" class="btn-action btn-edit">Reschedule</a>
                        <a href="#" class="btn-action btn-delete">Cancel</a>
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
