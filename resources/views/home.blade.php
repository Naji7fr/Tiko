@extends('layouts.app')

@section('title', 'Welkom bij BNG')

@section('content')
<!-- Hero Section - Booking.com style -->
<section class="hero-section hero-booking">
    <div class="container">
        <h1 class="hero-booking-title">Vind uw volgende verblijf</h1>
        <p class="hero-booking-sub">Zoek aanbiedingen voor hotels, woningen en veel meer...</p>

        <!-- Search widget - Booking.com style -->
        <div class="search-widget-booking card shadow border-0 rounded-3 overflow-hidden" id="search">
            <div class="card-body p-0">
                <ul class="nav nav-tabs nav-fill search-tabs" id="searchTypeTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-stays" data-bs-toggle="tab" data-bs-target="#panel-stays" type="button" role="tab">
                            <i class="fas fa-bed me-2"></i>Verblijf
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-flights" data-bs-toggle="tab" data-bs-target="#panel-flights" type="button" role="tab">
                            <i class="fas fa-plane me-2"></i>Vluchten
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-flight-hotel" data-bs-toggle="tab" data-bs-target="#panel-flight-hotel" type="button" role="tab">
                            <i class="fas fa-plane-arrival me-2"></i><i class="fas fa-bed ms-1 me-2"></i>Vlucht + Hotel
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-car" data-bs-toggle="tab" data-bs-target="#panel-car" type="button" role="tab">
                            <i class="fas fa-car me-2"></i>Auto huren
                        </button>
                    </li>
                </ul>

                <div class="tab-content search-tab-content bg-white">
                    <!-- Stays -->
                    <div class="tab-pane fade show active p-4" id="panel-stays" role="tabpanel">
                        <form action="{{ route('search') }}" method="GET" class="search-form-booking">
                            <input type="hidden" name="type" value="stays">
                            <div class="search-row">
                                <div class="search-field">
                                    <label class="search-field-label"><i class="fas fa-bed me-2"></i>Waar gaat u naartoe?</label>
                                    <input type="text" name="q" class="form-control border-0" placeholder="Bestemming, stad of accommodatie" value="{{ request('q') }}" required>
                                </div>
                                <div class="search-field search-field-dates">
                                    <label class="search-field-label"><i class="fas fa-calendar-alt me-2"></i>Check-in — Check-out</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="check_in" class="form-control border-0" value="{{ request('check_in') }}">
                                        <span class="align-self-center text-muted">—</span>
                                        <input type="date" name="check_out" class="form-control border-0" value="{{ request('check_out') }}">
                                    </div>
                                </div>
                                <div class="search-field search-field-guests">
                                    <label class="search-field-label"><i class="fas fa-user me-2"></i>Gasten en kamers</label>
                                    <div class="dropdown">
                                        <button class="form-control border-0 text-start dropdown-toggle bg-white" type="button" id="guestsDropdown" data-bs-toggle="dropdown">
                                            <span id="guestsSummary">2 volwassenen · 0 kinderen · 1 kamer</span>
                                        </button>
                                        <input type="hidden" name="adults" id="inputAdults" value="{{ request('adults', 2) }}">
                                        <input type="hidden" name="children" id="inputChildren" value="{{ request('children', 0) }}">
                                        <input type="hidden" name="rooms" id="inputRooms" value="{{ request('rooms', 1) }}">
                                        <ul class="dropdown-menu dropdown-menu-end p-3 shadow" style="min-width: 280px;">
                                            <li class="d-flex justify-content-between align-items-center mb-3">
                                                <span>Volwassenen</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary" id="adultsMinus">−</button>
                                                    <span class="btn btn-outline-secondary disabled" id="adultsCount">2</span>
                                                    <button type="button" class="btn btn-outline-secondary" id="adultsPlus">+</button>
                                                </div>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center mb-3">
                                                <span>Kinderen</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary" id="childrenMinus">−</button>
                                                    <span class="btn btn-outline-secondary disabled" id="childrenCount">0</span>
                                                    <button type="button" class="btn btn-outline-secondary" id="childrenPlus">+</button>
                                                </div>
                                            </li>
                                            <li class="d-flex justify-content-between align-items-center">
                                                <span>Kamers</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-secondary" id="roomsMinus">−</button>
                                                    <span class="btn btn-outline-secondary disabled" id="roomsCount">1</span>
                                                    <button type="button" class="btn btn-outline-secondary" id="roomsPlus">+</button>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="search-field search-field-btn">
                                    <button type="submit" class="btn btn-primary btn-search-booking">
                                        <i class="fas fa-search me-2"></i>Zoeken
                                    </button>
                                </div>
                            </div>
                            <div class="form-check mt-3">
                                <input type="checkbox" class="form-check-input" name="add_flights" id="addFlights" value="1" {{ request('add_flights') ? 'checked' : '' }}>
                                <label class="form-check-label" for="addFlights">Voeg vluchten toe aan mijn zoekopdracht</label>
                            </div>
                        </form>
                    </div>

                    <!-- Flights -->
                    <div class="tab-pane fade p-4" id="panel-flights" role="tabpanel">
                        <form action="{{ route('search') }}" method="GET" class="search-form-booking">
                            <input type="hidden" name="type" value="flights">
                            <div class="search-row">
                                <div class="search-field">
                                    <label class="search-field-label"><i class="fas fa-plane-departure me-2"></i>Van</label>
                                    <input type="text" name="from" class="form-control border-0" placeholder="Vertrekluchthaven of stad" value="{{ request('from') }}">
                                </div>
                                <div class="search-field">
                                    <label class="search-field-label"><i class="fas fa-plane-arrival me-2"></i>Naar</label>
                                    <input type="text" name="to" class="form-control border-0" placeholder="Aankomstluchthaven of stad" value="{{ request('to') }}">
                                </div>
                                <div class="search-field search-field-dates">
                                    <label class="search-field-label"><i class="fas fa-calendar-alt me-2"></i>Datum</label>
                                    <input type="date" name="flight_date" class="form-control border-0" value="{{ request('flight_date') }}">
                                </div>
                                <div class="search-field search-field-btn">
                                    <button type="submit" class="btn btn-primary btn-search-booking">
                                        <i class="fas fa-search me-2"></i>Zoeken
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Flight + Hotel -->
                    <div class="tab-pane fade p-4" id="panel-flight-hotel" role="tabpanel">
                        <form action="{{ route('search') }}" method="GET" class="search-form-booking">
                            <input type="hidden" name="type" value="flight_hotel">
                            <div class="search-row">
                                <div class="search-field">
                                    <label class="search-field-label"><i class="fas fa-bed me-2"></i>Waar gaat u naartoe?</label>
                                    <input type="text" name="q" class="form-control border-0" placeholder="Bestemming" value="{{ request('q') }}">
                                </div>
                                <div class="search-field search-field-dates">
                                    <label class="search-field-label"><i class="fas fa-calendar-alt me-2"></i>Check-in — Check-out</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="check_in" class="form-control border-0" value="{{ request('check_in') }}">
                                        <span class="align-self-center text-muted">—</span>
                                        <input type="date" name="check_out" class="form-control border-0" value="{{ request('check_out') }}">
                                    </div>
                                </div>
                                <div class="search-field search-field-btn">
                                    <button type="submit" class="btn btn-primary btn-search-booking">
                                        <i class="fas fa-search me-2"></i>Zoeken
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Car rental -->
                    <div class="tab-pane fade p-4" id="panel-car" role="tabpanel">
                        <form action="{{ route('search') }}" method="GET" class="search-form-booking">
                            <input type="hidden" name="type" value="car">
                            <div class="search-row">
                                <div class="search-field">
                                    <label class="search-field-label"><i class="fas fa-map-marker-alt me-2"></i>Waar?</label>
                                    <input type="text" name="q" class="form-control border-0" placeholder="Ophaallocatie" value="{{ request('q') }}">
                                </div>
                                <div class="search-field search-field-dates">
                                    <label class="search-field-label"><i class="fas fa-calendar-alt me-2"></i>Ophalen — Inleveren</label>
                                    <div class="d-flex gap-2">
                                        <input type="date" name="pickup_date" class="form-control border-0" value="{{ request('pickup_date') }}">
                                        <span class="align-self-center text-muted">—</span>
                                        <input type="date" name="return_date" class="form-control border-0" value="{{ request('return_date') }}">
                                    </div>
                                </div>
                                <div class="search-field search-field-btn">
                                    <button type="submit" class="btn btn-primary btn-search-booking">
                                        <i class="fas fa-search me-2"></i>Zoeken
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <a href="#about" class="btn-hero mt-4">Meer Over Ons</a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&h=600&fit=crop" alt="Accommodatie" class="about-image">
            </div>
            <div class="col-lg-6">
                <h2>Over BNG</h2>
                <p>BNG is uw boekingsplatform voor accommodaties wereldwijd. Net als Booking.com helpen we reizigers de juiste plek te vinden, van hotels en appartementen tot vakantiewoningen.</p>
                <p>Met duizenden accommodaties, eerlijke reviews en een eenvoudig boekingsproces maakt BNG reizen eenvoudiger en betaalbaarder.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2>Waarom BNG?</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=400&h=300&fit=crop" alt="Hotels" class="service-image">
                    <div class="p-3">
                        <h5>Hotels & Resorts</h5>
                        <p>Van budget tot luxe. Vind het juiste hotel voor elke reis en elk budget.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop" alt="Appartementen" class="service-image">
                    <div class="p-3">
                        <h5>Appartementen</h5>
                        <p>Meer ruimte en privacy. Ideaal voor gezinnen en langere verblijven.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=400&h=300&fit=crop" alt="Vakantiewoningen" class="service-image">
                    <div class="p-3">
                        <h5>Vakantiewoningen</h5>
                        <p>Unieke plekken met karakter. Van villa's tot huisjes aan het strand.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <h2>Klaar om te Boeken?</h2>
        <p>Registreer of log in om accommodaties te vergelijken en direct te reserveren.</p>
        <a href="{{ route('register') }}" class="btn btn-light btn-lg rounded-pill px-4 py-3">
            <i class="fas fa-user-plus me-2"></i>Registreren
        </a>
    </div>
</section>

<!-- Management Access Section -->
<section class="management-section">
    <div class="container">
        <h2>Manager Operations</h2>
        <p>Voor beheerders: toegang tot overzichten en beheer.</p>
        <div class="management-grid">
            <div class="management-item">
                <i class="fas fa-users"></i><br>
                <a href="{{ route('medewerkers.index') }}">Overzicht medewerker</a>
            </div>
            @if(Route::has('klanten.index'))
            <div class="management-item">
                <i class="fas fa-user-friends"></i><br>
                <a href="{{ route('klanten.index') }}">Overzicht klant</a>
            </div>
            @endif
            <div class="management-item">
                <i class="fas fa-user-shield"></i><br>
                <a href="{{ route('accounts.index') }}">Overzicht accounts</a>
            </div>
            <div class="management-item">
                <i class="fas fa-envelope"></i><br>
                <a href="{{ route('berichten.index') }}">Berichten</a>
            </div>
            <div class="management-item">
                <i class="fas fa-file-invoice-dollar"></i><br>
                <a href="{{ route('facturen.index') }}">Facturen</a>
            </div>
            <div class="management-item">
                <i class="fas fa-chart-bar"></i><br>
                <a href="{{ route('statistieken.index') }}">Statistieken</a>
            </div>
            <div class="management-item">
                <i class="fas fa-calendar-check"></i><br>
                <a href="{{ route('afspraken.index') }}">Afspraken</a>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var adults = parseInt(document.getElementById('inputAdults').value) || 2;
    var children = parseInt(document.getElementById('inputChildren').value) || 0;
    var rooms = parseInt(document.getElementById('inputRooms').value) || 1;
    adults = Math.min(30, Math.max(1, adults));
    children = Math.min(10, Math.max(0, children));
    rooms = Math.min(10, Math.max(1, rooms));

    function updateGuests() {
        document.getElementById('inputAdults').value = adults;
        document.getElementById('inputChildren').value = children;
        document.getElementById('inputRooms').value = rooms;
        document.getElementById('adultsCount').textContent = adults;
        document.getElementById('childrenCount').textContent = children;
        document.getElementById('roomsCount').textContent = rooms;
        document.getElementById('guestsSummary').textContent = adults + ' volwassenen · ' + children + ' kinderen · ' + rooms + ' kamer' + (rooms > 1 ? 's' : '');
    }
    updateGuests();

    document.getElementById('adultsPlus').addEventListener('click', function() { adults = Math.min(30, adults + 1); updateGuests(); });
    document.getElementById('adultsMinus').addEventListener('click', function() { adults = Math.max(1, adults - 1); updateGuests(); });
    document.getElementById('childrenPlus').addEventListener('click', function() { children = Math.min(10, children + 1); updateGuests(); });
    document.getElementById('childrenMinus').addEventListener('click', function() { children = Math.max(0, children - 1); updateGuests(); });
    document.getElementById('roomsPlus').addEventListener('click', function() { rooms = Math.min(10, rooms + 1); updateGuests(); });
    document.getElementById('roomsMinus').addEventListener('click', function() { rooms = Math.max(1, rooms - 1); updateGuests(); });
});
</script>
@endpush
@endsection
