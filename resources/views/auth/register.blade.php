<x-guest-layout>
    <style>
        /* The Wide Card Container */
        .split-login-container {
            display: flex;
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            width: 100%;
            margin: 2rem auto;
            overflow: hidden;
            min-height: 700px;
        }

        /* Left Side: The Form */
        .login-form-side {
            flex: 1;
            padding: 4rem 5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #ffffff;
        }

        /* Right Side: The Illustration - BLENDED WHITE */
        .login-image-side {
            flex: 1.2;
            background-color: #ffffff; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* Typography */
        .login-title { font-weight: 800; color: #14213d; font-size: 2.2rem; margin-bottom: 0.5rem; }
        .login-subtitle { color: #4b5563; font-weight: 600; font-size: 1rem; margin-bottom: 2rem; }

        /* Unified Seamless Input Group (Matches Login) */
        .custom-input-group {
            display: flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 12px;
            background-color: #ffffff;
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1rem;
        }

        .custom-input-group:focus-within {
            border-color: #1f6fff;
            box-shadow: 0 0 0 4px rgba(31, 111, 255, 0.1);
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

        /* Strength Bar */
        .strength-meter { height: 6px; background-color: #e5e7eb; border-radius: 3px; margin: 10px 0; overflow: hidden; display: none; }
        #strength-bar { height: 100%; width: 0%; transition: all 0.3s ease; }
        
        /* Checklist Popup */
        #password-checklist { 
            display: none; 
            background: #f9fafb; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px; 
            padding: 15px; 
            margin-top: 5px; 
            margin-bottom: 15px;
        }
        .check-item { font-size: 0.8rem; color: #9ca3af; margin-bottom: 4px; display: flex; align-items: center; }
        .check-item i { margin-right: 8px; width: 14px; }
        .check-item.valid { color: #10b981; font-weight: 600; }

        /* Premium Button */
        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none; border-radius: 12px; padding: 1rem; font-weight: 700;
            color: white; transition: all 0.3s ease; box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.6);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.8); }

        .illustration-static {
            max-width: 100%;
            height: auto;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.02));
        }
    </style>

    <div class="split-login-container">
        {{-- FORM SIDE --}}
        <div class="login-form-side">
            <h1 class="login-title">Patient Sign Up</h1>
            <p class="login-subtitle">Create your CareSmile medical account</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name --}}
                <div class="custom-input-group">
                    <span class="input-icon"><i class="far fa-id-badge"></i></span>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus>
                </div>

                {{-- Email --}}
                <div class="custom-input-group">
                    <span class="input-icon"><i class="far fa-envelope"></i></span>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required>
                </div>

                {{-- Password with Validation and Eye Toggle --}}
                <div class="mb-3">
                    <div class="custom-input-group mb-0">
                        <span class="input-icon"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Password" required oninput="validatePassword(this.value)">
                        <span class="input-icon" id="togglePassword" style="cursor: pointer;">
                            <i class="far fa-eye" id="eyeIcon"></i>
                        </span>
                    </div>

                    <div class="strength-meter" id="meter-container">
                        <div id="strength-bar"></div>
                    </div>

                    <div id="password-checklist">
                        <p class="small fw-bold mb-2">Password requirements:</p>
                        <div class="check-item" id="req-length"><i class="fas fa-circle"></i> 8+ characters</div>
                        <div class="check-item" id="req-upper"><i class="fas fa-circle"></i> One uppercase letter</div>
                        <div class="check-item" id="req-number"><i class="fas fa-circle"></i> One number</div>
                        <div class="check-item" id="req-special"><i class="fas fa-circle"></i> One special character</div>
                    </div>
                </div>

                {{-- Confirm Password --}}
                <div class="custom-input-group">
                    <span class="input-icon"><i class="fas fa-check-circle"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
                </div>

                <div class="mb-4 form-check text-start">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label text-muted" for="terms" style="font-size: 0.85rem;">
                        I agree to the <a href="#" class="text-decoration-none fw-bold" style="color: #2563eb;">Terms & Privacy Policy</a>.
                    </label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-login">Register Account</button>
                </div>

                <div class="text-center mt-3">
                    <p class="text-muted" style="font-size: 0.85rem;">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-decoration-none fw-bold" style="color: #2563eb;">Log in here</a>
                    </p>
                </div>
            </form>
        </div>

        {{-- IMAGE SIDE --}}
        <div class="login-image-side">
            <img src="{{ asset('login1.jpg') }}" alt="Register Image" class="illustration-static">
        </div>
    </div>

    <script>
        // Toggle Password Logic
        const togglePassword = document.querySelector('#togglePassword');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            eyeIcon.classList.toggle('fa-eye');
            eyeIcon.classList.toggle('fa-eye-slash');
        });

        // Validation Logic
        function validatePassword(pass) {
            const checklist = document.getElementById('password-checklist');
            const meter = document.getElementById('meter-container');
            const bar = document.getElementById('strength-bar');
            
            if (pass.length > 0) {
                checklist.style.display = 'block';
                meter.style.display = 'block';
            } else {
                checklist.style.display = 'none';
                meter.style.display = 'none';
            }

            const rules = {
                'req-length': pass.length >= 8,
                'req-upper': /[A-Z]/.test(pass),
                'req-number': /[0-9]/.test(pass),
                'req-special': /[!@#$%^&*(),.?":{}|<>]/.test(pass)
            };

            let score = 0;
            for (const [id, passed] of Object.entries(rules)) {
                const el = document.getElementById(id);
                if (passed) {
                    el.classList.add('valid');
                    el.querySelector('i').className = 'fas fa-check-circle';
                    score++;
                } else {
                    el.classList.remove('valid');
                    el.querySelector('i').className = 'fas fa-circle';
                }
            }

            const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            bar.style.width = (score / 4) * 100 + '%';
            bar.style.backgroundColor = colors[score - 1] || '#e5e7eb';
        }
    </script>
</x-guest-layout>