@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Medewerker Toevoegen</h1>
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('medewerkers.store') }}">
            @csrf
            <div class="mb-3">
                <label for="naam" class="form-label">Naam</label>
                <input type="text" class="form-control" id="naam" name="naam" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="">Selecteer status</option>
                    <option value="Actief">Actief</option>
                    <option value="Inactief">Inactief</option>
                    <option value="Op proef">Op proef</option>
                    <option value="Afwezig">Afwezig</option>
                    <option value="Gepauzeerd">Gepauzeerd</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="">Selecteer type</option>
                    <option value="Admin">Admin</option>
                    <option value="Financieel Medewerker">Financieel Medewerker</option>
                    <option value="Reisadviseur">Reisadviseur</option>
                    <option value="Boekingsagent">Boekingsagent</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Medewerker toevoegen</button>
        </form>
    </div>
@endsection
