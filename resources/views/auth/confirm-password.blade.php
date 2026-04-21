<x-guest-layout>
    <style>
        /* CareSmile Centered Security Card Theme */
        .security-card {
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-width: 500px; 
            width: 100%;
            margin: 4rem auto; 
            padding: 3rem;
        }

        .auth-title {
            font-weight: 800;
            color: #14213d;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        /* Reusing your Input Styling */
        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: #9ca3af;
            border-radius: 8px 0 0 8px;
        }

        .form-control.with-icon {
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 0.75rem 0.75rem 0;
            background-color: transparent;
        }

        .form-control.with-icon:focus {
            box-shadow: none;
            border-color: #dee2e6; 
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.15);
            border-radius: 8px;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control.with-icon {
            border-color: #1f6fff;
            color: #1f6fff;
        }

        /* Premium Button */
        .btn-security {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.6);
            width: 100%;
            color: white;
        }

        .btn-security:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.8);
            color: white;
        }
    </style>

    <div class="security-card">
        
        <div class="text-center mb-4">
            <div class="mb-3">
                <i class="fas fa-shield-check text-primary" style="font-size: 3.5rem; color: #2563eb;"></i>
            </div>
            <h1 class="auth-title">Security Check</h1>
            <p class="text-muted" style="font-size: 0.95rem;">
                This is a secure area of the CareSmile platform. Please confirm your password to continue.
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            {{-- Password Input --}}
            <div class="mb-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control with-icon @error('password') is-invalid @enderror" 
                           id="password" name="password" placeholder="Enter your password" required autocomplete="current-password" autofocus>
                </div>
                @error('password')
                    <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                @enderror
            </div>

            {{-- Confirm Button --}}
            <div class="d-grid gap-2 mt-2">
                <button type="submit" class="btn btn-security">
                    <i class="fas fa-check-circle me-2"></i> Confirm Identity
                </button>
            </div>
        </form>
    </div>
</x-guest-layout>