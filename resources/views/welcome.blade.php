@extends('layouts.public')

@section('content')
{{-- Hero Section --}}
<section class="hero-section py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            {{-- Hero Content --}}
            <div class="col-lg-6">
                <div class="hero-content pe-lg-4">
                    <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3 rounded-pill">
                        🦷 Quality Dental Care
                    </span>
                    <h1 class="display-5 fw-bold text-primary mb-3 hero-title">
                        Your Smile Deserves the Best Care
                    </h1>
                    <p class="lead text-muted mb-4 hero-description">
                        Experience compassionate dental care with our experienced team. 
                        We provide comprehensive services to keep your smile healthy and beautiful.
                    </p>
                    @guest
                        <div class="d-flex flex-wrap gap-3 hero-buttons">
                            <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 shadow-sm">
                                <i class="bi bi-calendar-check me-2"></i>Book Appointment
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg px-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Login
                            </a>
                        </div>
                    @else
                        @if(Auth::user()->isPatient())
                            <a href="#" class="btn btn-primary btn-lg px-4 shadow-sm disabled">
                                <i class="bi bi-calendar-check me-2"></i>Book Appointment
                            </a>
                        @endif
                    @endguest
                </div>
            </div>

            {{-- Hero Image/Card --}}
            <div class="col-lg-6">
                <div class="hero-card">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="feature-icon-wrapper bg-primary-subtle rounded-circle p-3 me-3">
                                    <i class="bi bi-heart-pulse text-primary fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-1 fw-semibold">Our Services</h5>
                                    <p class="text-muted small mb-0">Comprehensive dental care</p>
                                </div>
                            </div>
                            
                            <div class="services-list">
                                <div class="service-item d-flex align-items-center py-3 border-bottom">
                                    <div class="service-icon me-3">
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <i class="bi bi-check-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">General Dentistry</h6>
                                        <small class="text-muted">Complete oral health care</small>
                                    </div>
                                </div>
                                
                                <div class="service-item d-flex align-items-center py-3 border-bottom">
                                    <div class="service-icon me-3">
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <i class="bi bi-check-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Teeth Whitening</h6>
                                        <small class="text-muted">Professional whitening services</small>
                                    </div>
                                </div>
                                
                                <div class="service-item d-flex align-items-center py-3 border-bottom">
                                    <div class="service-icon me-3">
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <i class="bi bi-check-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Dental Implants</h6>
                                        <small class="text-muted">Permanent tooth replacement</small>
                                    </div>
                                </div>
                                
                                <div class="service-item d-flex align-items-center py-3 border-bottom">
                                    <div class="service-icon me-3">
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <i class="bi bi-check-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Orthodontics</h6>
                                        <small class="text-muted">Teeth alignment & correction</small>
                                    </div>
                                </div>
                                
                                <div class="service-item d-flex align-items-center py-3">
                                    <div class="service-icon me-3">
                                        <span class="badge bg-primary rounded-circle p-2">
                                            <i class="bi bi-check-lg text-white"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">Emergency Care</h6>
                                        <small class="text-muted">24/7 urgent dental care</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Features Section --}}
<section class="features-section py-5 bg-light-subtle" id="services">
    <div class="container">
        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-2 rounded-pill">Why Choose Us</span>
            <h2 class="fw-bold text-primary mb-3">Experience Excellence in Dental Care</h2>
            <p class="text-muted mx-auto" style="max-width: 600px;">
                We combine expertise, technology, and compassionate care to deliver exceptional dental services for you and your family.
            </p>
        </div>

        <div class="row g-4">
            {{-- Feature 1 --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-bg bg-primary-subtle rounded-circle p-4 d-inline-block mb-3">
                            <i class="bi bi-people-fill text-primary fs-3"></i>
                        </div>
                        <h5 class="card-title fw-semibold mb-2">Experienced Team</h5>
                        <p class="card-text text-muted small">
                            Our team of certified dentists brings years of expertise and continuous training to serve you better.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Feature 2 --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-bg bg-primary-subtle rounded-circle p-4 d-inline-block mb-3">
                            <i class="bi bi-gear-fill text-primary fs-3"></i>
                        </div>
                        <h5 class="card-title fw-semibold mb-2">Modern Technology</h5>
                        <p class="card-text text-muted small">
                            We use state-of-the-art equipment and latest techniques for accurate diagnosis and treatment.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Feature 3 --}}
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 feature-card">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon-bg bg-primary-subtle rounded-circle p-4 d-inline-block mb-3">
                            <i class="bi bi-shield-check text-primary fs-3"></i>
                        </div>
                        <h5 class="card-title fw-semibold mb-2">Safe & Comfortable</h5>
                        <p class="card-text text-muted small">
                            Your safety and comfort are our priority. We maintain strict hygiene and safety protocols.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA Section --}}
<section class="cta-section py-5" id="contact">
    <div class="container">
        <div class="card bg-primary border-0 rounded-4 shadow">
            <div class="card-body py-5 px-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="text-white fw-bold mb-2">Ready to Get Started?</h3>
                        <p class="text-white-50 mb-0">
                            Book your appointment today and take the first step towards a healthier, brighter smile.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        @guest
                            <a href="{{ route('register') }}" class="btn btn-light btn-lg text-primary px-4 shadow-sm">
                                <i class="bi bi-calendar-plus me-2"></i>Book Now
                            </a>
                        @else
                            <a href="#" class="btn btn-light btn-lg text-primary px-4 shadow-sm disabled">
                                <i class="bi bi-calendar-plus me-2"></i>Book Now
                            </a>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
