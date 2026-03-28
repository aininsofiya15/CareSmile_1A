<x-guest-layout>
    <style>
        /* The Wide Card Container */
        .split-login-container {
            display: flex;
            background-color: #ffffff;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            max-width: 950px;
            width: 100%;
            margin: 0 auto;
            overflow: hidden;
            min-height: 600px;
        }

        /* Left Side: The Form */
        .login-form-side {
            flex: 1;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Right Side: The Illustration */
        .login-image-side {
            flex: 1.2;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 60px 0 0 60px;
            margin-left: -20px;
            box-shadow: -10px 0 20px rgba(0,0,0,0.03);
            background: linear-gradient(135deg, #eef5ff 0%, #d6e8ff 100%);
        }

        /* Typography */
        .login-title {
            font-weight: 800;
            color: #14213d;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #4b5563;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 2rem;
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

        /* Button */
        .btn-login {
            background-color: #4361ee;
            border: none;
            border-radius: 8px;
            padding: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background-color: #3a56d4;
            transform: translateY(-2px);
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
                    @error('password')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Confirm Password --}}
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" class="form-control with-icon @error('password_confirmation') is-invalid @enderror" 
                               id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                    </div>
                    @error('password_confirmation')
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
            {{-- Reusing the login image to keep the theme consistent! --}}
            <img src="{{ asset('login.png') }}" alt="Register Image" style="max-width: 80%; height: auto;">
        </div>

    </div>
</x-guest-layout>