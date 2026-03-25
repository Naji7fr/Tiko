@extends('layouts.app')

@section('title', 'Account Bewerken')

@section('content')
<div class="container">
    <h1>Account Bewerken</h1>
    <form action="{{ route('accounts.update', $account) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Gebruikersnaam</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name', $account->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="voornaam" class="form-label">Voornaam</label>
                <input type="text" class="form-control @error('voornaam') is-invalid @enderror"
                       id="voornaam" name="voornaam" value="{{ old('voornaam', $account->voornaam) }}">
                @error('voornaam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label for="achternaam" class="form-label">Achternaam</label>
                <input type="text" class="form-control @error('achternaam') is-invalid @enderror"
                       id="achternaam" name="achternaam" value="{{ old('achternaam', $account->achternaam) }}">
                @error('achternaam')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $account->email) }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Wachtwoord (laat leeg om niet te wijzigen)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Rol</label>
            <select class="form-control" id="role" name="role" required>
                <option value="klant" {{ old('role', $account->role) == 'klant' ? 'selected' : '' }}>Klant</option>
                <option value="reisadviseur" {{ old('role', $account->role) == 'reisadviseur' ? 'selected' : '' }}>Reisadviseur</option>
                <option value="financieel_medewerker" {{ old('role', $account->role) == 'financieel_medewerker' ? 'selected' : '' }}>Financieel Medewerker</option>
                <option value="manager" {{ old('role', $account->role) == 'manager' ? 'selected' : '' }}>Manager</option>
                <option value="admin" {{ old('role', $account->role) == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-control" id="status" name="status" required>
                <option value="Actief" {{ old('status', $account->status) == 'Actief' ? 'selected' : '' }}>Actief</option>
                <option value="Inactief" {{ old('status', $account->status) == 'Inactief' ? 'selected' : '' }}>Inactief</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Account Bijwerken</button>
        <a href="{{ route('accounts.index') }}" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
@endsection