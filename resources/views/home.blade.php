@extends('layouts.app')

@section('title', 'Tiko Barbershop — Sharp Cuts & Clean Fades')

@section('content')
<div class="home-page">

    {{-- Hero --}}
    <section class="barber-hero">
        <div class="barber-hero__overlay"></div>
        <div class="container barber-hero__content">
            <span class="barber-hero__badge"><i class="fas fa-scissors me-2"></i>Amsterdam · Sinds 2018</span>
            <h1 class="barber-hero__title">TIKO<br><span>Barbershop</span></h1>
            <p class="barber-hero__tagline">Sharp cuts. Clean fades. Real craft.</p>
            <div class="barber-hero__actions">
                <a href="#diensten" class="btn btn-gold btn-lg">
                    <i class="fas fa-cut me-2"></i>Onze diensten
                </a>
                <a href="#contact" class="btn btn-outline-gold btn-lg">
                    <i class="fas fa-phone me-2"></i>Bel nu
                </a>
            </div>
            <div class="barber-hero__stats">
                <div class="barber-stat">
                    <strong>2.500+</strong>
                    <span>Tevreden klanten</span>
                </div>
                <div class="barber-stat">
                    <strong>4.9</strong>
                    <span>Gemiddelde score</span>
                </div>
                <div class="barber-stat">
                    <strong>6</strong>
                    <span>Top kappers</span>
                </div>
            </div>
        </div>
        <a href="#diensten" class="barber-hero__scroll" aria-label="Scroll naar diensten">
            <i class="fas fa-chevron-down"></i>
        </a>
    </section>

    {{-- Diensten --}}
    <section id="diensten" class="barber-section barber-services">
        <div class="container">
            <div class="barber-section__header text-center">
                <span class="barber-section__label">Wat wij doen</span>
                <h2>Onze diensten</h2>
                <p>Van klassieke fades tot baardverzorging en kleur — alles onder één dak.</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="barber-service-card">
                        <div class="barber-service-card__image">
                            <img src="https://images.unsplash.com/photo-1622286342621-4bd786c2447c?w=600&h=400&fit=crop" alt="Fade knippen">
                            <span class="barber-service-card__price">vanaf €25</span>
                        </div>
                        <div class="barber-service-card__body">
                            <div class="barber-service-card__icon"><i class="fas fa-layer-group"></i></div>
                            <h3>Fade</h3>
                            <p>Skin fades, low fades en tapers — strak afgewerkt tot in de details.</p>
                            <ul class="barber-service-card__list">
                                <li><i class="fas fa-check"></i> Low / Mid / High fade</li>
                                <li><i class="fas fa-check"></i> Lineup & shape-up</li>
                                <li><i class="fas fa-check"></i> ± 30 minuten</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="barber-service-card barber-service-card--featured">
                        <div class="barber-service-card__badge">Populair</div>
                        <div class="barber-service-card__image">
                            <img src="https://images.unsplash.com/photo-1599351431202-1e0f0137899a?w=600&h=400&fit=crop" alt="Baard trimmen">
                            <span class="barber-service-card__price">vanaf €15</span>
                        </div>
                        <div class="barber-service-card__body">
                            <div class="barber-service-card__icon"><i class="fas fa-user-tie"></i></div>
                            <h3>Baard</h3>
                            <p>Trimmen, shapen en verzorgen — voor een verzorgde look die past bij je gezicht.</p>
                            <ul class="barber-service-card__list">
                                <li><i class="fas fa-check"></i> Baard trim & contour</li>
                                <li><i class="fas fa-check"></i> Hot towel behandeling</li>
                                <li><i class="fas fa-check"></i> ± 20 minuten</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="barber-service-card">
                        <div class="barber-service-card__image">
                            <img src="https://images.unsplash.com/photo-1562320880-bbe3b042977b?w=600&h=400&fit=crop" alt="Haarkleuring">
                            <span class="barber-service-card__price">op aanvraag</span>
                        </div>
                        <div class="barber-service-card__body">
                            <div class="barber-service-card__icon"><i class="fas fa-palette"></i></div>
                            <h3>Kleuren</h3>
                            <p>Subtiele highlights, grijs blending of een volledige kleurtransformatie.</p>
                            <ul class="barber-service-card__list">
                                <li><i class="fas fa-check"></i> Grey blending</li>
                                <li><i class="fas fa-check"></i> Highlights & lowlights</li>
                                <li><i class="fas fa-check"></i> Premium kleurproducten</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Over ons --}}
    <section id="over-ons" class="barber-section barber-about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="barber-about__images">
                        <img src="https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=700&h=500&fit=crop" alt="Barber aan het werk" class="barber-about__img-main">
                        <img src="https://images.unsplash.com/photo-1585747860715-2b67befef277?w=400&h=300&fit=crop" alt="Barbershop interieur" class="barber-about__img-accent">
                    </div>
                </div>
                <div class="col-lg-6">
                    <span class="barber-section__label">Over ons</span>
                    <h2>Meer dan alleen een knipbeurt</h2>
                    <p class="barber-about__lead">Bij Tiko draait alles om vakmanschap, sfeer en aandacht. Geen haast, geen compromissen — alleen topresultaten.</p>
                    <p>Onze shop in Amsterdam is een plek waar je even tot rust komt. Goede muziek, koffie erbij, en kappers die weten wat ze doen. Of je nu wekelijks langskomt of voor het eerst binnenstapt — je voelt je meteen thuis.</p>
                    <div class="barber-about__features">
                        <div class="barber-feature">
                            <i class="fas fa-award"></i>
                            <div>
                                <strong>Ervaren team</strong>
                                <span>Gespecialiseerde kappers per discipline</span>
                            </div>
                        </div>
                        <div class="barber-feature">
                            <i class="fas fa-spray-can-sparkles"></i>
                            <div>
                                <strong>Premium producten</strong>
                                <span>Alleen A-merken op de werkvloer</span>
                            </div>
                        </div>
                        <div class="barber-feature">
                            <i class="fas fa-walkie-talkie"></i>
                            <div>
                                <strong>Walk-ins welkom</strong>
                                <span>Geen afspraak? Kom gerust langs</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Galerij --}}
    <section class="barber-section barber-gallery">
        <div class="container">
            <div class="barber-section__header text-center">
                <span class="barber-section__label">Sfeerimpressie</span>
                <h2>Onze shop</h2>
            </div>
            <div class="barber-gallery__grid">
                <div class="barber-gallery__item barber-gallery__item--wide">
                    <img src="https://images.unsplash.com/photo-1621605815971-fbc98d665033?w=900&h=500&fit=crop" alt="Barbershop">
                    <div class="barber-gallery__caption">De shop</div>
                </div>
                <div class="barber-gallery__item">
                    <img src="https://images.unsplash.com/photo-1593702275687-f8b4024441f5?w=500&h=400&fit=crop" alt="Fade knippen">
                    <div class="barber-gallery__caption">Precision fade</div>
                </div>
                <div class="barber-gallery__item">
                    <img src="https://images.unsplash.com/photo-1633681199700-64a092b927a8?w=500&h=400&fit=crop" alt="Baardverzorging">
                    <div class="barber-gallery__caption">Baardverzorging</div>
                </div>
                <div class="barber-gallery__item">
                    <img src="https://images.unsplash.com/photo-1521590832167-7bcbfaa6381f?w=500&h=400&fit=crop" alt="Kapper tools">
                    <div class="barber-gallery__caption">Vakmanschap</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Contact & openingstijden --}}
    <section id="contact" class="barber-section barber-contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="barber-contact-card">
                        <span class="barber-section__label">Bezoek ons</span>
                        <h2>Contact & locatie</h2>
                        <ul class="barber-contact-list">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Adres</strong>
                                    <span>Hoofdstraat 123, 1012 AB Amsterdam</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Telefoon</strong>
                                    <span>020 - 123 4567</span>
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>E-mail</strong>
                                    <span>info@tiko.nl</span>
                                </div>
                            </li>
                            <li>
                                <i class="fab fa-instagram"></i>
                                <div>
                                    <strong>Instagram</strong>
                                    <span>@tiko.barbershop</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="barber-hours-card">
                        <h3><i class="fas fa-clock me-2"></i>Openingstijden</h3>
                        <table class="barber-hours-table">
                            <tr><td>Maandag</td><td>09:00 – 18:00</td></tr>
                            <tr><td>Dinsdag</td><td>09:00 – 18:00</td></tr>
                            <tr><td>Woensdag</td><td>09:00 – 18:00</td></tr>
                            <tr><td>Donderdag</td><td>09:00 – 20:00</td></tr>
                            <tr><td>Vrijdag</td><td>09:00 – 20:00</td></tr>
                            <tr><td>Zaterdag</td><td>08:00 – 17:00</td></tr>
                            <tr class="barber-hours-closed"><td>Zondag</td><td>Gesloten</td></tr>
                        </table>
                        <p class="barber-hours-note">
                            <i class="fas fa-info-circle me-2"></i>
                            Walk-ins zijn welkom. Voor kleurbehandelingen raden we een afspraak aan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="barber-cta">
        <div class="container text-center">
            <h2>Klaar voor een frisse look?</h2>
            <p>Kom langs in de shop of bel ons direct. Wij staan voor je klaar.</p>
            <div class="barber-cta__actions">
                <a href="tel:0201234567" class="btn btn-gold btn-lg">
                    <i class="fas fa-phone me-2"></i>020 - 123 4567
                </a>
                @auth
                    @if(Auth::user()->isEigenaar())
                        <a href="{{ route('eigenaar.dashboard') }}" class="btn btn-outline-gold btn-lg">
                            <i class="fas fa-crown me-2"></i>Eigenaar Dashboard
                        </a>
                    @elseif(Auth::user()->isManager())
                        <a href="{{ route('medewerkers.index') }}" class="btn btn-outline-gold btn-lg">
                            <i class="fas fa-users me-2"></i>Medewerkerbeheer
                        </a>
                    @elseif(Auth::user()->isKlant())
                        <a href="{{ route('klant.dashboard') }}" class="btn btn-outline-gold btn-lg">
                            <i class="fas fa-user me-2"></i>Mijn account
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="btn btn-outline-gold btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Klantaccount maken
                    </a>
                    <a href="{{ route('login') }}" class="btn btn-outline-gold btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Inloggen
                    </a>
                @endauth
            </div>
        </div>
    </section>

</div>
@endsection
