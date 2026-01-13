@extends('layouts.app')

@section('title', 'Welkom bij No Gaffie Clinic')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <h1>Uw Betrouwbare Tandartspraktijk</h1>
        <p>Moderne mondzorg met persoonlijke aandacht. Professionele behandelingen in een ontspannen sfeer, waar uw comfort voorop staat.</p>
        <a href="#about" class="btn-hero">Meer Over Ons</a>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <img src="https://images.unsplash.com/photo-1606811971618-4486d14f3f99?w=800&h=600&fit=crop" alt="Moderne tandartspraktijk" class="about-image">
            </div>
            <div class="col-lg-6">
                <h2>Over No Gaffie Clinic</h2>
                <p>Sinds onze oprichting in 2020 zijn we uitgegroeid tot een vertrouwde partner in mondzorg. Ons team van ervaren tandartsen en specialisten staat klaar om u te helpen met alle aspecten van uw mondgezondheid.</p>
                <p>We geloven in preventieve zorg en persoonlijke aandacht. Elke patiënt is uniek, en daarom bieden we maatwerk oplossingen die perfect aansluiten bij uw behoeften en wensen.</p>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section">
    <div class="container">
        <h2>Onze Diensten</h2>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1609840114035-3c981b782dfe?w=400&h=300&fit=crop" alt="Algemene Tandheelkunde" class="service-image">
                    <div class="p-3">
                        <h5>Algemene Tandheelkunde</h5>
                        <p>Van routinecontroles tot complexe behandelingen. Wij zorgen voor een gezonde en stralende glimlach.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1551601651-2a8555f1a136?w=400&h=300&fit=crop" alt="Preventieve Zorg" class="service-image">
                    <div class="p-3">
                        <h5>Preventieve Zorg</h5>
                        <p>Voorkomen is beter dan genezen. Leer hoe u uw tanden gezond houdt met onze preventieprogramma's.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1629909613654-28e377c37b09?w=400&h=300&fit=crop" alt="Cosmetische Tandheelkunde" class="service-image">
                    <div class="p-3">
                        <h5>Cosmetische Tandheelkunde</h5>
                        <p>Verbeter uw uiterlijk met professionele whitening, facings en andere esthetische behandelingen.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=400&h=300&fit=crop" alt="Kindertandheelkunde" class="service-image">
                    <div class="p-3">
                        <h5>Kindertandheelkunde</h5>
                        <p>Speciaal voor onze jongste patiënten. Maak tandartsbezoek leuk en zorgeloos.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1607613009820-a29f7bb81c04?w=400&h=300&fit=crop" alt="Orthodontie" class="service-image">
                    <div class="p-3">
                        <h5>Orthodontie</h5>
                        <p>Rechte tanden voor een mooi gebit. Van beugels tot moderne aligners.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card service-card">
                    <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=400&h=300&fit=crop" alt="Spoedeisende Hulp" class="service-image">
                    <div class="p-3">
                        <h5>Spoedeisende Hulp</h5>
                        <p>Pijn of een noodgeval? Wij staan 24/7 voor u klaar met snelle hulp.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="cta-section">
    <div class="container">
        <h2>Klaar voor een Gezonde Glimlach?</h2>
        <p>Maak vandaag nog een afspraak en ontdek hoe wij uw mondgezondheid kunnen verbeteren.</p>
        <a href="tel:0123456789" class="btn btn-light btn-lg rounded-pill px-4 py-3">
            <i class="fas fa-phone me-2"></i>Bel Nu: 012-3456789
        </a>
    </div>
</section>

<!-- Management Access Section -->
<section class="management-section">
    <div class="container">
        <h2>Praktijk Management</h2>
        <p>Voor praktijkmedewerkers: snelle toegang tot alle beheerfuncties.</p>
        <div class="management-grid">
            <div class="management-item">
                <i class="fas fa-users"></i><br>
                <a href="{{ route('medewerkers.index') }}">Medewerkers</a>
            </div>
            <div class="management-item">
                <i class="fas fa-user-shield"></i><br>
                <a href="{{ route('accounts.index') }}">Accounts</a>
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
@endsection