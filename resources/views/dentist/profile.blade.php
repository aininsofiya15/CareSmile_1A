@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
    <h2 class="fw-bold mb-4" style="color: #111827;">System Administrator</h2>

    <div class="row">
        <div class="col-md-6">
            <div class="card p-4" style="border-radius: 16px; border: 1px solid #e5e7eb;">
                <h4 class="fw-bold">{{ $user->name }}</h4>
                <p class="text-muted mb-2">Admin ID: #ADM-{{ $user->id }}</p>
                <span class="badge bg-dark rounded-pill px-3 py-2 mb-4">System Admin</span>

                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Admin Email</label>
                    <input type="email" class="form-control bg-light" value="{{ $user->email }}" readonly>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection