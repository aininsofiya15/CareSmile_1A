<x-guest-layout>
    <style>
        /* The Wide Card Container - MADE EVEN BIGGER (1200px) */
        .split-login-container {
            display: flex;
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-width: 1200px; /* BIGGER CONTAINER */
            width: 100%;
            margin: 0 auto;
            overflow: hidden;
            min-height: 650px; /* TALLER CONTAINER */
        }

        /* Left Side: The Form */
        .login-form-side {
            flex: 1;
            padding: 5rem 5rem; /* More padding so it breathes */
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Right Side: The Illustration - CLEAN WHITE BACKGROUND */
        .login-image-side {
            flex: 1.2;
            /* Removed the blue gradient, curved borders, and shadows! */
            background-color: transparent; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Typography */
        .login-title {
            font-weight: 800;
            color: #14213d;
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #4b5563;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 2.5rem;
        }

        /* Input Styling with Icons */
        .input-group-text {
            background-color: transparent;
            border-right: none;
            color: #9ca3af;
            border-radius: 8px 0 0 8px;
        }

        .form-control.with-icon,
        .form-select.with-icon {
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 0.75rem 0.75rem 0;
            background-color: transparent;
        }

        .form-control.with-icon:focus,
        .form-select.with-icon:focus {
            box-shadow: none;
            border-color: #dee2e6; 
        }

        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.15);
            border-radius: 8px;
        }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control.with-icon,
        .input-group:focus-within .form-select.with-icon {
            border-color: #1f6fff;
            color: #1f6fff;
        }

        /* Premium Button */
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 8px;
            padding: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.6);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.8);
        }

    </style>

    <div class="split-login-container">
        
        {{-- LEFT SIDE: FORM --}}
        <div class="login-form-side">
            <h1 class="login-title">Create Account</h1>
            <p class="login-subtitle">Join the CareSmile Dental Platform</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Full Name --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-id-badge"></i></span>
                        <input type="text" class="form-control with-icon @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus>
                    </div>
                    @error('name')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Email Address --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                        <input type="email" class="form-control with-icon @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Register As (Role Dropdown) --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                        <select class="form-select with-icon @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="">Select your role</option>
                            <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient</option>
                            <option value="dentist" {{ old('role') == 'dentist' ? 'selected' : '' }}>Dentist</option>
                        </select>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                        Note: Admin accounts must be created by the system administrator.
                    </div>
                    @error('role')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control with-icon @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Password" required>
                    </div>
                    
                    {{-- ✅ SQA ADDITION: Password Rules Hint --}}
                    <div class="text-muted mt-1" style="font-size: 0.75rem;">
                        <i class="fas fa-shield-alt me-1 text-primary"></i> Must be at least 8 characters, with 1 uppercase, 1 number, and 1 symbol.
                    </div>

                    @error('password')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-3"> {{-- Changed margin bottom slightly to fit the checkbox --}}
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" class="form-control with-icon @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                    </div>
                    @error('password_confirmation')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ✅ SQA ADDITION: Privacy Policy Checkbox --}}
                <div class="mb-4 form-check text-start">
                    <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" id="terms" name="terms" required>
                    <label class="form-check-label text-muted" for="terms" style="font-size: 0.85rem;">
                        I agree to the <a href="#" class="text-decoration-none fw-bold">Terms of Service</a> and <a href="#" class="text-decoration-none fw-bold">Privacy Policy</a> regarding my medical data.
                    </label>
                    @error('terms')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Register Button --}}
                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-login text-white">Register</button>
                </div>
                
                {{-- Login Link --}}
                <div class="text-center mt-2">
                    <p class="text-muted" style="font-size: 0.85rem;">
                        Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login</a>
                    </p>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE: ILLUSTRATION --}}
        <div class="login-image-side">
            {{-- Added the floating animation and made max-width 100% --}}
            <img src="{{ asset('login1.jpg') }}" alt="Register Image" style="max-width: 100%; height: auto;">
        </div>

    </div>
</x-guest-layout>