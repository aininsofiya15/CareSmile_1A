<footer class="footer-section bg-{{ theme_footer() }} text-{{ theme_footer_text() }} py-5 mt-auto">
    <div class="container">
        <div class="row g-4">
            {{-- Brand Column --}}
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand mb-3">
                    <img src="{{ asset('CareSmile.png') }}" alt="CareSmile Logo" style="height: 40px;" class="mb-2">
                    <h6 class="fw-bold mb-2">CareSmile Dental Clinic</h6>
                </div>
                <p class="text-muted small mb-0 footer-description">
                    Your trusted partner for comprehensive dental care. We provide quality healthcare services with compassion and expertise.
                </p>
            </div>

            {{-- Quick Links --}}
            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><a href="{{ route('home') }}" class="text-muted text-decoration-none small">Home</a></li>
                    <li class="mb-2"><a href="{{ route('about') }}" class="text-muted text-decoration-none small">About Us</a></li>
                    <li class="mb-2"><a href="#services" class="text-muted text-decoration-none small">Services</a></li>
                    <li class="mb-2"><a href="#contact" class="text-muted text-decoration-none small">Contact</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Our Services</h6>
                <ul class="list-unstyled footer-links">
                    <li class="mb-2"><span class="text-muted small">General Dentistry</span></li>
                    <li class="mb-2"><span class="text-muted small">Teeth Whitening</span></li>
                    <li class="mb-2"><span class="text-muted small">Dental Implants</span></li>
                    <li class="mb-2"><span class="text-muted small">Orthodontics</span></li>
                </ul>
            </div>

            {{-- Contact Info --}}
            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Contact Us</h6>
                <ul class="list-unstyled text-muted small footer-contact">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i>
                        123 Dental Street, Healthcare City
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        (123) 456-7890
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>
                        info@caresmile.com
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-clock me-2"></i>
                        Mon - Fri: 8:00 AM - 6:00 PM
                    </li>
                </ul>
            </div>
        </div>

        {{-- Divider --}}
        <hr class="my-4 border-secondary opacity-25">

        {{-- Bottom Footer --}}
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted small mb-0">
                    &copy; {{ date('Y') }} CareSmile Dental Clinic. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="text-muted small mb-0">
                    Designed with <span class="text-danger">❤</span> for your smile
                </p>
            </div>
        </div>
    </div>
</footer>
