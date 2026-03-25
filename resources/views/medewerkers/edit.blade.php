@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Medewerker Bewerken</h1>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('medewerkers.update', $medewerker->id) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="naam" class="form-label">Naam</label>
                <input type="text" class="form-control" id="naam" name="naam" value="{{ old('naam', $medewerker->naam) }}" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $medewerker->email) }}" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="">Selecteer status</option>
                    <option value="Actief" {{ old('status', $medewerker->status) == 'Actief' ? 'selected' : '' }}>Actief</option>
                    <option value="Inactief" {{ old('status', $medewerker->status) == 'Inactief' ? 'selected' : '' }}>Inactief</option>
                    <option value="Op proef" {{ old('status', $medewerker->status) == 'Op proef' ? 'selected' : '' }}>Op proef</option>
                    <option value="Afwezig" {{ old('status', $medewerker->status) == 'Afwezig' ? 'selected' : '' }}>Afwezig</option>
                    <option value="Gepauzeerd" {{ old('status', $medewerker->status) == 'Gepauzeerd' ? 'selected' : '' }}>Gepauzeerd</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Selecteer type</option>
                    <option value="Admin" {{ old('type', $medewerker->type) == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option value="Financieel Medewerker" {{ old('type', $medewerker->type) == 'Financieel Medewerker' ? 'selected' : '' }}>Financieel Medewerker</option>
                    <option value="Reisadviseur" {{ old('type', $medewerker->type) == 'Reisadviseur' ? 'selected' : '' }}>Reisadviseur</option>
                    <option value="Boekingsagent" {{ old('type', $medewerker->type) == 'Boekingsagent' ? 'selected' : '' }}>Boekingsagent</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Opslaan</button>
            <a href="{{ route('medewerkers.index') }}" class="btn btn-secondary">Annuleren</a>
        </form>
    </div>
@endsection
