<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BNG')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-bed"></i>
                <span>BNG</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            <i class="fas fa-home me-1"></i>Home
                        </a>
                    </li>
                    
                    @auth
                        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                            {{-- Admin/Praktijkmanager Menu --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('medewerkers.*') ? 'active' : '' }}" href="{{ route('medewerkers.index') }}">
                                    <i class="fas fa-users me-1"></i>Medewerkers
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('accounts.*') ? 'active' : '' }}" href="{{ route('accounts.index') }}">
                                    <i class="fas fa-user-shield me-1"></i>Accounts
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('berichten.*') ? 'active' : '' }}" href="{{ route('berichten.index') }}">
                                    <i class="fas fa-envelope me-1"></i>Berichten
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('facturen.*') ? 'active' : '' }}" href="{{ route('facturen.index') }}">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>Facturen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('statistieken.*') ? 'active' : '' }}" href="{{ route('statistieken.index') }}">
                                    <i class="fas fa-chart-bar me-1"></i>Statistieken
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('afspraken.*') ? 'active' : '' }}" href="{{ route('afspraken.index') }}">
                                    <i class="fas fa-calendar-check me-1"></i>Afspraken
                                </a>
                            </li>
                        @else
                            {{-- Patient Menu --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patient.appointments') ? 'active' : '' }}" href="{{ route('patient.appointments') }}">
                                    <i class="fas fa-calendar-check me-1"></i>Mijn Afspraken
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patient.invoices') ? 'active' : '' }}" href="{{ route('patient.invoices') }}">
                                    <i class="fas fa-file-invoice-dollar me-1"></i>Mijn Facturen
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('patient.messages') ? 'active' : '' }}" href="{{ route('patient.messages') }}">
                                    <i class="fas fa-envelope me-1"></i>Mijn Berichten
                                </a>
                            </li>
                        @endif
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Profiel</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout.get') }}">
                                        <i class="fas fa-sign-out-alt me-2"></i>Uitloggen
                                    </a>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Inloggen
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Registreren
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="content-wrapper">
        @yield('content')
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-bed me-2"></i>BNG</h5>
                    <p>Uw vertrouwde boekingsplatform. Vergelijk accommodaties en vind de beste aanbiedingen, net als Booking.com.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-phone me-2"></i>Contact</h5>
                    <p>
                        <i class="fas fa-phone-alt me-2"></i>Telefoon: 012-3456789<br>
                        <i class="fas fa-envelope me-2"></i>Email: info@bng.nl<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Adres: Hoofdstraat 123, Amsterdam
                    </p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-clock me-2"></i>Openingstijden</h5>
                    <p>
                        Maandag - Vrijdag: 8:00 - 17:00<br>
                        Zaterdag: Gesloten<br>
                        Zondag: Gesloten
                    </p>
                </div>
            </div>
            <hr>
            <p class="text-center mb-0">&copy; {{ date('Y') }} BNG. Alle rechten voorbehouden.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
