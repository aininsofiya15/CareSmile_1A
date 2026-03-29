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

        /* Floating Animation */
        @keyframes float {
            0% { transform: translateY(0px) scale(1.15); }
            50% { transform: translateY(-12px) scale(1.15); }
            100% { transform: translateY(0px) scale(1.15); }
        }

        .floating-illustration {
            animation: float 6s ease-in-out infinite;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.08));
        }
    </style>

    <div class="split-login-container">
        
        {{-- LEFT SIDE: FORM --}}
        <div class="login-form-side">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to CareSmile Dental Platform</p>

            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Username / Email with Icon --}}
                <div class="mb-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-user"></i></span>
                        <input type="email" class="form-control with-icon @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" placeholder="Username or email" required autofocus>
                    </div>
                    @error('email')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Password with Icon --}}
                <div class="mb-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control with-icon @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Password" required>
                    </div>
                    @error('password')
                        <div class="text-danger small mt-1 fw-semibold">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Forgot Password Link --}}
                <div class="text-end mb-4">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-decoration-none" style="font-size: 0.85rem; font-weight: 600;">Forgot?</a>
                    @endif
                </div>

                {{-- Remember me (Hidden, but active so login works perfectly) --}}
                <input type="checkbox" name="remember" checked hidden>

                {{-- Login Button --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-login text-white">Log In</button>
                </div>
                
                {{-- Register Link --}}
                <div class="text-center mt-4">
                    <p class="text-muted" style="font-size: 0.85rem;">
                        New to CareSmile? <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Sign up here</a>
                    </p>
                </div>
            </form>
        </div>

        {{-- RIGHT SIDE: ILLUSTRATION --}}
        <div class="login-image-side">
            <img src="{{ asset('login1.jpg') }}" alt="Login Image" style="max-width: 100%; height: auto;">
        </div>

    </div>
</x-guest-layout>