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

    .btn-primary-custom {
        background: var(--brand-blue);
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .btn-primary-custom:hover {
        background: var(--brand-blue-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    .card-custom {
        border: 1px solid var(--card-border);
        border-radius: var(--radius-lg);
        background: #fff;
        box-shadow: var(--shadow-soft);
    }

    .table-custom {
        width: 100%;
        border-collapse: collapse;
    }

    .table-custom th {
        background: #f8fafc;
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: var(--text-muted);
        font-size: 0.875rem;
        border-bottom: 1px solid var(--card-border);
    }

    .table-custom td {
        padding: 1rem;
        border-bottom: 1px solid var(--card-border);
        color: var(--text-dark);
    }

    .table-custom tr:last-child td {
        border-bottom: none;
    }

    .badge-dentist {
        background: var(--brand-blue-light);
        color: var(--brand-blue);
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        font-size: 0.85rem;
        font-weight: 600;
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

    .action-btns {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s ease;
    }

    .btn-edit {
        background: var(--brand-blue-light);
        color: var(--brand-blue);
    }

    .btn-edit:hover {
        background: var(--brand-blue);
        color: #fff;
    }

    .btn-view {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .btn-view:hover {
        background: #4f46e5;
        color: #fff;
    }

    .btn-delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-delete:hover {
        background: #dc2626;
        color: #fff;
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
    <h1 class="page-title">Doctor Schedules</h1>
    <a href="{{ route('admin.schedules.create') }}" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Add Schedule
    </a>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        @if($schedules->isEmpty())
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <p>No schedules found. Create your first schedule.</p>
            </div>
        @else
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Dentist</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Break</th>
                        <th>Slot Duration</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schedules as $schedule)
                        <tr>
                            <td>
                                <span class="badge-dentist">
                                    {{ $schedule->doctor->name }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($schedule->working_date)->format('M d, Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</td>
                            <td>
                                @if($schedule->break_start && $schedule->break_end)
                                    {{ \Carbon\Carbon::parse($schedule->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $schedule->slot_duration }} min</td>
                            <td>
                                @if($schedule->is_active)
                                    <span class="badge-active">Active</span>
                                @else
                                    <span class="badge-inactive">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('admin.schedules.show', $schedule->id) }}" class="btn-action btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule->id) }}" class="btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@if($schedules->hasPages())
    <div class="pagination-custom">
        {{ $schedules->links() }}
    </div>
@endif
@endsection
