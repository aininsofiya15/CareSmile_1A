@extends('layouts.app')

@section('content')
<style>
    /* Using existing theme colors from dashboard */
    .services-hero {
        background: linear-gradient(135deg, #4361ee 0%, #3a56d4 100%);
        border-radius: 24px;
        color: white;
        padding: 2rem 3rem;
        box-shadow: 0 20px 40px rgba(67, 97, 238, 0.2);
        margin-bottom: 2.5rem;
    }

    .patient-badge {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.4rem 1rem;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
    }

    .service-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        transition: transform 0.3s ease;
        height: 100%;
        padding: 1.5rem;
    }

    .service-card:hover {
        transform: translateY(-5px);
    }

    .icon-box-blue {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        background-color: #eef2ff;
        color: #4361ee;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin: 0 auto 1.2rem auto;
    }

    .duration-badge {
        background: #eef2ff;
        color: #4361ee;
        padding: 0.35rem 1rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-block;
    }

    .service-price {
        font-size: 1.8rem;
        font-weight: 800;
        color: #4361ee;
    }

    .service-price small {
        font-size: 0.9rem;
        font-weight: 500;
        color: #6c757d;
    }

    .service-description {
        color: #6c757d;
        font-size: 0.85rem;
        line-height: 1.5;
        min-height: 60px;
    }

    .btn-book {
        background-color: #4361ee;
        color: white;
        font-weight: 700;
        border: none;
        border-radius: 8px;
        padding: 0.7rem 1rem;
        text-decoration: none;
        transition: all 0.2s;
        display: inline-block;
        text-align: center;
    }

    .btn-book:hover {
        background-color: #3a56d4;
        transform: scale(1.02);
        color: white;
    }

    .btn-outline-light-custom {
        background-color: white;
        color: #4361ee;
        font-weight: 700;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-block;
    }

    .btn-outline-light-custom:hover {
        background-color: #f8fafc;
        transform: scale(1.02);
        color: #3a56d4;
    }

    .filter-section {
        background: white;
        border-radius: 20px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
    }

    .search-input {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 0.6rem 1rem;
    }

    .search-input:focus {
        border-color: #4361ee;
        box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        outline: none;
    }
</style>

<div class="container-fluid py-2">
    {{-- Hero Banner matching dashboard style --}}
    <div class="services-hero d-flex justify-content-between align-items-center flex-wrap gap-4">
        <div>
            <div class="patient-badge">
                <i class="fas fa-tooth"></i> Our Dental Services
            </div>
            <h1 class="fw-bold mb-2" style="font-size: 2.5rem;">Choose Your Treatment</h1>
            <p class="mb-0 text-white" style="max-width: 600px; opacity: 0.9;">
                Browse through our comprehensive range of dental services and book an appointment that suits your needs.
            </p>
        </div>
        <div>
            {{-- FIXED: Changed from patient.appointments.index to patient.appointments --}}
            <a href="{{ route('patient.appointments') }}" class="btn-outline-light-custom shadow-sm">
                <i class="fas fa-calendar-alt me-2"></i>My Appointments
            </a>
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="filter-section">
        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <div class="position-relative">
                    <i class="fas fa-search position-absolute" style="left: 15px; top: 50%; transform: translateY(-50%); color: #9ca3af;"></i>
                    <input type="text" id="searchService" class="form-control search-input" 
                           placeholder="Search for services..." style="padding-left: 40px;">
                </div>
            </div>
            <div class="col-md-3">
                <select id="sortPrice" class="form-select search-input">
                    <option value="default">Sort by: Default</option>
                    <option value="price_asc">Price: Low to High</option>
                    <option value="price_desc">Price: High to Low</option>
                    <option value="name_asc">Name: A to Z</option>
                </select>
            </div>
            <div class="col-md-3 text-md-end">
                <span class="text-muted" id="serviceCount">{{ $services->count() }} services available</span>
            </div>
        </div>
    </div>

    {{-- Services Grid --}}
    <div class="row g-4" id="servicesGrid">
        @forelse($services as $service)
        <div class="col-md-6 col-lg-4 service-item" 
             data-name="{{ strtolower($service->name) }}" 
             data-description="{{ strtolower($service->description) }}"
             data-price="{{ $service->price }}"
             data-name-original="{{ $service->name }}">
            <div class="card service-card">
                <div class="text-center mb-3">
                    <div class="icon-box-blue">
                        <i class="fas fa-tooth"></i>
                    </div>
                </div>

                <h5 class="fw-bold text-center mb-2">{{ $service->name }}</h5>

                <div class="text-center mb-3">
                    <span class="duration-badge">
                        <i class="far fa-clock me-1"></i>{{ $service->duration_minutes }} minutes
                    </span>
                </div>

                <p class="service-description text-center mb-3">
                    {{ Str::limit($service->description, 100) }}
                </p>

                <div class="text-center mb-4">
                    <span class="service-price">
                        RM {{ number_format($service->price, 2) }}
                    </span>
                    <small>/ session</small>
                </div>

                <a href="{{ route('patient.appointments.create', ['service_id' => $service->id]) }}" 
                   class="btn-book w-100">
                    <i class="fas fa-calendar-check me-2"></i>Book Appointment
                </a>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-tooth fa-3x text-muted mb-3 opacity-25"></i>
                <h5 class="text-muted">No services available at the moment.</h5>
                <p class="text-muted small">Please check back later for our dental services.</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($services->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $services->links() }}
    </div>
    @endif
</div>

<script>
    const searchInput = document.getElementById('searchService');
    const sortSelect = document.getElementById('sortPrice');
    const servicesGrid = document.getElementById('servicesGrid');
    const serviceCountSpan = document.getElementById('serviceCount');
    let allServices = [];

    function refreshServiceList() {
        allServices = Array.from(document.querySelectorAll('.service-item'));
        updateServiceCount();
    }

    function updateServiceCount() {
        const visibleServices = document.querySelectorAll('.service-item:not([style*="display: none"])');
        serviceCountSpan.textContent = visibleServices.length + ' services available';
    }

    function filterServices() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        
        allServices.forEach(service => {
            const name = service.getAttribute('data-name');
            const description = service.getAttribute('data-description');
            
            if (searchTerm === '' || name.includes(searchTerm) || description.includes(searchTerm)) {
                service.style.display = '';
            } else {
                service.style.display = 'none';
            }
        });
        
        sortServices();
        updateServiceCount();
    }

    function sortServices() {
        const sortBy = sortSelect.value;
        const visibleServices = allServices.filter(service => service.style.display !== 'none');
        
        visibleServices.sort((a, b) => {
            switch(sortBy) {
                case 'price_asc':
                    return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                case 'price_desc':
                    return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                case 'name_asc':
                    return a.getAttribute('data-name-original').localeCompare(b.getAttribute('data-name-original'));
                default:
                    return 0;
            }
        });
        
        visibleServices.forEach(service => {
            servicesGrid.appendChild(service);
        });
        
        allServices.forEach(service => {
            if (service.style.display === 'none') {
                servicesGrid.appendChild(service);
            }
        });
    }

    searchInput.addEventListener('input', filterServices);
    sortSelect.addEventListener('change', () => {
        sortServices();
    });

    setTimeout(() => {
        refreshServiceList();
    }, 100);
</script>
@endsection