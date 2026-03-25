@extends('layouts.app')

@section('title', 'Mijn Dashboard')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-tachometer-alt me-3"></i>Welkom, {{ Auth::user()->name }}</h1>
        <p class="mb-0 mt-2 opacity-90">Uw persoonlijke dashboard</p>
    </div>
</div>

<div class="container">
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Mijn Afspraken</h5>
                    <h2 class="text-primary mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $appointments->count() }}</h2>
                    <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-primary mt-3">
                        <i class="fas fa-eye me-1"></i>Bekijken
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Mijn Facturen</h5>
                    <h2 class="text-warning mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $invoices->count() }}</h2>
                    <a href="{{ route('patient.invoices') }}" class="btn btn-sm btn-warning mt-3">
                        <i class="fas fa-eye me-1"></i>Bekijken
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Mijn Berichten</h5>
                    <h2 class="text-success mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $messages->count() }}</h2>
                    <a href="{{ route('patient.messages') }}" class="btn btn-sm btn-success mt-3">
                        <i class="fas fa-eye me-1"></i>Bekijken
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Recente Afspraken</h5>
                </div>
                <div class="card-body">
                    @if($appointments->isEmpty())
                        <p class="text-muted mb-0">Geen afspraken gevonden.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($appointments->take(3) as $appointment)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $appointment->appointment_date->format('d-m-Y H:i') }}</strong>
                                            <br>
                                            <small class="text-muted">Reisadviseur: {{ $appointment->dentist->name ?? 'Onbekend' }}</small>
                                        </div>
                                        <span class="badge bg-primary">{{ ucfirst($appointment->status) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('patient.appointments') }}" class="btn btn-sm btn-outline-primary">Alle afspraken bekijken</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-2"></i>Recente Facturen</h5>
                </div>
                <div class="card-body">
                    @if($invoices->isEmpty())
                        <p class="text-muted mb-0">Geen facturen gevonden.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($invoices->take(3) as $invoice)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>€{{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                                            <br>
                                            <small class="text-muted">Vervaldatum: {{ $invoice->due_date->format('d-m-Y') }}</small>
                                        </div>
                                        @if($invoice->status == 'betaald')
                                            <span class="badge bg-success">Betaald</span>
                                        @elseif($invoice->status == 'vervallen')
                                            <span class="badge bg-danger">Vervallen</span>
                                        @else
                                            <span class="badge bg-warning">Openstaand</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('patient.invoices') }}" class="btn btn-sm btn-outline-primary">Alle facturen bekijken</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

