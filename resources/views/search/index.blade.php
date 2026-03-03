@extends('layouts.app')

@section('title', 'Zoekresultaten - BNG')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-search me-3"></i>Zoekresultaten</h1>
        <p class="mb-0 mt-2 opacity-90">{{ $typeLabel }} — {{ $query ?: ($to ?? $from ?? 'uw zoekopdracht') }}</p>
    </div>
</div>

<div class="container">
    {{-- Search summary --}}
    <div class="card mb-4">
        <div class="card-body">
            <h6 class="text-muted mb-2">Uw zoekopdracht</h6>
            <p class="mb-0">
                @if($type === 'stays' || $type === 'flight_hotel')
                    <strong>Bestemming:</strong> {{ $query ?: '—' }}
                    @if($checkIn || $checkOut)
                        · <strong>Periode:</strong> {{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d-m-Y') : '—' }} — {{ $checkOut ? \Carbon\Carbon::parse($checkOut)->format('d-m-Y') : '—' }}
                    @endif
                    @if(isset($adults))
                        · <strong>{{ $adults }} volw.</strong> @if($children) · {{ $children }} kind. @endif · <strong>{{ $rooms }} kamer(s)</strong>
                    @endif
                    @if(!empty($addFlights))
                        · <span class="badge bg-info">Vluchten toegevoegd</span>
                    @endif
                @elseif($type === 'flights')
                    <strong>Van:</strong> {{ $from ?: '—' }} → <strong>Naar:</strong> {{ $to ?: '—' }}
                    @if($flightDate)
                        · <strong>Datum:</strong> {{ \Carbon\Carbon::parse($flightDate)->format('d-m-Y') }}
                    @endif
                @elseif($type === 'car')
                    <strong>Locatie:</strong> {{ $query ?: '—' }}
                    @if($pickupDate || $returnDate)
                        · <strong>Ophalen:</strong> {{ $pickupDate ? \Carbon\Carbon::parse($pickupDate)->format('d-m-Y') : '—' }} — <strong>Inleveren:</strong> {{ $returnDate ? \Carbon\Carbon::parse($returnDate)->format('d-m-Y') : '—' }}
                    @endif
                @endif
            </p>
        </div>
    </div>

    {{-- Results --}}
    @if($type === 'stays' || $type === 'flight_hotel')
        <h5 class="mb-3">{{ count($results) }} accommodaties gevonden</h5>
        <div class="row g-4">
            @foreach($results as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $item['image'] }}" class="card-img-top" alt="{{ $item['name'] }}" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-secondary mb-2">{{ $item['type'] }}</span>
                            <h6 class="card-title">{{ $item['name'] }}</h6>
                            <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt me-1"></i>{{ $item['location'] }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">€ {{ $item['price'] }} <small class="text-muted">/ nacht</small></span>
                                <span class="badge bg-success">{{ $item['rating'] }} · {{ $item['reviews'] }} beoordelingen</span>
                            </div>
                            <a href="{{ route('home') }}#search" class="btn btn-primary btn-sm mt-3 w-100">Bekijken & boeken</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($type === 'flights')
        <h5 class="mb-3">{{ count($results) }} vluchten gevonden</h5>
        <div class="list-group">
            @foreach($results as $item)
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <strong>{{ $item['airline'] }}</strong>
                            <span class="text-muted ms-2">{{ $item['from'] }} → {{ $item['to'] }}</span>
                        </div>
                        <div class="text-muted small">
                            {{ $item['departure'] }} – {{ $item['arrival'] }} ({{ $item['duration'] }}) · {{ $item['stops'] }}
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="fw-bold text-primary">€ {{ $item['price'] }}</span>
                            <a href="{{ route('home') }}#search" class="btn btn-primary btn-sm">Selecteer</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @elseif($type === 'car')
        <h5 class="mb-3">{{ count($results) }} auto's gevonden</h5>
        <div class="row g-4">
            @foreach($results as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <img src="{{ $item['image'] }}" class="card-img-top" alt="{{ $item['name'] }}" style="height: 160px; object-fit: cover;">
                        <div class="card-body">
                            <h6 class="card-title">{{ $item['name'] }}</h6>
                            <p class="text-muted small mb-2">{{ $item['supplier'] }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-primary fw-bold">€ {{ $item['price'] }} <small class="text-muted">/ dag</small></span>
                                <a href="{{ route('home') }}#search" class="btn btn-primary btn-sm">Boeken</a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">Geen zoektype geselecteerd. <a href="{{ route('home') }}#search">Start een nieuwe zoekopdracht</a>.</p>
            </div>
        </div>
    @endif

    <div class="mt-4 text-center">
        <a href="{{ route('home') }}#search" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i>Nieuwe zoekopdracht
        </a>
    </div>
</div>
@endsection
