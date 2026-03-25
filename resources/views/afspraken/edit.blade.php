@extends('layouts.app')

@section('title', 'Afspraak Bewerken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-calendar-plus me-3"></i>Afspraak Bewerken</h1>
        <p class="mb-0 mt-2 opacity-90">Bewerk de details van deze afspraak</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('afspraken.update', $appointment->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">
                                <i class="fas fa-user me-2"></i>Klant <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">Selecteer een klant</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id', $appointment->patient_id) == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="dentist_id" class="form-label">
                                <i class="fas fa-user-tie me-2"></i>Reisadviseur <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('dentist_id') is-invalid @enderror" id="dentist_id" name="dentist_id" required>
                                <option value="">Selecteer een reisadviseur</option>
                                @foreach($dentists as $dentist)
                                    <option value="{{ $dentist->id }}" {{ old('dentist_id', $appointment->dentist_id) == $dentist->id ? 'selected' : '' }}>
                                        {{ $dentist->name }} ({{ $dentist->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dentist_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="appointment_date" class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Datum & Tijd <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control @error('appointment_date') is-invalid @enderror" 
                                   id="appointment_date" name="appointment_date" 
                                   value="{{ old('appointment_date', $appointment->appointment_date->format('Y-m-d\TH:i')) }}" required>
                            @error('appointment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-2"></i>Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="gepland" {{ old('status', $appointment->status) == 'gepland' ? 'selected' : '' }}>Gepland</option>
                                <option value="bevestigd" {{ old('status', $appointment->status) == 'bevestigd' ? 'selected' : '' }}>Bevestigd</option>
                                <option value="voltooid" {{ old('status', $appointment->status) == 'voltooid' ? 'selected' : '' }}>Voltooid</option>
                                <option value="geannuleerd" {{ old('status', $appointment->status) == 'geannuleerd' ? 'selected' : '' }}>Geannuleerd</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="form-label">
                                <i class="fas fa-sticky-note me-2"></i>Notities
                            </label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" 
                                      placeholder="Optionele notities over de afspraak...">{{ old('notes', $appointment->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Afspraak Bijwerken
                            </button>
                            <a href="{{ route('afspraken.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuleren
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection