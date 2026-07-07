{{-- klant.view — Klantgegevens bewerken (eigenaar) --}}
@extends('layouts.app')

@section('title', 'Klant bewerken')

@section('content')
<div class="page-header">
    <div class="container">
        <h1><i class="fas fa-user-edit me-3"></i>Klant bewerken</h1>
        <p class="mb-0 mt-2 opacity-90">Werk de klantgegevens bij</p>
    </div>
</div>

<div class="container">
    @include('eigenaar.partials.alerts')

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('eigenaar.klanten.update', $klant->id) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="voornaam" class="form-label">Voornaam</label>
                        <input type="text" class="form-control @error('voornaam') is-invalid @enderror" id="voornaam" name="voornaam"
                               value="{{ $form['voornaam'] }}" required maxlength="50" pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-\.'']+">
                        @error('voornaam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="achternaam" class="form-label">Achternaam</label>
                        <input type="text" class="form-control @error('achternaam') is-invalid @enderror" id="achternaam" name="achternaam"
                               value="{{ $form['achternaam'] }}" required maxlength="50" pattern="[A-Za-zÀ-ÖØ-öø-ÿ\s\-\.'']+">
                        @error('achternaam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                               value="{{ $form['email'] }}" required maxlength="254">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="telefoon" class="form-label">Telefoon</label>
                        <input type="text" class="form-control @error('telefoon') is-invalid @enderror" id="telefoon" name="telefoon"
                               value="{{ $form['telefoon'] }}" maxlength="25" pattern="^(?:\+31|0031|0)[1-9][0-9]{8}$">
                        @error('telefoon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="straat" class="form-label">Straat</label>
                        <input type="text" class="form-control @error('straat') is-invalid @enderror" id="straat" name="straat"
                               value="{{ $form['straat'] }}" maxlength="100">
                        @error('straat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="huisnummer" class="form-label">Huisnummer</label>
                        <input type="text" class="form-control @error('huisnummer') is-invalid @enderror" id="huisnummer" name="huisnummer"
                               value="{{ $form['huisnummer'] }}" maxlength="10">
                        @error('huisnummer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="postcode" class="form-label">Postcode</label>
                        <input type="text" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode"
                               value="{{ $form['postcode'] }}" maxlength="10" pattern="^[1-9][0-9]{3}\s?[A-Za-z]{2}$">
                        @error('postcode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="plaats" class="form-label">Plaats</label>
                        <input type="text" class="form-control @error('plaats') is-invalid @enderror" id="plaats" name="plaats"
                               value="{{ $form['plaats'] }}" maxlength="50">
                        @error('plaats')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="land" class="form-label">Land</label>
                        <input type="text" class="form-control @error('land') is-invalid @enderror" id="land" name="land"
                               value="{{ $form['land'] }}" maxlength="50">
                        @error('land')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Opslaan
                    </button>
                    <a href="{{ route('eigenaar.klanten.index') }}" class="btn btn-secondary">Annuleren</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
