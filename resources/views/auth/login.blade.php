<x-guest-layout>
    <style>
        /* 1. Main Container */
        .split-login-container {
            display: flex;
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            width: 100%;
            margin: 2rem auto;
            overflow: hidden;
            min-height: 650px;
        }

        /* 2. Left Side: Form */
        .login-form-side {
            flex: 1;
            padding: 5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        /* 3. Right Side: Illustration */
        .login-image-side {
            flex: 1.2;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
        }

        /* 4. Typography */
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

        /* 5. Input Styling & Error States */
        .custom-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .custom-input-group:focus-within {
            border-color: #1f6fff;
            box-shadow: 0 0 0 4px rgba(31, 111, 255, 0.1);
        }

        /* Red border if login fails OR if locked out */
        .is-invalid-border {
            border-color: #ef4444 !important;
            background-color: #fffafb !important;
        }

        .custom-input-group .input-icon {
            padding: 0 1rem;
            color: #9ca3af;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-input-group .form-control {
            border: none !important;
            box-shadow: none !important;
            padding: 0.85rem 0.5rem;
            background: transparent !important;
            font-size: 1rem;
            flex: 1;
        }

        /* 6. Premium Error Alert Box (Updated for Lockout) */
        .error-alert {
            background-color: #fef2f2;
            border-left: 4px solid #ef4444;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: translateY(-10px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .error-message {
            color: #b91c1c;
            font-size: 0.88rem;
            font-weight: 700;
            margin: 0;
        }

        /* 7. Buttons */
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-weight: 700;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.5);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.7);
        }

        .illustration-static {
            max-width: 100%; 
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.02));
        }
    </style>

    <div class="split-login-container">
        
        {{-- LEFT SIDE: FORM --}}
        <div class="login-form-side">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to CareSmile Dental Platform</p>

            {{-- Error Message Display (Handles both Wrong Credentials and Lockout) --}}
            @if ($errors->any())
                <div class="error-alert">
                    @if ($errors->has('throttle'))
                        {{-- Locked out icon --}}
                        <i class="fas fa-user-shield" style="color: #ef4444; font-size: 1.2rem;"></i>
                    @else
                        {{-- Standard warning icon --}}
                        <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.2rem;"></i>
                    @endif
                    
                    <div>
                        <p class="error-message">
                            {{ $errors->first() }}
                        </p>
                        
                    </div>
                </div>
            @endif

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; font-weight: 600;">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email Input --}}
                <div class="custom-input-group {{ $errors->any() ? 'is-invalid-border' : '' }}">
                    <span class="input-icon"><i class="far fa-user"></i></span>
                    <input type="email" class="form-control" 
                           id="email" name="email" value="{{ old('email') }}" 
                           placeholder="Username or email" required autofocus>
                </div>

                {{-- Password Input with Toggle --}}
                <div class="custom-input-group mb-2 {{ $errors->any() ? 'is-invalid-border' : '' }}">
                    <span class="input-icon"><i class="fas fa-lock"></i></span>
                    <input type="password" class="form-control" 
                           id="password" name="password" placeholder="Password" required>
                    <span class="input-icon" id="togglePassword" style="cursor: pointer;">
                        <i class="far fa-eye" id="eyeIcon"></i>
                    </span>
                </div>

                <div class="text-end mb-4">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none fw-bold" style="font-size: 0.85rem; color: #2563eb;">Forgot Password?</a>
                    @endif
                </div>

                <input type="checkbox" name="remember" checked hidden>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-login">Log In</button>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-muted" style="font-size: 0.85rem;">
                        New to CareSmile? <a href="{{ route('register') }}" class="text-decoration-none fw-bold" style="color: #2563eb;">Sign up here</a>
                    </p>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE: ILLUSTRATION --}}
        <div class="login-image-side">
            <img src="{{ asset('login1.jpg') }}" alt="Dental Illustration" class="illustration-static">
        </div>

    </div>

    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });
    </script>
</x-guest-layout>