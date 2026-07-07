@extends('layouts.app')

@section('title', 'Klant Overzicht bewerken')

@section('content')
<div class="container py-5" style="padding-top: 6rem;">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
                        <div>
                            <span class="text-uppercase text-muted small fw-semibold">Update details</span>
                            <h1 class="h3 mb-2">Wijzig je klantgegevens</h1>
                            <p class="text-muted mb-0">Werk hier je naam, e-mail, telefoon en adres bij.</p>
                        </div>
                        <a href="{{ route('klant.overzicht') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Terug naar details
                        </a>
                    </div>

                    <form method="POST" action="{{ route('klant.overzicht.update') }}" class="row g-4">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6">
                            <label for="voornaam" class="form-label">First name</label>
                            <input type="text" id="voornaam" name="voornaam" value="{{ $form['voornaam'] }}" class="form-control @error('voornaam') is-invalid @enderror">
                            @error('voornaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="achternaam" class="form-label">Last name</label>
                            <input type="text" id="achternaam" name="achternaam" value="{{ $form['achternaam'] }}" class="form-control @error('achternaam') is-invalid @enderror">
                            @error('achternaam')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" value="{{ $form['email'] }}" class="form-control @error('email') is-invalid @enderror">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="telefoon" class="form-label">Telefoon</label>
                            <input type="text" id="telefoon" name="telefoon" value="{{ $form['telefoon'] }}" class="form-control @error('telefoon') is-invalid @enderror">
                            @error('telefoon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <h2 class="h5 mb-0">Adres</h2>
                            <p class="text-muted small mb-0">Laat leeg als je geen adres wilt opslaan.</p>
                        </div>

                        <div class="col-12">
                            <label for="straat" class="form-label">Straat</label>
                            <input type="text" id="straat" name="straat" value="{{ $form['straat'] }}" class="form-control @error('straat') is-invalid @enderror">
                            @error('straat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="huisnummer" class="form-label">Huisnummer</label>
                            <input type="text" id="huisnummer" name="huisnummer" value="{{ $form['huisnummer'] }}" class="form-control @error('huisnummer') is-invalid @enderror">
                            @error('huisnummer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="postcode" class="form-label">Postcode</label>
                            <input type="text" id="postcode" name="postcode" value="{{ $form['postcode'] }}" class="form-control @error('postcode') is-invalid @enderror">
                            @error('postcode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label for="plaats" class="form-label">Plaats</label>
                            <input type="text" id="plaats" name="plaats" value="{{ $form['plaats'] }}" class="form-control @error('plaats') is-invalid @enderror">
                            @error('plaats')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label for="land" class="form-label">Land</label>
                            <input type="text" id="land" name="land" value="{{ $form['land'] }}" class="form-control @error('land') is-invalid @enderror">
                            @error('land')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12 d-flex flex-wrap gap-2 pt-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check me-2"></i>Save changes
                            </button>
                            <a href="{{ route('klant.overzicht') }}" class="btn btn-outline-secondary">
                                Annuleren
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection