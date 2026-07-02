{{-- eigenaar.view — Module placeholder --}}
@extends('layouts.app')

@section('title', $titel)

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-tools me-3"></i>{{ $titel }}</h1>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')

    <div class="mb-3">
        <a href="{{ route('eigenaar.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Terug naar dashboard
        </a>
    </div>

    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-hammer fa-3x text-muted mb-3"></i>
            <h4>{{ $titel }}beheer komt binnenkort</h4>
            <p class="text-muted mb-4">
                Deze module wordt nog gebouwd. Via het eigenaar-dashboard heb je straks
                volledige toegang tot alle {{ strtolower($titel) }}gegevens.
            </p>
            <a href="{{ route('eigenaar.dashboard') }}" class="btn btn-primary">Naar dashboard</a>
        </div>
    </div>
</div>
@endsection
