@extends('layouts.app')

@section('title', 'Mijn Afspraken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-check me-3"></i>Mijn Afspraken</h1>
        <p class="mb-0 mt-2 opacity-90">Bekijk en maak afspraken</p>
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
            <h2 class="mb-0" style="color: var(--text-primary);">Mijn Afspraken</h2>
            <p class="text-muted mb-0">Totaal: {{ $appointments->count() }} afspraken</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
            <i class="fas fa-plus me-2"></i>Nieuwe Afspraak
        </button>
    </div>

    @if($appointments->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Geen afspraken gevonden</h4>
                <p class="text-muted mb-4">U heeft nog geen afspraken ingepland.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="fas fa-plus me-2"></i>Maak eerste afspraak
                </button>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="list-group list-group-flush">
                    @foreach($appointments as $appointment)
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <strong>{{ $appointment->appointment_date->format('d-m-Y') }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $appointment->appointment_date->format('H:i') }}</small>
                                </div>
                                <div class="col-md-3">
                                    <i class="fas fa-user-md me-2 text-muted"></i>
                                    {{ $appointment->dentist->name ?? 'Onbekend' }}
                                </div>
                                <div class="col-md-3">
                                    @php
                                        $statusColors = [
                                            'gepland' => 'primary',
                                            'bevestigd' => 'info',
                                            'voltooid' => 'success',
                                            'geannuleerd' => 'danger'
                                        ];
                                        $statusColor = $statusColors[$appointment->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statusColor }}">{{ ucfirst($appointment->status) }}</span>
                                </div>
                                <div class="col-md-3">
                                    @if($appointment->notes)
                                        <small class="text-muted">{{ Str::limit($appointment->notes, 30) }}</small>
                                    @else
                                        <small class="text-muted fst-italic">Geen notities</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAppointmentModalLabel">Nieuwe Afspraak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('patient.appointments.create') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="dentist_id" class="form-label">Reisadviseur <span class="text-danger">*</span></label>
                        <select class="form-select" id="dentist_id" name="dentist_id" required>
                            <option value="">Selecteer een reisadviseur</option>
                            @foreach($dentists as $dentist)
                                <option value="{{ $dentist->id }}">{{ $dentist->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="appointment_date" class="form-label">Datum & Tijd <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notities</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Optionele notities..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-primary">Afspraak Maken</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

