@extends('layouts.public')

@section('content')
<div class="bg-light py-5">
    <div class="container">
        <h1 class="mb-4 text-primary">About CareSmile Dental Clinic</h1>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Our Mission</h3>
                        <p class="card-text">
                            At CareSmile Dental Clinic, we are committed to providing exceptional dental care 
                            in a comfortable and welcoming environment. Our team of experienced professionals 
                            uses the latest technology and techniques to ensure the best outcomes for our patients.
                        </p>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h3 class="card-title">Why Choose Us?</h3>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">✓ Experienced team of dental professionals</li>
                            <li class="list-group-item">✓ State-of-the-art facilities and equipment</li>
                            <li class="list-group-item">✓ Comprehensive range of dental services</li>
                            <li class="list-group-item">✓ Personalized treatment plans</li>
                            <li class="list-group-item">✓ Comfortable and modern environment</li>
                        </ul>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Contact Information</h3>
                        <p class="card-text">
                            <strong>Address:</strong> 123 Dental Street, Healthcare City<br>
                            <strong>Phone:</strong> (123) 456-7890<br>
                            <strong>Email:</strong> info@caresmile.com<br>
                            <strong>Hours:</strong> Monday - Friday: 8:00 AM - 6:00 PM
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Quick Links</h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('register') }}" class="btn btn-primary">Register as Patient</a>
                            <a href="{{ route('login') }}" class="btn btn-outline-primary">Login to Your Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
