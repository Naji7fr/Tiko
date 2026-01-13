@extends('layouts.app')

@section('title', 'Account Bewerken')

@section('content')
<div class="container">
    <h1>Account Bewerken</h1>
    <form action="{{ route('accounts.update', $account) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Naam</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $account->name) }}" required>
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
                <option value="patient" {{ old('role', $account->role) == 'patient' ? 'selected' : '' }}>Patiënt</option>
                <option value="tandarts" {{ old('role', $account->role) == 'tandarts' ? 'selected' : '' }}>Tandarts</option>
                <option value="praktijkmanager" {{ old('role', $account->role) == 'praktijkmanager' ? 'selected' : '' }}>Praktijkmanager</option>
                <option value="assistent" {{ old('role', $account->role) == 'assistent' ? 'selected' : '' }}>Assistent</option>
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