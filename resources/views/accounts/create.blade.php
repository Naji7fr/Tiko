@extends('layouts.app')

@section('title', 'Nieuw Account toevoegen')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-plus me-3"></i>Nieuw Account toevoegen</h1>
        <p class="mb-0 mt-2 opacity-90">Voeg een nieuw account toe aan het systeem</p>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body p-4">
                    <form action="{{ route('accounts.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Naam <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Volledige naam..." required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" 
                                   value="{{ old('email') }}" 
                                   placeholder="email@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2"></i>Wachtwoord <span class="text-danger">*</span>
                            </label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                   id="password" name="password" 
                                   placeholder="Minimaal 8 karakters" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">
                                <i class="fas fa-user-tag me-2"></i>Rol <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="">Selecteer een rol</option>
                                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patiënt</option>
                                <option value="tandarts" {{ old('role') == 'tandarts' ? 'selected' : '' }}>Tandarts</option>
                                <option value="praktijkmanager" {{ old('role') == 'praktijkmanager' ? 'selected' : '' }}>Praktijkmanager</option>
                                <option value="assistent" {{ old('role') == 'assistent' ? 'selected' : '' }}>Assistent</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle me-2"></i>Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="">Selecteer status</option>
                                <option value="Actief" {{ old('status') == 'Actief' ? 'selected' : '' }}>Actief</option>
                                <option value="Inactief" {{ old('status') == 'Inactief' ? 'selected' : '' }}>Inactief</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Account Toevoegen
                            </button>
                            <a href="{{ route('accounts.index') }}" class="btn btn-secondary">
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