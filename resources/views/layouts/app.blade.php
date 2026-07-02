<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tiko')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @stack('styles')
</head>
<body class="@yield('body-class')">
    <nav class="navbar navbar-expand-lg fixed-top @if(request()->routeIs('home')) navbar-dark barber-nav @else navbar-light @endif">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-scissors"></i>
                <span>Tiko</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Menu openen">
                <span class="navbar-toggler-bars" aria-hidden="true">
                    <span></span>
                    <span></span>
                    <span></span>
                </span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    @if(request()->routeIs('home'))
                        <li class="nav-item">
                            <a class="nav-link" href="#diensten">Diensten</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#over-ons">Over ons</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    @endif

                    @auth
                        @if(Auth::user()->isEigenaar())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('eigenaar.*') ? 'active' : '' }}" href="{{ route('eigenaar.dashboard') }}">
                                    <i class="fas fa-crown me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i>Beheer
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('medewerkers.index') }}"><i class="fas fa-users me-2"></i>Medewerkers</a></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.module', 'klanten') }}"><i class="fas fa-user-friends me-2"></i>Klanten</a></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.module', 'afspraken') }}"><i class="fas fa-calendar me-2"></i>Afspraken</a></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.module', 'behandelingen') }}"><i class="fas fa-cut me-2"></i>Behandelingen</a></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.module', 'producten') }}"><i class="fas fa-box me-2"></i>Producten</a></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.module', 'bestellingen') }}"><i class="fas fa-shopping-cart me-2"></i>Bestellingen</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('eigenaar.rapportages') }}"><i class="fas fa-chart-bar me-2"></i>Rapportages</a></li>
                                </ul>
                            </li>
                        @elseif(Auth::user()->isManager())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('medewerkers.*') ? 'active' : '' }}" href="{{ route('medewerkers.index') }}">
                                    <i class="fas fa-users me-1"></i>Medewerkers
                                </a>
                            </li>
                        @elseif(Auth::user()->isKlant())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('klant.*') ? 'active' : '' }}" href="{{ route('klant.dashboard') }}">
                                    <i class="fas fa-user me-1"></i>Mijn account
                                </a>
                            </li>
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout.get') }}">
                                        <i class="fas fa-sign-out-alt me-2"></i>Uitloggen
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Registreren
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Inloggen
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-wrapper @if(request()->routeIs('home')) content-wrapper--home @endif">
        @yield('content')
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-scissors me-2"></i>Tiko Barbershop</h5>
                    <p>Sharp cuts. Clean fades. Real craft — midden in Amsterdam.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-clock me-2"></i>Openingstijden</h5>
                    <p>Ma–Vr: 09:00 – 18:00<br>Do–Vr: tot 20:00<br>Za: 08:00 – 17:00</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-phone me-2"></i>Contact</h5>
                    <p>
                        <i class="fas fa-phone-alt me-2"></i>020 - 123 4567<br>
                        <i class="fas fa-envelope me-2"></i>info@tiko.nl<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Hoofdstraat 123, Amsterdam
                    </p>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">&copy; {{ date('Y') }} Tiko. Alle rechten voorbehouden.</p>
        </div>
    </footer>
    @include('partials.delete-confirm-modal')
    @include('partials.delete-blocked-modal')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
