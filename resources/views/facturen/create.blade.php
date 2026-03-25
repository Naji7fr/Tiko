@extends('layouts.app')

@section('title', 'Nieuwe Factuur')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-file-invoice-dollar me-3"></i>Nieuwe Factuur</h1>
        <p class="mb-0 mt-2 opacity-90">Maak een nieuwe factuur aan</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('facturen.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="factuurnummer" class="form-label">
                                <i class="fas fa-hashtag me-2"></i>Factuur Nummer <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('factuurnummer') is-invalid @enderror"
                                   id="factuurnummer" name="factuurnummer"
                                   value="{{ old('factuurnummer') }}"
                                   placeholder="bijv. FACT-2026-001" required>
                            @error('factuurnummer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="patient_id" class="form-label">
                                <i class="fas fa-user me-2"></i>Klant <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('patient_id') is-invalid @enderror" id="patient_id" name="patient_id" required>
                                <option value="">Selecteer een klant</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                        {{ $patient->name }} ({{ $patient->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-euro-sign me-2"></i>Bedrag <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" step="0.01" min="0.01" 
                                       class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" 
                                       value="{{ old('amount') }}" 
                                       placeholder="0.00" required>
                                @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Vervaldatum <span class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" 
                                   value="{{ old('due_date') }}" required>
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-2"></i>Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="openstaand" {{ old('status') == 'openstaand' ? 'selected' : '' }}>Openstaand</option>
                                <option value="betaald" {{ old('status') == 'betaald' ? 'selected' : '' }}>Betaald</option>
                                <option value="vervallen" {{ old('status') == 'vervallen' ? 'selected' : '' }}>Vervallen</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-2"></i>Beschrijving
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Beschrijving van de factuur...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Factuur Opslaan
                            </button>
                            <a href="{{ route('facturen.index') }}" class="btn btn-secondary">
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

