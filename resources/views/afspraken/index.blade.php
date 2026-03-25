@extends('layouts.app')

@section('title', 'Overzicht Afspraken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check me-3"></i>Overzicht Afspraken</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle afspraken en planningen</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Afspraken</h2>
            <p class="text-muted mb-0">Totaal: {{ $appointments->count() }} afspraken</p>
        </div>
        <a href="{{ route('afspraken.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nieuwe Afspraak
        </a>
    </div>

    @php
        $upcomingCount = $appointments->where('appointment_date', '>', now())->count();
        $todayCount = $appointments->filter(function($apt) {
            return $apt->appointment_date->isToday();
        })->count();
        $completedCount = $appointments->where('status', 'voltooid')->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Vandaag</h6>
                            <h4 class="mb-0 text-primary">{{ $todayCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Aankomend</h6>
                            <h4 class="mb-0 text-warning">{{ $upcomingCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Voltooid</h6>
                            <h4 class="mb-0 text-success">{{ $completedCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($appointments->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen afspraken gevonden</h4>
                <p class="text-muted mb-4">Er zijn momenteel geen afspraken ingepland in het systeem.</p>
                <a href="{{ route('afspraken.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Voeg eerste afspraak toe
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Klant</th>
                            <th><i class="fas fa-user-tie me-2"></i>Reisadviseur</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Datum & Tijd</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-sticky-note me-2"></i>Notities</th>
                            <th><i class="fas fa-cogs me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments->sortBy('appointment_date') as $appointment)
                            <tr class="{{ $appointment->appointment_date->isToday() ? 'table-info' : '' }}">
                                <td data-label="Klant">
                                    <div class="d-flex align-items-center">
                                        @if($appointment->patient)
                                            <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                {{ strtoupper(substr($appointment->patient->name ?? '?', 0, 1)) }}
                                            </div>
                                            <strong>{{ $appointment->patient->name ?? 'Onbekend' }}</strong>
                                        @else
                                            <span class="text-muted">Geen klant</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Reisadviseur">
                                    <div class="d-flex align-items-center">
                                        @if($appointment->dentist)
                                            <div class="avatar-circle me-2" style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 0.75rem;">
                                                {{ strtoupper(substr($appointment->dentist->name ?? '?', 0, 1)) }}
                                            </div>
                                            {{ $appointment->dentist->name ?? 'Onbekend' }}
                                        @else
                                            <span class="text-muted">Geen reisadviseur</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Datum & Tijd">
                                    <div>
                                        <i class="fas fa-calendar me-2 text-muted"></i>
                                        <strong>{{ $appointment->appointment_date->format('d-m-Y') }}</strong>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>{{ $appointment->appointment_date->format('H:i') }}
                                    </small>
                                </td>
                                <td data-label="Status">
                                    @php
                                        $statusColors = [
                                            'gepland' => 'primary',
                                            'bevestigd' => 'info',
                                            'voltooid' => 'success',
                                            'geannuleerd' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$appointment->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">
                                        <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i>{{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                                <td data-label="Notities">
                                    @if($appointment->notes)
                                        <span class="text-muted">{{ Str::limit($appointment->notes, 40) }}</span>
                                    @else
                                        <span class="text-muted fst-italic">Geen notities</span>
                                    @endif
                                </td>
                                <td data-label="Acties">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('afspraken.edit', $appointment->id) }}" class="btn btn-sm btn-warning" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('afspraken.destroy', $appointment->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet je zeker dat je deze afspraak wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Verwijderen">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection