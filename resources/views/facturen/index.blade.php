@extends('layouts.app')

@section('title', 'Overzicht Facturen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-file-invoice-dollar me-3"></i>Overzicht Facturen</h1>
        <p class="mb-0 mt-2 opacity-90">Beheer alle facturen en betalingen</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0" style="color: var(--text-primary);">Alle Facturen</h2>
            <p class="text-muted mb-0">Totaal: {{ $invoices->count() }} facturen</p>
        </div>
        <a href="{{ route('facturen.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nieuwe Factuur
        </a>
    </div>

    @php
        $totalAmount = $invoices->sum('amount');
        $paidAmount = $invoices->where('status', 'betaald')->sum('amount');
        $pendingAmount = $invoices->where('status', 'openstaand')->sum('amount');
        $overdueCount = $invoices->where('status', 'vervallen')->count();
    @endphp

    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Totaal Facturen</h6>
                            <h4 class="mb-0">{{ $invoices->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Betaald</h6>
                            <h4 class="mb-0 text-success">€{{ number_format($paidAmount, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-clock"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Openstaand</h6>
                            <h4 class="mb-0 text-warning">€{{ number_format($pendingAmount, 2) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avatar-circle" style="width: 50px; height: 50px; background: linear-gradient(135deg, var(--danger-color), #dc2626); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Vervallen</h6>
                            <h4 class="mb-0 text-danger">{{ $overdueCount }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($invoices->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen facturen gevonden</h4>
                <p class="text-muted mb-4">Er zijn momenteel geen facturen beschikbaar in het systeem.</p>
                <a href="{{ route('facturen.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Voeg eerste factuur toe
                </a>
            </div>
        </div>
    @else
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-2"></i>Factuur Nr.</th>
                            <th><i class="fas fa-user me-2"></i>Klant</th>
                            <th><i class="fas fa-euro-sign me-2"></i>Bedrag</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Vervaldatum</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-align-left me-2"></i>Beschrijving</th>
                            <th><i class="fas fa-cogs me-2"></i>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td data-label="Factuur Nr.">
                                    <span class="badge bg-secondary fw-semibold">
                                        <i class="fas fa-hashtag me-1"></i>{{ $invoice->factuurnummer }}
                                    </span>
                                </td>
                                <td data-label="Klant">
                                    <div class="d-flex align-items-center">
                                        @if($invoice->patient)
                                            <div class="avatar-circle me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                {{ strtoupper(substr($invoice->patient->name ?? '?', 0, 1)) }}
                                            </div>
                                            <strong>{{ $invoice->patient->name ?? 'Onbekend' }}</strong>
                                        @else
                                            <span class="text-muted">Geen klant</span>
                                        @endif
                                    </div>
                                </td>
                                <td data-label="Bedrag">
                                    <strong class="text-primary">€{{ number_format($invoice->amount, 2, ',', '.') }}</strong>
                                </td>
                                <td data-label="Vervaldatum">
                                    <i class="fas fa-calendar me-2 text-muted"></i>
                                    {{ $invoice->due_date->format('d-m-Y') }}
                                </td>
                                <td data-label="Status">
                                    @if($invoice->status == 'betaald')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Betaald
                                        </span>
                                    @elseif($invoice->status == 'vervallen')
                                        <span class="badge bg-danger">
                                            <i class="fas fa-exclamation-triangle me-1"></i>Vervallen
                                        </span>
                                    @else
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>Openstaand
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Beschrijving">
                                    <span class="text-muted">{{ Str::limit($invoice->description, 50) }}</span>
                                </td>
                                <td data-label="Acties">
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('facturen.edit', $invoice->id) }}" class="btn btn-sm btn-warning" title="Bewerken">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('facturen.destroy', $invoice->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Weet je zeker dat je deze factuur wilt verwijderen?');">
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