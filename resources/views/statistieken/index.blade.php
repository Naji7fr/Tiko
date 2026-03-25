@extends('layouts.app')

@section('title', 'Overzicht Statistieken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-chart-bar me-3"></i>Overzicht Statistieken</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk alle belangrijke statistieken en metrics</p>
    </div>
</div>

<div class="container">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Totaal Klanten</h5>
                    <h2 class="text-primary mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $totalPatients }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-user-md"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Totaal Reisadviseurs</h5>
                    <h2 class="text-success mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $totalDentists }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--warning-color), #d97706); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Totaal Afspraken</h5>
                    <h2 class="text-warning mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $totalAppointments }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Totaal Facturen</h5>
                    <h2 class="mb-0" style="font-size: 2.5rem; font-weight: 700; color: #8b5cf6;">{{ $totalInvoices }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--success-color), #059669); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Betaalde Facturen</h5>
                    <h2 class="text-success mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $paidInvoices }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto" style="width: 70px; height: 70px; background: linear-gradient(135deg, var(--danger-color), #dc2626); border-radius: 16px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                    <h5 class="card-title text-muted mb-2">Vervallen Facturen</h5>
                    <h2 class="text-danger mb-0" style="font-size: 2.5rem; font-weight: 700;">{{ $overdueInvoices }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection