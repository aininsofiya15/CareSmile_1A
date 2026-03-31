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

    .btn-view {
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
</style>

<div class="page-header">
    <h1 class="page-title">Manage Appointments</h1>
</div>

<div class="card-custom">
    <div class="card-body p-0">

        <table class="table-custom">
            <thead>
                <tr>
                    <th>Patient</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Service</th>
                    <th>Reschedule</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>John</td>
                    <td>2026-04-01</td>
                    <td>10:00</td>
                    <td>Scaling</td>
                    <td>Pending</td>
                    <td>
                        <button class="btn-action btn-view">Approve</button>
                        <button class="btn-action btn-delete">Reject</button>
                    </td>
                </tr>
            </tbody>

        </table>

    </div>
</div>

@endsection
