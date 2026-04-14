@extends('layouts.app')

@section('content')
<style>
    /* 1. Main Workspace Wrapper */
    .workspace-wrapper {
        background: #ffffff;
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        border: 1px solid #f1f5f9;
        margin-top: 20px;
    }

    /* 2. Page Header Layout */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0 10px;
    }

    /* 3. Info & Slot Sections */
    .info-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 25px;
        height: 100%;
        border: 1px solid #f1f5f9;
    }

    .section-title {
        font-weight: 800;
        color: #1e293b;
        font-size: 1.1rem;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* 4. Detail Styling */
    .detail-item {
        margin-bottom: 18px;
    }

    .detail-label {
        font-weight: 700;
        color: #94a3b8;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }

    .detail-value {
        color: #1e293b;
        font-weight: 600;
        font-size: 1rem;
    }

    /* 5. Slot Grid Styling */
    .slot-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(85px, 1fr));
        gap: 10px;
    }

    .slot-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 12px 5px;
        border-radius: 12px;
        text-align: center;
        font-weight: 700;
        font-size: 0.9rem;
        color: #1f6fff;
        transition: 0.2s;
    }

    .slot-item:hover {
        border-color: #1f6fff;
        background: #eef5ff;
    }

    .slot-item.unavailable {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fecaca;
    }

    /* 6. Status Badges */
    .badge-status {
        padding: 6px 14px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.7rem;
        text-transform: uppercase;
    }
    .status-active { background: #dcfce7; color: #15803d; }
    .status-inactive { background: #fee2e2; color: #b91c1c; }
</style>

<div class="container py-4">
    {{-- Header --}}
    <div class="page-header">
        <div>
            <h2 class="font-weight-bold text-dark mb-1">Schedule Details</h2>
            <p class="text-muted small mb-0">Review generated time slots for the dentist.</p>
        </div>
        <a href="{{ route('admin.schedules.index') }}" class="btn btn-light px-4 shadow-sm" style="border-radius: 12px; font-weight: 700; border: 1px solid #e2e8f0;">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    {{-- Main Workspace --}}
    <div class="workspace-wrapper">
        <div class="row">
            {{-- Left Side: Information --}}
            <div class="col-lg-5 mb-4 mb-lg-0">
                <div class="info-card">
                    <h5 class="section-title">
                        <i class="fas fa-info-circle text-primary"></i> Schedule Info
                    </h5>
                    
                    <div class="detail-item">
                        <div class="detail-label">Doctor Name</div>
                        <div class="detail-value text-primary">{{ $schedule->doctor->name }}</div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Working Date</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($schedule->working_date)->format('l, d M Y') }}</div>
                    </div>

                    <div class="row">
                        <div class="col-6 detail-item">
                            <div class="detail-label">Working Hours</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</div>
                        </div>
                        <div class="col-6 detail-item">
                            <div class="detail-label">Slot Duration</div>
                            <div class="detail-value">{{ $schedule->slot_duration }} Mins</div>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Break Time</div>
                        <div class="detail-value">
                            @if($schedule->break_start)
                                <span class="text-danger">{{ \Carbon\Carbon::parse($schedule->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->break_end)->format('H:i') }}</span>
                            @else
                                <span class="text-muted">No break scheduled</span>
                            @endif
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-label">Current Status</div>
                        <div>
                            @if($schedule->is_active)
                                <span class="badge-status status-active">Active</span>
                            @else
                                <span class="badge-status status-inactive">Inactive</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: Time Slots --}}
            <div class="col-lg-7">
                <div class="p-2">
                    <h5 class="section-title">
                        <i class="fas fa-calendar-alt text-primary"></i> Generated Slots 
                        <span class="badge badge-primary ml-2" style="border-radius: 8px; font-size: 0.8rem;">{{ $schedule->slots->count() }}</span>
                    </h5>

                    @if($schedule->slots->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-clock fa-3x text-light mb-3"></i>
                            <p class="text-muted">No slots available for this schedule.</p>
                        </div>
                    @else
                        <div class="slot-grid">
                            @foreach($schedule->slots as $slot)
                                <div class="slot-item shadow-sm {{ !$slot->is_available ? 'unavailable' : '' }}">
                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                    @if(!$slot->is_available)
                                        <div style="font-size: 0.6rem; text-transform: uppercase;">Booked</div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-5 pt-4 border-top text-center text-muted small">
            CareSmile Management &bull; Generated via Admin Control Panel
        </div>
    </div>
</div>
@endsection