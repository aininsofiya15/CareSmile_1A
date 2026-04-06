<x-guest-layout>
    <style>
        .split-login-container {
            display: flex;
            background-color: #ffffff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            overflow: hidden;
            min-height: 650px;
        }

        .login-form-side {
            flex: 1;
            padding: 4rem 5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-image-side {
            flex: 1.2;
            background-color: transparent; 
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .login-title { font-weight: 800; color: #14213d; font-size: 2.2rem; margin-bottom: 0.5rem; }
        .login-subtitle { color: #4b5563; font-weight: 600; font-size: 1rem; margin-bottom: 2rem; }

        /* Input Styling */
        .input-group-text { background-color: transparent; border-right: none; color: #9ca3af; border-radius: 8px 0 0 8px; }
        .form-control.with-icon { border-left: none; border-radius: 0 8px 8px 0; padding: 0.75rem; }
        .input-group:focus-within { box-shadow: 0 0 0 3px rgba(31, 111, 255, 0.15); border-radius: 8px; }

        /* Strength Bar */
        .strength-meter { height: 6px; background-color: #e5e7eb; border-radius: 3px; margin: 10px 0; overflow: hidden; display: none; }
        #strength-bar { height: 100%; width: 0%; transition: all 0.3s ease; }
        
        /* Checklist Popup */
        #password-checklist { display: none; background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 15px; margin-top: 10px; }
        .check-item { font-size: 0.8rem; color: #9ca3af; margin-bottom: 4px; display: flex; align-items: center; }
        .check-item i { margin-right: 8px; width: 14px; }
        .check-item.valid { color: #10b981; font-weight: 600; }
        .check-item.valid i { color: #10b981; }

        .btn-login {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border: none; border-radius: 8px; padding: 0.85rem; font-weight: 600;
            color: white; transition: all 0.3s ease; box-shadow: 0 8px 20px -6px rgba(37, 99, 235, 0.6);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 12px 24px -8px rgba(37, 99, 235, 0.8); }
    </style>

    <div class="split-login-container">
        <div class="login-form-side">
            <h1 class="login-title">Patient Sign Up</h1>
            <p class="login-subtitle">Create your CareSmile medical account</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-id-badge"></i></span>
                        <input type="text" class="form-control with-icon" name="name" value="{{ old('name') }}" placeholder="Full Name" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="far fa-envelope"></i></span>
                        <input type="email" class="form-control with-icon" name="email" value="{{ old('email') }}" placeholder="Email Address" required>
                    </div>
                </div>

                {{-- Password with Real-time Validation --}}
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" id="password" name="password" class="form-control with-icon" placeholder="Password" required oninput="validatePassword(this.value)">
                    </div>

                    {{-- Strength Meter --}}
                    <div class="strength-meter" id="meter-container">
                        <div id="strength-bar"></div>
                    </div>

                    {{-- Checklist --}}
                    <div id="password-checklist">
                        <p class="small fw-bold mb-2">Password requirements:</p>
                        <div class="check-item" id="req-length"><i class="fas fa-circle"></i> 8+ characters</div>
                        <div class="check-item" id="req-upper"><i class="fas fa-circle"></i> One uppercase letter</div>
                        <div class="check-item" id="req-number"><i class="fas fa-circle"></i> One number</div>
                        <div class="check-item" id="req-special"><i class="fas fa-circle"></i> One special character</div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                        <input type="password" name="password_confirmation" class="form-control with-icon" placeholder="Confirm Password" required>
                    </div>
                </div>

                <div class="mb-4 form-check text-start">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                    <label class="form-check-label text-muted" for="terms" style="font-size: 0.85rem;">
                        I agree to the <a href="#" class="text-decoration-none fw-bold">Terms & Privacy Policy</a>.
                    </label>
                </div>

                <div class="d-grid gap-2 mb-3">
                    <button type="submit" class="btn btn-login">Register</button>
                </div>
            </form>
        </div>

        <div class="login-image-side">
            <img src="{{ asset('login1.jpg') }}" alt="Register Image" style="max-width: 100%; height: auto;">
        </div>
    </div>

    <script>
        function validatePassword(pass) {
            const checklist = document.getElementById('password-checklist');
            const meter = document.getElementById('meter-container');
            const bar = document.getElementById('strength-bar');
            
            checklist.style.display = 'block';
            meter.style.display = 'block';

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

            // Strength Bar Colors
            const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'];
            bar.style.width = (score / 4) * 100 + '%';
            bar.style.backgroundColor = colors[score - 1] || '#e5e7eb';
        }
    </script>
</x-guest-layout>